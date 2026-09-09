
<?php
/**
 * quiz/take.php - Take Quiz Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// ✅ Get questions from session
$questions = $_SESSION['quiz_questions'] ?? [];
$topic = $_SESSION['quiz_topic'] ?? 'Untitled';

// ✅ If no questions, redirect to create page
if (empty($questions)) {
    $_SESSION['quiz_error'] = 'No quiz found. Please create a quiz first.';
    header('Location: create.php');
    exit();
}

// ✅ Calculate total questions
$totalQuestions = count($questions);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .question { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .options { margin-left: 20px; }
        .options label { display: block; margin: 5px 0; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #2196F3; text-decoration: none; }
        .error { color: red; }
        .info { margin-bottom: 20px; padding: 10px; background: #f5f5f5; border-radius: 5px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="create.php">Create New Quiz</a>
        <a href="../logout.php">Logout</a>
    </div>
    
    <h1>📝 Quiz: <?php echo htmlspecialchars($topic); ?></h1>
    
    <div class="info">
        <strong>Total Questions:</strong> <?php echo $totalQuestions; ?>
        <br>
        <strong>Instructions:</strong> Answer all questions and click Submit.
    </div>
    
    <form method="POST" action="submit.php">
        <?php foreach ($questions as $index => $question): ?>
            <div class="question">
                <h3>Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question']); ?></h3>
                <div class="options">
                    <?php foreach ($question['options'] as $option): ?>
                        <label>
                            <input type="radio" name="answer_<?php echo $index; ?>" value="<?php echo substr($option, 0, 1); ?>" required>
                            <?php echo htmlspecialchars($option); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <button type="submit">Submit Quiz</button>
    </form>
    
    <p style="font-size: 12px; color: #666; margin-top: 20px;">
        Powered by: Gemini API
    </p>
</body>
</html>