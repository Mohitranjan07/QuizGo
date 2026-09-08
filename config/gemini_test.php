<?php
/**
 * gemini_test.php - Test Script with Proper Timeout Handling
 */

require_once 'config/config.php';
require_once 'api_helper.php';

// ============================================
// Test Configuration
// ============================================

echo "===========================================\n";
echo "     API CONNECTION TEST\n";
echo "===========================================\n\n";

echo "Active API: " . API_TYPE . "\n";
echo "API Model: " . API_MODEL . "\n";
echo "API URL: " . API_URL . "\n";
echo "Timeout: " . API_TIMEOUT . " seconds\n";
echo "Connection Timeout: " . CONNECTION_TIMEOUT . " seconds\n";
echo "Environment: " . (IS_DEVELOPMENT ? "Development" : "Production") . "\n\n";

// ============================================
// Step 1: Check cURL
// ============================================

echo "📌 Step 1: Checking cURL...\n";
if (function_exists('curl_version')) {
    $version = curl_version();
    echo "✅ cURL Installed (Version: " . $version['version'] . ")\n\n";
} else {
    echo "❌ cURL NOT INSTALLED! Please install php-curl.\n";
    exit(1);
}

// ============================================
// Step 2: Check API Key
// ============================================

echo "📌 Step 2: Checking API Key...\n";
$apiKey = API_KEY;
if (empty($apiKey) || $apiKey == "AIzaSy...YOUR_GEMINI_KEY_HERE" || $apiKey == "sk-proj-...") {
    echo "❌ API Key is not configured! Please add your key to config.php\n";
    exit(1);
}
echo "✅ API Key present (starts with: " . substr($apiKey, 0, 10) . "...)\n\n";

// ============================================
// Step 3: Test Connection
// ============================================

echo "📌 Step 3: Testing API Connection...\n";
echo "Making API call with timeout: " . API_TIMEOUT . " seconds...\n\n";

// Use the enhanced test function
$result = testAPIConnection();

// ============================================
// Step 4: Display Results
// ============================================

echo "===========================================\n";
echo "     TEST RESULTS\n";
echo "===========================================\n\n";

if ($result['success']) {
    echo "✅ CONNECTION SUCCESSFUL!\n";
    echo "   API: " . $result['api'] . "\n";
    echo "   Response: " . $result['response'] . "\n\n";
    echo "Your API is working correctly!\n";
} else {
    echo "❌ CONNECTION FAILED!\n";
    echo "   API: " . $result['api'] . "\n";
    echo "   Error: " . $result['error'] . "\n\n";
    
    echo "Common Solutions:\n";
    echo "1. Check your internet connection\n";
    echo "2. Verify your API key is correct\n";
    echo "3. Check if you can access https://generativelanguage.googleapis.com in your browser\n";
    echo "4. Check if your firewall is blocking outgoing connections\n";
    echo "5. Try increasing API_TIMEOUT in config.php\n";
}

echo "\n===========================================\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo "===========================================\n";
?>