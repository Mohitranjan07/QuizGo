<?php
/**
 * quiz/submit.php - Submit Quiz
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// ✅ Get questions from session
$questions = $_SESSION['quiz_questions'] ?? [];

if (empty($questions)) {
    $_SESSION['quiz_error'] = 'No quiz found.';
    header('Location: create.php');
    exit();
}

// ✅ Get user answers
$answers = [];
foreach ($questions as $index => $question) {
    $answers[$index] = $_POST['answer_' . $index] ?? null;
}

// ✅ Calculate score
$score = 0;
$results = [];

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
}

$total = count($questions);
$percentage = ($score / $total) * 100;

// ✅ Store results in session
$_SESSION['quiz_results'] = $results;
$_SESSION['quiz_score'] = $score;
$_SESSION['quiz_total'] = $total;
$_SESSION['quiz_percentage'] = $percentage;

// ✅ Generate a temporary quiz ID (if you don't have database)
$quizId = time(); // or use $_SESSION['quiz_id'] if you set it earlier

// ✅ Redirect to result.php with quiz_id
header('Location: result.php?quiz_id=' . $quizId);
exit();
?>