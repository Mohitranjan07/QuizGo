<?php

require_once "includes/auth.php";

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard - AI Quiz Builder</title>

</head>

<body>

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