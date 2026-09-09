<?php
/**
 * quiz/result.php - Quiz Results
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// ✅ Get results from SESSION (ignore quiz_id)
$results = $_SESSION['quiz_results'] ?? [];
$score = $_SESSION['quiz_score'] ?? 0;
$total = $_SESSION['quiz_total'] ?? 0;
$percentage = $_SESSION['quiz_percentage'] ?? 0;

if (empty($results)) {
    $_SESSION['quiz_error'] = 'No results found.';
    header('Location: create.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .score { font-size: 24px; margin: 20px 0; padding: 20px; background: #f5f5f5; border-radius: 5px; text-align: center; }
        .result { margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .correct { background: #e8f5e9; border-color: #4CAF50; }
        .wrong { background: #ffebee; border-color: #f44336; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #2196F3; text-decoration: none; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="create.php">Create New Quiz</a>
        <a href="../logout.php">Logout</a>
    </div>
    
    <h1>📊 Quiz Results</h1>
    
    <div class="score">
        <strong>Score:</strong> <?php echo $score; ?> / <?php echo $total; ?> 
        <br>
        <strong>Percentage:</strong> <?php echo round($percentage, 1); ?>%
        <br>
        <strong>Result:</strong> 
        <?php if ($percentage >= 80): ?>
            🎉 Excellent!
        <?php elseif ($percentage >= 60): ?>
            👍 Good job!
        <?php elseif ($percentage >= 40): ?>
            📚 Keep learning!
        <?php else: ?>
            💪 Practice more!
        <?php endif; ?>
    </div>
    
    <h2>Detailed Results</h2>
    
    <?php foreach ($results as $index => $result): ?>
        <div class="result <?php echo $result['is_correct'] ? 'correct' : 'wrong'; ?>">
            <h3>Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($result['question']); ?></h3>
            <p><strong>Your Answer:</strong> <?php echo htmlspecialchars($result['user_answer']); ?></p>
            <?php if (!$result['is_correct']): ?>
                <p><strong>Correct Answer:</strong> <?php echo htmlspecialchars($result['correct_answer']); ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <a href="create.php" class="btn">Create Another Quiz</a>
</body>
</html>