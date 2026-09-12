<?php
/**
 * includes/generate_quiz.php - Quiz Generation with Database
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/api_helper.php';
require_once __DIR__ . '/../config/database.php';

function generateQuizQuestions($topic, $numQuestions = 5, $difficulty = 'medium') {
    $prompt = "Create $numQuestions multiple choice questions about $topic.
Return ONLY valid JSON. No markdown. No explanations.
Use this EXACT format:
{\"questions\":[{\"question\":\"Q1?\",\"options\":[\"A. 1\",\"B. 2\",\"C. 3\",\"D. 4\"],\"correct_answer\":\"A\"}]}";

    $maxAttempts = 2;
    $attempt = 0;
    $content = '';
    $quizData = null;

    while ($attempt < $maxAttempts) {
        $result = callAI($prompt, null, 3000);

        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error']];
        }

        $content = trim($result['content']);
        file_put_contents(__DIR__ . '/../debug_response.txt', $content);

        $content = preg_replace('/```json\s*/i', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        $content = trim($content);

        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $content = $matches[0];
        }

        $content = preg_replace('/\n/', ' ', $content);
        $content = preg_replace('/\r/', ' ', $content);
        $content = preg_replace('/\t/', ' ', $content);
        $content = stripslashes($content);
        $content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
        $content = preg_replace('/,\s*}/', '}', $content);
        $content = preg_replace('/,\s*]/', ']', $content);

        $openBraces = substr_count($content, '{');
        $closeBraces = substr_count($content, '}');
        $openBrackets = substr_count($content, '[');
        $closeBrackets = substr_count($content, ']');

        while ($openBrackets > $closeBrackets) {
            $content .= ']';
            $closeBrackets++;
        }
        while ($openBraces > $closeBraces) {
            $content .= '}';
            $closeBraces++;
        }

        $quizData = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
            break;
        }

        $attempt++;
        if ($attempt < $maxAttempts) {
            sleep(1);
        }
    }

    if (!isset($quizData['questions']) || !is_array($quizData['questions'])) {
        return [
            'success' => false,
            'error' => 'JSON Error: ' . json_last_error_msg(),
            'raw_response' => $content
        ];
    }

    return [
        'success' => true,
        'questions' => $quizData['questions'],
        'topic' => $topic
    ];
}

function createQuiz($topic, $numQuestions = 5, $difficulty = 'medium', $userId = null, $category = 'General') {
    global $pdo;

    $result = generateQuizQuestions($topic, $numQuestions, $difficulty);

    if (!$result['success']) {
        return $result;
    }

    if (!$userId) {
        $userId = $_SESSION['user_id'] ?? null;
    }

    if (!$userId) {
        return ['success' => false, 'error' => 'User not logged in'];
    }

    try {
        // ✅ Insert quiz with category
        $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, topic, difficulty, total_questions, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $topic, $difficulty, $numQuestions, $category]);
        $quizId = $pdo->lastInsertId();

        // Insert questions (matching your column names)
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");

        foreach ($result['questions'] as $q) {
            $options = $q['options'];
            $optionA = $options[0] ?? '';
            $optionB = $options[1] ?? '';
            $optionC = $options[2] ?? '';
            $optionD = $options[3] ?? '';

            $stmt->execute([
                $quizId,
                $q['question'],
                $optionA,
                $optionB,
                $optionC,
                $optionD,
                $q['correct_answer']
            ]);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['quiz_id'] = $quizId;
        $_SESSION['quiz_questions'] = $result['questions'];
        $_SESSION['quiz_topic'] = $topic;

        return [
            'success' => true,
            'quiz_id' => $quizId,
            'questions' => $result['questions'],
            'topic' => $topic
        ];

    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}
?>