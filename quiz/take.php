<?php
/**
 * quiz/take.php - Take Quiz Page with Timer
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$questions = $_SESSION['quiz_questions'] ?? [];
$topic = $_SESSION['quiz_topic'] ?? 'Untitled';

if (empty($questions)) {
    $_SESSION['quiz_error'] = 'No quiz found. Please create a quiz first.';
    header('Location: create.php');
    exit();
}

$totalQuestions = count($questions);
$timeLimit = 10; // minutes
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-question-circle"></i> Quiz: <?php echo htmlspecialchars($topic); ?></h4>
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-clock"></i> <span id="timer">10:00</span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Total Questions:</strong> <?php echo $totalQuestions; ?>
                            <br>
                            <strong>Instructions:</strong> Answer all questions and click Submit. Time limit: <?php echo $timeLimit; ?> minutes.
                        </div>

                        <form method="POST" action="submit.php" id="quizForm">
                            <input type="hidden" name="time_taken" id="time_taken" value="0">
                            
                            <?php foreach ($questions as $index => $question): ?>
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Question <?php echo $index + 1; ?></strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?php echo htmlspecialchars($question['question']); ?></p>
                                        <div class="form-check">
                                            <?php foreach ($question['options'] as $option): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" 
                                                           name="answer_<?php echo $index; ?>" 
                                                           value="<?php echo substr($option, 0, 1); ?>" 
                                                           id="q<?php echo $index; ?>_<?php echo substr($option, 0, 1); ?>" required>
                                                    <label class="form-check-label" for="q<?php echo $index; ?>_<?php echo substr($option, 0, 1); ?>">
                                                        <?php echo htmlspecialchars($option); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane"></i> Submit Quiz
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const TIME_LIMIT = <?php echo $timeLimit; ?> * 60; // seconds
    let timeLeft = TIME_LIMIT;
    const timerElement = document.getElementById('timer');
    const form = document.getElementById('quizForm');
    const timeTakenInput = document.getElementById('time_taken');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! Your quiz will be submitted automatically.');
            form.submit();
        }
        timeLeft--;
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    form.addEventListener('submit', function() {
        const timeTaken = TIME_LIMIT - timeLeft;
        timeTakenInput.value = timeTaken;
    });
    </script>
</body>
</html>