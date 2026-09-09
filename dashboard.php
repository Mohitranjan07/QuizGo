<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$userName = getCurrentUserName();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($userName); ?>!</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Ready to create a new quiz? Click the button below.</p>
                        <a href="quiz/create.php" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-rocket"></i> Create a New Quiz
                        </a>
                        <hr>
                        <div class="row text-center mt-3">
                            <div class="col-sm-6 mb-2">
                                <a href="quiz/history.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-clock"></i> View History
                                </a>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <a href="logout.php" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>