<?php
/**
 * test_api.php - Test Gemini API
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/api_helper.php';

// Force SSL disable for testing
if (IS_DEVELOPMENT) {
    ini_set('curl.cainfo', '');
}

echo "===========================================\n";
echo "     GEMINI API TEST\n";
echo "===========================================\n\n";

echo "Model: " . GEMINI_MODEL . "\n";
echo "Timeout: " . API_TIMEOUT . " seconds\n\n";

echo "Testing API call...\n";
$result = callAI("What is the capital of France? Answer in one word.");

if ($result['success']) {
    echo "✅ SUCCESS!\n";
    echo "Response: " . $result['content'] . "\n";
} else {
    echo "❌ FAILED!\n";
    echo "Error: " . $result['error'] . "\n";
}

echo "\n===========================================\n";
?>