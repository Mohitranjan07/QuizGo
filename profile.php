<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

$userId = getCurrentUserId();
$user = getUserById($userId);

$stmt = $pdo->prepare("SELECT COUNT(*) as total_quizzes, AVG(percentage) as avg_score FROM results WHERE user_id = ?");
$stmt->execute([$userId]);
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user"></i> User Profile</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Member since:</strong> <?= date('M j, Y', strtotime($user['created_at'])) ?></p>
                        <hr>
                        <h5>Statistics</h5>
                        <p><strong>Total Quizzes Taken:</strong> <?= $stats['total_quizzes'] ?></p>
                        <p><strong>Average Score:</strong> <?= round($stats['avg_score'], 1) ?>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>