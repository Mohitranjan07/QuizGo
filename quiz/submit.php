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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitting Quiz</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- ✅ Navbar -->
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="mt-3">Submitting your quiz...</h5>
                        <p class="text-muted">Please wait while we save your results.</p>
                        <a href="result.php" class="btn btn-primary">View Results</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>