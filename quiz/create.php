<?php
// DEBUG: Show raw API response
if (isset($result['content'])) {
    echo "<pre>Raw Response:\n" . htmlspecialchars($result['content']) . "</pre>";
    exit;
}
/**
 * quiz/create.php - Create Quiz Page
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// TEMPORARY: Auto-login for testing (remove this later)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_email'] = 'test@example.com';
}

// Include required files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/generate_quiz.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$error = null;
$success = null;
$generatedQuestions = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = trim($_POST['topic'] ?? '');
    $numQuestions = intval($_POST['num_questions'] ?? 5);
    $difficulty = $_POST['difficulty'] ?? 'medium';
    
    if (empty($topic)) {
        $error = 'Please enter a topic.';
    } elseif ($numQuestions < 1 || $numQuestions > MAX_QUESTIONS) {
        $error = 'Please enter a valid number of questions (1-' . MAX_QUESTIONS . ').';
    } else {
        // Generate quiz using the updated function
        $result = createQuiz($topic, $numQuestions, $difficulty, $_SESSION['user_id']);
        
        if ($result['success']) {
            $success = 'Quiz generated successfully!';
            $generatedQuestions = $result['questions'];
            $_SESSION['quiz_id'] = $result['quiz_id'];
            
            // Redirect to quiz page
            header('Location: take.php?quiz_id=' . $result['quiz_id']);
            exit();
        } else {
            $error = 'Failed to generate quiz: ' . $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #45a049; }
        .error { color: red; padding: 10px; background: #ffebee; border-radius: 4px; margin-bottom: 15px; }
        .success { color: green; padding: 10px; background: #e8f5e9; border-radius: 4px; margin-bottom: 15px; }
        .api-info { font-size: 12px; color: #666; margin-top: 20px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #2196F3; text-decoration: none; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="history.php">Quiz History</a>
        <a href="../logout.php">Logout</a>
    </div>
    
    <h1>Create a New Quiz</h1>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="create.php">
        <div class="form-group">
            <label for="topic">Topic:</label>
            <input type="text" id="topic" name="topic" required 
                   placeholder="e.g., PHP Programming" value="<?php echo $_POST['topic'] ?? ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="num_questions">Number of Questions:</label>
            <input type="number" id="num_questions" name="num_questions" 
                   min="<?php echo MIN_QUESTIONS; ?>" 
                   max="<?php echo MAX_QUESTIONS; ?>" 
                   value="5">
        </div>
        
        <div class="form-group">
            <label for="difficulty">Difficulty:</label>
            <select id="difficulty" name="difficulty">
                <option value="easy">Easy</option>
                <option value="medium" selected>Medium</option>
                <option value="hard">Hard</option>
            </select>
        </div>
        
        <button type="submit">Generate Quiz</button>
    </form>
    
    <div class="api-info">
        Powered by: Gemini API (<?php echo GEMINI_MODEL ?? 'gemini-3.6-flash'; ?>)
    </div>
</body>
</html>