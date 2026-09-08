<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {

        $message = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->execute([$email]);

        if ($check->fetch()) {

            $message = "Email already registered.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword
            ]);

            $message = "Registration successful!";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Register - AI Quiz Builder</title>

</head>

<body>

    <h1>AI Quiz Builder</h1>

    <h2>Create Account</h2>

    <?php if (!empty($message)): ?>

        <p><?php echo htmlspecialchars($message); ?></p>

    <?php endif; ?>

    <form method="POST">

        <label>Name:</label>
        <br>

        <input
            type="text"
            name="name"
            required
        >

        <br><br>

        <label>Email:</label>
        <br>

        <input
            type="email"
            name="email"
            required
        >

        <br><br>

        <label>Password:</label>
        <br>

        <input
            type="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit">
            Register
        </button>

    </form>

</body>
</html>