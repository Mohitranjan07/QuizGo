<?php
/**
 * includes/generate_quiz.php - Quiz Generation with Database
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/api_helper.php';
require_once __DIR__ . '/../config/database.php'; // <-- PDO connection

/**
 * Generate quiz questions using Gemini API
 */
function generateQuizQuestions($topic, $numQuestions = 5, $difficulty = 'medium') {
    $prompt = "Create $numQuestions multiple choice questions about $topic.
Return ONLY valid JSON. No markdown. No explanations.
Use this EXACT format:
{\"questions\":[{\"question\":\"Q1?\",\"options\":[\"A. 1\",\"B. 2\",\"C. 3\",\"D. 4\"],\"correct_answer\":\"A\"}]}";

    // Retry up to 2 times with higher token limit
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

        // Save raw for debugging
        file_put_contents(__DIR__ . '/../debug_response.txt', $content);

        // Remove markdown
        $content = preg_replace('/```json\s*/i', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        $content = trim($content);

        // Extract JSON
        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $content = $matches[0];
        }

        // Clean
        $content = preg_replace('/\n/', ' ', $content);
        $content = preg_replace('/\r/', ' ', $content);
        $content = preg_replace('/\t/', ' ', $content);
        $content = stripslashes($content);
        $content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
        $content = preg_replace('/,\s*}/', '}', $content);
        $content = preg_replace('/,\s*]/', ']', $content);

        // Auto-complete missing brackets
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
            break; // Success
        }

        $attempt++;
        if ($attempt < $maxAttempts) {
            sleep(1); // Wait before retry
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

/**
 * Create a quiz and save to database
 */
function createQuiz($topic, $numQuestions = 5, $difficulty = 'medium', $userId = null) {
    global $pdo; // Use database connection

    // Generate questions
    $result = generateQuizQuestions($topic, $numQuestions, $difficulty);

    if (!$result['success']) {
        return $result;
    }

    // If no user ID, use session
    if (!$userId) {
        $userId = $_SESSION['user_id'] ?? null;
    }

    if (!$userId) {
        return ['success' => false, 'error' => 'User not logged in'];
    }

    try {
        // Insert quiz into database
        $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, topic, difficulty, num_questions) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $topic, $difficulty, $numQuestions]);
        $quizId = $pdo->lastInsertId();

        // Insert questions
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question, options, correct_answer) VALUES (?, ?, ?, ?)");
        foreach ($result['questions'] as $q) {
            $stmt->execute([
                $quizId,
                $q['question'],
                json_encode($q['options']),
                $q['correct_answer']
            ]);
        }

        // Store in session for immediate use
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