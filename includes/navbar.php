<?php
/**
 * includes/navbar.php - Smart Navbar
 * Detects if we're in a subfolder and adjusts paths accordingly
 */

// Detect if current file is in a subfolder (like quiz/)
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
$isInSubfolder = in_array($currentDir, ['quiz', 'config', 'includes']);
$base = $isInSubfolder ? '../' : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $base ?>dashboard.php">
            <i class="fas fa-brain me-2"></i>AI Quiz Builder
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>dashboard.php">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>quiz/create.php">
                        <i class="fas fa-plus-circle"></i> Create Quiz
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>quiz/history.php">
                        <i class="fas fa-history"></i> History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>quiz/leaderboard.php">
                        <i class="fas fa-trophy"></i> Leaderboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base ?>logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>