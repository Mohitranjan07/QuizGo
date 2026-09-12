<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("
    SELECT u.name, r.score, r.total, r.percentage, q.topic, r.completed_at
    FROM results r
    JOIN users u ON r.user_id = u.id
    JOIN quizzes q ON r.quiz_id = q.id
    ORDER BY r.percentage DESC, r.completed_at ASC
    LIMIT 20
");
$leaders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-trophy"></i> Leaderboard</h4>
            </div>
            <div class="card-body">
                <?php if (empty($leaders)): ?>
                    <div class="alert alert-info">No quiz results yet.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Topic</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($leaders as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['topic']) ?></td>
                                    <td><?= $row['score'] ?>/<?= $row['total'] ?></td>
                                    <td><?= round($row['percentage'],1) ?>%</td>
                                    <td><?= date('M j, Y', strtotime($row['completed_at'])) ?></td>
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