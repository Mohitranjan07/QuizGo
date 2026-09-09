<?php
require_once __DIR__ . '/includes/auth.php';
$isLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Quiz Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="container mt-5 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body py-5">
                        <h1 class="display-4"><i class="fas fa-brain text-primary"></i> AI Quiz Builder</h1>
                        <p class="lead">Create custom quizzes powered by Google Gemini AI</p>
                        <?php if ($isLoggedIn): ?>
                            <a href="dashboard.php" class="btn btn-success btn-lg mt-3">
                                <i class="fas fa-dashboard"></i> Go to Dashboard
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-primary btn-lg mt-3">
                                <i class="fas fa-user-plus"></i> Get Started
                            </a>
                            <a href="login.php" class="btn btn-outline-secondary btn-lg mt-3 ms-2">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>