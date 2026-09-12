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

$results = $_SESSION['quiz_results'] ?? [];
$score = $_SESSION['quiz_score'] ?? 0;
$total = $_SESSION['quiz_total'] ?? 0;
$percentage = $_SESSION['quiz_percentage'] ?? 0;
$timeTaken = $_SESSION['quiz_time_taken'] ?? 0;

if (empty($results)) {
    header('Location: create.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Quiz Results</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-<?php echo $percentage >= 60 ? 'success' : 'warning'; ?> text-center">
                            <h4>Score: <?php echo $score; ?> / <?php echo $total; ?></h4>
                            <h5>Percentage: <?php echo round($percentage, 1); ?>%</h5>
                            <h5>Time Taken: <?php echo gmdate("i:s", $timeTaken); ?></h5>
                            <h5><?php 
                                if ($percentage >= 80) echo '🎉 Excellent!';
                                elseif ($percentage >= 60) echo '👍 Good job!';
                                elseif ($percentage >= 40) echo '📚 Keep learning!';
                                else echo '💪 Practice more!';
                            ?></h5>
                        </div>

                        <?php foreach ($results as $index => $result): ?>
                            <div class="card mb-3 border-<?php echo $result['is_correct'] ? 'success' : 'danger'; ?>">
                                <div class="card-header bg-<?php echo $result['is_correct'] ? 'success' : 'danger'; ?> text-white">
                                    <strong>Question <?php echo $index + 1; ?></strong>
                                </div>
                                <div class="card-body">
                                    <p><?php echo htmlspecialchars($result['question']); ?></p>
                                    <p><strong>Your Answer:</strong> <?php echo htmlspecialchars($result['user_answer']); ?></p>
                                    <?php if (!$result['is_correct']): ?>
                                        <p><strong>Correct Answer:</strong> <?php echo htmlspecialchars($result['correct_answer']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-grid gap-2">
                            <a href="create.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create Another Quiz
                            </a>
                            <a href="export_pdf.php" class="btn btn-secondary">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>