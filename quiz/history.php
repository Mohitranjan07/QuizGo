<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("
    SELECT
        results.id AS result_id,
        results.score,
        results.total_questions,
        results.completed_at,
        quizzes.title,
        quizzes.topic,
        quizzes.difficulty
    FROM results
    JOIN quizzes
        ON results.quiz_id = quizzes.id
    WHERE results.user_id = ?
    ORDER BY results.completed_at DESC
");

$stmt->execute([$user_id]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Quiz History - AI Quiz Builder</title>

</head>

<body>

    <h1>AI Quiz Builder</h1>

    <h2>Quiz History</h2>

    <?php if (count($results) == 0): ?>

        <p>You have not completed any quizzes yet.</p>

    <?php else: ?>

        <table border="1" cellpadding="8">

            <tr>

                <th>Quiz</th>
                <th>Topic</th>
                <th>Difficulty</th>
                <th>Score</th>
                <th>Completed At</th>
                <th>View Result</th>

            </tr>

            <?php foreach ($results as $result): ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($result["title"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($result["topic"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($result["difficulty"]); ?>
                    </td>

                    <td>
                        <?php echo $result["score"]; ?>
                        /
                        <?php echo $result["total_questions"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($result["completed_at"]); ?>
                    </td>

                    <td>

                        <a href="result.php?result_id=<?php echo $result["result_id"]; ?>">
                            View Result
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    <?php endif; ?>

    <br>

    <a href="../dashboard.php">
        Back to Dashboard
    </a>

</body>

</html>