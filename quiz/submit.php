<?php
/**
 * quiz/submit.php - Submit Quiz with Database
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$userId = getCurrentUserId();
$questions = $_SESSION['quiz_questions'] ?? [];
$quizId = $_SESSION['quiz_id'] ?? null;

if (empty($questions)) {
    header('Location: create.php');
    exit();
}

// Get user answers
$answers = [];
foreach ($questions as $index => $question) {
    $answers[$index] = $_POST['answer_' . $index] ?? null;
}

// Calculate score
$score = 0;
$results = [];
$answerData = [];

foreach ($questions as $index => $question) {
    $userAnswer = $answers[$index] ?? '';
    $correctAnswer = $question['correct_answer'] ?? '';
    $isCorrect = $userAnswer === $correctAnswer;
    
    if ($isCorrect) {
        $score++;
    }
    
    $results[] = [
        'question' => $question['question'],
        'user_answer' => $userAnswer,
        'correct_answer' => $correctAnswer,
        'is_correct' => $isCorrect
    ];
    
    $answerData[] = [
        'question_id' => $index,
        'user_answer' => $userAnswer,
        'correct_answer' => $correctAnswer,
        'is_correct' => $isCorrect
    ];
}

$total = count($questions);
$percentage = ($score / $total) * 100;

// Save results to database
try {
    if ($quizId) {
        $stmt = $pdo->prepare("INSERT INTO results (user_id, quiz_id, score, total, percentage, answers) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $quizId,
            $score,
            $total,
            $percentage,
            json_encode($answerData)
        ]);
        $resultId = $pdo->lastInsertId();
        $_SESSION['result_id'] = $resultId;
    }
} catch (PDOException $e) {
    // Log error but continue
    error_log("Failed to save results: " . $e->getMessage());
}

// Store results in session
$_SESSION['quiz_results'] = $results;
$_SESSION['quiz_score'] = $score;
$_SESSION['quiz_total'] = $total;
$_SESSION['quiz_percentage'] = $percentage;

header('Location: result.php');
exit();
?>