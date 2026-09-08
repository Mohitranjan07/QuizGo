<?php
/**
 * debug_quiz.php - Debug Quiz Generation from Terminal
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/generate_quiz.php';

echo "===========================================\n";
echo "     QUIZ GENERATION DEBUG\n";
echo "===========================================\n\n";

$topic = "Operating Systems";
$numQuestions = 3;
$difficulty = "medium";

echo "Topic: $topic\n";
echo "Questions: $numQuestions\n";
echo "Difficulty: $difficulty\n\n";

echo "Calling API...\n";
$result = generateQuizQuestions($topic, $numQuestions, $difficulty);

echo "\n===========================================\n";
echo "     RESULT\n";
echo "===========================================\n\n";

if ($result['success']) {
    echo "✅ SUCCESS!\n";
    echo "Generated " . count($result['questions']) . " questions:\n\n";
    print_r($result['questions']);
} else {
    echo "❌ FAILED!\n";
    echo "Error: " . $result['error'] . "\n\n";
    
    if (isset($result['raw_response'])) {
        echo "Raw Response:\n";
        echo "-------------------------------------------\n";
        echo $result['raw_response'] . "\n";
        echo "-------------------------------------------\n";
    }
}

echo "\n===========================================\n";
?>