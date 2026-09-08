<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$message = "";

$quiz_id = isset($_GET["quiz_id"]) ? (int) $_GET["quiz_id"] : 0;

if ($quiz_id <= 0) {
    die("Invalid quiz ID.");
}

/*
 * Make sure this quiz belongs to the logged-in user.
 */
$stmt = $pdo->prepare(
    "SELECT id, title, topic, total_questions
     FROM quizzes
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([
    $quiz_id,
    $_SESSION["user_id"]
]);

$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    die("Quiz not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $question_text = trim($_POST["question_text"]);
    $option_a = trim($_POST["option_a"]);
    $option_b = trim($_POST["option_b"]);
    $option_c = trim($_POST["option_c"]);
    $option_d = trim($_POST["option_d"]);
    $correct_answer = $_POST["correct_answer"];
    $explanation = trim($_POST["explanation"]);

    if (
        empty($question_text) ||
        empty($option_a) ||
        empty($option_b) ||
        empty($option_c) ||
        empty($option_d) ||
        empty($correct_answer)
    ) {

        $message = "All question fields are required.";

    } elseif (!in_array($correct_answer, ["A", "B", "C", "D"])) {

        $message = "Invalid correct answer.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO questions
            (
                quiz_id,
                question_text,
                option_a,
                option_b,
                option_c,
                option_d,
                correct_answer,
                explanation
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $quiz_id,
            $question_text,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_answer,
            $explanation
        ]);

        $message = "Question added successfully!";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Question - AI Quiz Builder</title>

</head>

<body>

    <h1>AI Quiz Builder</h1>

    <h2>Add Question</h2>

    <p>
        Quiz:
        <?php echo htmlspecialchars($quiz["title"]); ?>
    </p>

    <p>
        Topic:
        <?php echo htmlspecialchars($quiz["topic"]); ?>
    </p>

    <?php if (!empty($message)): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Question:</label>
        <br>

        <textarea
            name="question_text"
            rows="4"
            cols="50"
            required
        ></textarea>

        <br><br>

        <label>Option A:</label>
        <br>

        <input
            type="text"
            name="option_a"
            required
        >

        <br><br>

        <label>Option B:</label>
        <br>

        <input
            type="text"
            name="option_b"
            required
        >

        <br><br>

        <label>Option C:</label>
        <br>

        <input
            type="text"
            name="option_c"
            required
        >

        <br><br>

        <label>Option D:</label>
        <br>

        <input
            type="text"
            name="option_d"
            required
        >

        <br><br>

        <label>Correct Answer:</label>
        <br>

        <select name="correct_answer" required>

            <option value="">Select Answer</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>

        </select>

        <br><br>

        <label>Explanation:</label>
        <br>

        <textarea
            name="explanation"
            rows="4"
            cols="50"
        ></textarea>

        <br><br>

        <button type="submit">
            Add Question
        </button>

    </form>

    <br>

    <a href="../dashboard.php">
        Back to Dashboard
    </a>

</body>

</html>