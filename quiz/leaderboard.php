<?php
/**
 * quiz/leaderboard.php - Leaderboard
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// ✅ Use total_questions (from results table) and compute percentage in PHP
$stmt = $pdo->query("
    SELECT 
        r.score,
        r.total_questions AS total,
        r.completed_at,
        u.name,
        q.topic
    FROM results r
    JOIN users u ON r.user_id = u.id
    JOIN quizzes q ON r.quiz_id = q.id
    ORDER BY r.id DESC
    LIMIT 50
");
$leaders = $stmt->fetchAll();

// ✅ Compute percentage and sort
foreach ($leaders as &$row) {
    $row['percentage'] = ($row['total'] > 0) 
        ? ($row['score'] / $row['total']) * 100 
        : 0;
}
unset($row);

// Sort by percentage descending
usort($leaders, function($a, $b) {
    return $b['percentage'] <=> $a['percentage'];
});

// Keep only top 20
$leaders = array_slice($leaders, 0, 20);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - AI Quiz Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-trophy"></i> Leaderboard</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($leaders)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> No quiz results yet. Be the first to take a quiz!
                            </div>
                            <div class="text-center">
                                <a href="create.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create a Quiz
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Rank</th>
                                            <th>User</th>
                                            <th>Topic</th>
                                            <th>Score</th>
                                            <th>Percentage</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $rank = 1; foreach ($leaders as $row): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($rank == 1): ?>
                                                        <span class="badge bg-warning text-dark">🥇 #1</span>
                                                    <?php elseif ($rank == 2): ?>
                                                        <span class="badge bg-secondary">🥈 #2</span>
                                                    <?php elseif ($rank == 3): ?>
                                                        <span class="badge bg-danger">🥉 #3</span>
                                                    <?php else: ?>
                                                        #<?= $rank ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['topic']) ?></td>
                                                <td><?= $row['score'] ?>/<?= $row['total'] ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $row['percentage'] >= 80 ? 'success' : ($row['percentage'] >= 60 ? 'primary' : ($row['percentage'] >= 40 ? 'warning' : 'danger')) ?>">
                                                        <?= round($row['percentage'], 1) ?>%
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($row['completed_at'])) ?></td>
                                            </tr>
                                            <?php $rank++; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>