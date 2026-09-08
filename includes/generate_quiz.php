<?php
/**
 * includes/generate_quiz.php - Quiz Generation (Permanent Fix)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/api_helper.php';

/**
 * Attempt to parse JSON with automatic repair and fallback
 */
function parseQuizResponse($content, $topic) {
    // Remove markdown
    $content = preg_replace('/```json\s*/i', '', $content);
    $content = preg_replace('/```\s*/', '', $content);
    $content = trim($content);

    // Extract JSON between first { and last }
    if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
        $content = $matches[0];
    }

    // Clean control characters
    $content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
    $content = preg_replace('/\n/', ' ', $content);
    $content = preg_replace('/\r/', ' ', $content);
    $content = preg_replace('/\t/', ' ', $content);

    // Remove trailing commas
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

    // Try to decode
    $quizData = json_decode($content, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
        return ['success' => true, 'questions' => $quizData['questions']];
    }

    // --- FALLBACK: Manual parsing if JSON still fails ---
    // Look for question patterns like "Question 1:" or "1. " or numbered list
    $lines = explode("\n", $content);
    $questions = [];
    $currentQuestion = null;
    $currentOptions = [];
    $questionPatterns = [
        '/^Question\s*(\d+)[\:\.]\s*(.+)/i',
        '/^(\d+)[\.\)]\s*(.+)/',
        '/^Q(\d+)[\.\)]\s*(.+)/i'
    ];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        // Check if this line starts a new question
        $isQuestion = false;
        foreach ($questionPatterns as $pattern) {
            if (preg_match($pattern, $line, $matches)) {
                // Save previous question if exists
                if ($currentQuestion !== null && !empty($currentOptions)) {
                    // Determine correct answer: usually the first option or the one marked with *
                    $correct = 'A'; // default
                    foreach ($currentOptions as $opt) {
                        if (strpos($opt, '*') !== false || strpos($opt, '(correct)') !== false) {
                            $correct = substr($opt, 0, 1);
                            break;
                        }
                    }
                    $questions[] = [
                        'question' => $currentQuestion,
                        'options' => $currentOptions,
                        'correct_answer' => $correct
                    ];
                }
                $currentQuestion = $matches[2] ?? $matches[1] ?? $line;
                $currentOptions = [];
                $isQuestion = true;
                break;
            }
        }

        if ($isQuestion) continue;

        // If we're inside a question, treat line as an option
        if ($currentQuestion !== null) {
            // Options often start with A., B., etc. or just a dash
            if (preg_match('/^([A-D])[\.\)]\s*(.+)/', $line, $optMatches)) {
                $currentOptions[] = $optMatches[0];
            } elseif (preg_match('/^[\-\*]\s*(.+)/', $line, $optMatches)) {
                // Simple bullet; we'll assign a letter
                $letter = chr(65 + count($currentOptions)); // A, B, C, D
                $currentOptions[] = $letter . '. ' . $optMatches[1];
            } else {
                // Maybe it's a continuation of the question
                $currentQuestion .= ' ' . $line;
            }
        }
    }

    // Save last question
    if ($currentQuestion !== null && !empty($currentOptions)) {
        $correct = 'A'; // default
        foreach ($currentOptions as $opt) {
            if (strpos($opt, '*') !== false || strpos($opt, '(correct)') !== false) {
                $correct = substr($opt, 0, 1);
                break;
            }
        }
        $questions[] = [
            'question' => $currentQuestion,
            'options' => $currentOptions,
            'correct_answer' => $correct
        ];
    }

    if (!empty($questions)) {
        return ['success' => true, 'questions' => $questions];
    }

    // Last resort: try to decode again after extra cleaning
    $content = stripslashes($content);
    $quizData = json_decode($content, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
        return ['success' => true, 'questions' => $quizData['questions']];
    }

    // If all fails, return error with raw response
    return [
        'success' => false,
        'error' => 'JSON Error: ' . json_last_error_msg(),
        'raw_response' => $content
    ];
}

/**
 * Generate quiz questions with robust error handling
 */
function generateQuizQuestions($topic, $numQuestions = 5, $difficulty = 'medium') {
    $prompt = "Create $numQuestions multiple choice questions about $topic.
Return ONLY valid JSON. No markdown. No explanations.
Use this EXACT format:
{\"questions\":[{\"question\":\"Q1?\",\"options\":[\"A. 1\",\"B. 2\",\"C. 3\",\"D. 4\"],\"correct_answer\":\"A\"}]}";

    // Retry up to 2 times with increased token limit
    $maxAttempts = 2;
    $attempt = 0;
    $result = null;
    $content = '';

    while ($attempt < $maxAttempts) {
        $result = callAI($prompt, null, 3000); // Increased token limit

        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error']];
        }

        $content = trim($result['content']);

        // Save raw for debugging
        file_put_contents(__DIR__ . '/../debug_response.txt', $content);

        // Try to parse with our robust parser
        $parsed = parseQuizResponse($content, $topic);

        if ($parsed['success']) {
            return [
                'success' => true,
                'questions' => $parsed['questions'],
                'topic' => $topic
            ];
        }

        // If failed, increment and retry
        $attempt++;
        if ($attempt < $maxAttempts) {
            sleep(1);
        }
    }

    // If all attempts fail, return the last error
    return [
        'success' => false,
        'error' => $parsed['error'] ?? 'Failed to generate quiz',
        'raw_response' => $content
    ];
}

function createQuiz($topic, $numQuestions = 5, $difficulty = 'medium', $userId = null) {
    $result = generateQuizQuestions($topic, $numQuestions, $difficulty);

    if (!$result['success']) {
        return $result;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['quiz_questions'] = $result['questions'];
    $_SESSION['quiz_topic'] = $topic;

    return [
        'success' => true,
        'quiz_id' => time(),
        'questions' => $result['questions'],
        'topic' => $topic
    ];
}
?>