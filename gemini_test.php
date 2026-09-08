<?php
// =============================================
// GEMINI API TEST - Replace OpenAI completely
// =============================================

// YOUR GEMINI API KEY (paste it here)
$apiKey = "AQ.Ab8RN6K9DxQPuJ8KDERHNnVWloahdevvHB7IxSG0gEml6wFsKA";

// Function to call Gemini API
function callGemini($prompt, $apiKey) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
    
    $postData = json_encode([
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 100,
            "topP" => 0.95,
            "topK" => 40
        ]
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return "cURL Error: " . $curlError;
    }
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? "No response";
    } else {
        return "Error $httpCode: " . $response;
    }
}

// =============================================
// TEST THE API
// =============================================

echo "=== GEMINI API TEST ===\n\n";

// Test 1: Simple prompt
echo "Test 1: Simple greeting\n";
echo "Response: " . callGemini("Say hello in 2 words", $apiKey) . "\n\n";

// Test 2: Longer prompt
echo "Test 2: General knowledge\n";
echo "Response: " . callGemini("What is the capital of France? Answer in one word.", $apiKey) . "\n\n";

// Test 3: Multiple requests (testing rate limits)
echo "Test 3: Multiple requests...\n";
for ($i = 1; $i <= 3; $i++) {
    $result = callGemini("Say the number $i in words", $apiKey);
    echo "  Request $i: " . $result . "\n";
    sleep(1); // Wait 1 second between requests
}

echo "\n✅ All tests completed!";
?>