<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$results = $_SESSION['quiz_results'] ?? [];
$score = $_SESSION['quiz_score'] ?? 0;
$total = $_SESSION['quiz_total'] ?? 0;
$percentage = $_SESSION['quiz_percentage'] ?? 0;
$timeTaken = $_SESSION['quiz_time_taken'] ?? 0;

$html = '<h1>Quiz Results</h1>';
$html .= "<p>Score: $score / $total ($percentage%)</p>";
$html .= "<p>Time Taken: " . gmdate("i:s", $timeTaken) . "</p><hr>";
foreach ($results as $index => $r) {
    $html .= "<p><strong>Q" . ($index+1) . ": " . htmlspecialchars($r['question']) . "</strong><br>";
    $html .= "Your Answer: " . htmlspecialchars($r['user_answer']) . "<br>";
    if (!$r['is_correct']) {
        $html .= "Correct Answer: " . htmlspecialchars($r['correct_answer']) . "<br>";
    }
    $html .= "</p>";
}

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('quiz_results.pdf', ['Attachment' => true]);