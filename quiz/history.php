<?php
/**
 * quiz/history.php - Quiz History with Category Filter
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();
$userId = getCurrentUserId();

// Get distinct categories for filter dropdown
$categories = $pdo->query("SELECT DISTINCT category FROM quizzes ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Build query — uses total_questions from results table
$sql = "SELECT 
            r.score,
            r.total_questions AS total,
            r.time_taken,
            r.completed_at,
            q.topic,
            q.difficulty,
            q.category
        FROM results r 
        JOIN quizzes q ON r.quiz_id = q.id 
        WHERE r.user_id = ?";
$params = [$userId];

if (!empty($_GET['category'])) {
    $sql .= " AND q.category = ?";
    $params[] = $_GET['category'];
}

$sql .= " ORDER BY r.completed_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
    <title>Quiz History - AI Quiz Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-history"></i> Quiz History</h4>
                        <form method="GET" class="d-flex">
                            <select name="category" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= (($_GET['category'] ?? '') == $cat) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (empty($history)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> You haven't taken any quizzes yet.
                            </div>
                            <div class="text-center">
                                <a href="create.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Your First Quiz
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Topic</th>
                                            <th>Category</th>
                                            <th>Difficulty</th>
                                            <th>Score</th>
                                            <th>Percentage</th>
                                            <th>Time Taken</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; foreach ($history as $row): 
                                            $pct = ($row['total'] > 0) ? ($row['score'] / $row['total']) * 100 : 0;
                                        ?>
                                            <tr>
                                                <td><?= $count++ ?></td>
                                                <td><?= htmlspecialchars($row['topic']) ?></td>
                                                <td><?= htmlspecialchars($row['category']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $row['difficulty'] == 'easy' ? 'success' : ($row['difficulty'] == 'medium' ? 'warning' : 'danger') ?>">
                                                        <?= ucfirst($row['difficulty']) ?>
                                                    </span>
                                                </td>
                                                <td><?= $row['score'] ?>/<?= $row['total'] ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $pct >= 80 ? 'success' : ($pct >= 60 ? 'primary' : ($pct >= 40 ? 'warning' : 'danger')) ?>">
                                                        <?= round($pct, 1) ?>%
                                                    </span>
                                                </td>
                                                <td><?= gmdate("i:s", $row['time_taken'] ?? 0) ?></td>
                                                <td><?= date('M j, Y g:i A', strtotime($row['completed_at'])) ?></td>
                                            </tr>
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