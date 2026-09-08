<?php
/**
 * test_quiz.php - Test Quiz Generation
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/generate_quiz.php';

echo "===========================================\n";
echo "     QUIZ GENERATION TEST\n";
echo "===========================================\n\n";

echo "Topic: PHP Programming\n";
echo "Questions: 3\n";
echo "Difficulty: easy\n\n";

echo "Generating quiz...\n";
$result = generateQuizQuestions("PHP Programming", 3, "easy");

if ($result['success']) {
    echo "✅ QUIZ GENERATED SUCCESSFULLY!\n\n";
    echo "Generated " . count($result['questions']) . " questions:\n\n";
    
    foreach ($result['questions'] as $index => $question) {
        echo "Question " . ($index + 1) . ": " . $question['question'] . "\n";
        echo "Options:\n";
        foreach ($question['options'] as $option) {
            echo "  " . $option . "\n";
        }
        echo "Correct Answer: " . $question['correct_answer'] . "\n\n";
    }
} else {
    echo "❌ FAILED TO GENERATE QUIZ!\n";
    echo "Error: " . $result['error'] . "\n";
    
    if (isset($result['raw_response'])) {
        echo "\nRaw Response:\n" . $result['raw_response'] . "\n";
    }
}

echo "===========================================\n";
?>