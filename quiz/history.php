<?php
/**
 * quiz/history.php - Quiz History (Database Version)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Require login
requireLogin();

$userId = getCurrentUserId();

// Fetch user's quiz history
try {
    $stmt = $pdo->prepare("
        SELECT r.*, q.topic, q.difficulty, q.num_questions 
        FROM results r 
        JOIN quizzes q ON r.quiz_id = q.id 
        WHERE r.user_id = ? 
        ORDER BY r.completed_at DESC
    ");
    $stmt->execute([$userId]);
    $history = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to fetch history: ' . $e->getMessage();
    $history = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz History</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #4CAF50; color: white; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #2196F3; text-decoration: none; }
        .btn { display: inline-block; padding: 8px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #45a049; }
        .error { color: red; }
        .empty { text-align: center; padding: 40px; color: #666; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="create.php">Create Quiz</a>
        <a href="../logout.php">Logout</a>
    </div>
    
    <h1>📊 Quiz History</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (empty($history)): ?>
        <div class="empty">
            <p>You haven't taken any quizzes yet.</p>
            <a href="create.php" class="btn">Create Your First Quiz</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Topic</th>
                    <th>Difficulty</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php foreach ($history as $row): ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['topic']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($row['difficulty'])); ?></td>
                        <td><?php echo $row['score'] . '/' . $row['total']; ?></td>
                        <td><?php echo round($row['percentage'], 1); ?>%</td>
                        <td><?php echo date('M j, Y g:i A', strtotime($row['completed_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>