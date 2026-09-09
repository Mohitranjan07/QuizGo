<?php

require_once "includes/auth.php";

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard - AI Quiz Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <h1>AI Quiz Builder</h1>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h2>

    <p>
        You are successfully logged in.
    </p>

    <p>
        Email:
        <?php echo htmlspecialchars($_SESSION["user_email"]); ?>
    </p>

    <p>
        Role:
        <?php echo htmlspecialchars($_SESSION["user_role"]); ?>
    </p>

    <hr>

    <h3>Quiz Builder</h3>

    <p>Create your own quiz.</p>

<a href="quiz/create.php">
    Create Quiz
</a>

    <a href="logout.php">Logout</a>

</body>

</html>