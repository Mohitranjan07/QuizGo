<?php
/**
 * config/api_helper.php - Gemini API Helper
 */

require_once __DIR__ . '/config.php';

function callGemini($prompt, $systemPrompt = null, $maxTokens = 500, $maxRetries = 3) {
    $apiKey = GEMINI_API_KEY;
    
    if (empty($apiKey) || strlen($apiKey) < 10) {
        return ['success' => false, 'error' => 'API key not configured'];
    }
    
    $url = GEMINI_API_URL . "?key=" . urlencode($apiKey);
    
    $contents = [];
    if ($systemPrompt) {
        $contents[] = ["role" => "user", "parts" => [["text" => $systemPrompt]]];
    }
    $contents[] = ["role" => "user", "parts" => [["text" => $prompt]]];
    
    $postData = json_encode([
        "contents" => $contents,
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => $maxTokens,
            "topP" => 0.95,
            "topK" => 40
        ]
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CONNECTION_TIMEOUT);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    // ✅ SSL - DISABLE FOR DEVELOPMENT
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Add these to help with SSL issues
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    curl_close($ch);
    
    if ($curlError) {
        $errorMsg = "cURL Error: " . $curlError;
        if ($curlErrno == 28) {
            $errorMsg = "Connection timed out after " . API_TIMEOUT . " seconds";
        } elseif ($curlErrno == 35) {
            $errorMsg = "SSL connection error. Please check your internet connection.";
        }
        return ['success' => false, 'error' => $errorMsg];
    }
    
    if ($httpCode != 200) {
        $errorData = json_decode($response, true);
        $errorMsg = isset($errorData['error']['message']) 
            ? $errorData['error']['message'] 
            : "HTTP $httpCode";
        return ['success' => false, 'error' => $errorMsg];
    }
    
    $data = json_decode($response, true);
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => true,
            'content' => $data['candidates'][0]['content']['parts'][0]['text']
        ];
    }
    
    return ['success' => false, 'error' => 'Unexpected response format'];
}

function callAI($prompt, $systemPrompt = null, $maxTokens = 500, $maxRetries = 3) {
    return callGemini($prompt, $systemPrompt, $maxTokens, $maxRetries);
}
?>