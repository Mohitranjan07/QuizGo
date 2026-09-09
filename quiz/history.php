<?php
/**
 * quiz/history.php - Quiz History
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();
$userId = getCurrentUserId();

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-history"></i> Quiz History</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (empty($history)): ?>
                    <div class="alert alert-info">You haven't taken any quizzes yet.</div>
                    <a href="create.php" class="btn btn-primary">Create Your First Quiz</a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
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
                                <?php $count = 1; foreach ($history as $row): ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo htmlspecialchars($row['topic']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['difficulty'] == 'easy' ? 'success' : ($row['difficulty'] == 'medium' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($row['difficulty']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['score'] . '/' . $row['total']; ?></td>
                                        <td><?php echo round($row['percentage'], 1); ?>%</td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($row['completed_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>