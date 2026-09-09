<?php
/**
 * quiz/create.php - Create Quiz Page
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_email'] = 'test@example.com';
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/generate_quiz.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = trim($_POST['topic'] ?? '');
    $numQuestions = intval($_POST['num_questions'] ?? 5);
    $difficulty = $_POST['difficulty'] ?? 'medium';

    if (empty($topic)) {
        $error = 'Please enter a topic.';
    } elseif ($numQuestions < 1 || $numQuestions > MAX_QUESTIONS) {
        $error = 'Please enter a valid number of questions (1-' . MAX_QUESTIONS . ').';
    } else {
        $result = createQuiz($topic, $numQuestions, $difficulty, $_SESSION['user_id']);

        if ($result['success']) {
            $_SESSION['quiz_id'] = $result['quiz_id'];
            $_SESSION['quiz_questions'] = $result['questions'];
            $_SESSION['quiz_topic'] = $topic;
            header('Location: take.php?quiz_id=' . $result['quiz_id']);
            exit();
        } else {
            $error = 'Failed to generate quiz: ' . $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-pencil-alt"></i> Create a New Quiz</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="create.php">
                            <div class="mb-3">
                                <label for="topic" class="form-label">Topic</label>
                                <input type="text" class="form-control" id="topic" name="topic" 
                                       placeholder="e.g., PHP Programming" value="<?php echo $_POST['topic'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="num_questions" class="form-label">Number of Questions</label>
                                <input type="number" class="form-control" id="num_questions" name="num_questions" 
                                       min="1" max="20" value="5">
                            </div>
                            <div class="mb-3">
                                <label for="difficulty" class="form-label">Difficulty</label>
                                <select class="form-select" id="difficulty" name="difficulty">
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-magic"></i> Generate Quiz
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>Powered by Gemini API (<?php echo GEMINI_MODEL ?? 'gemini-3.6-flash'; ?>)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>