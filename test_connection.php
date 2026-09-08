<?php
echo "Testing connection to Google APIs...\n\n";

// Test 1: Ping google.com
echo "Test 1: Checking internet...\n";
$connected = @fsockopen("google.com", 80, $errno, $errstr, 5);
if ($connected) {
    echo "✅ Internet connection OK\n";
    fclose($connected);
} else {
    echo "❌ No internet connection: $errstr\n";
}

// Test 2: Check Gemini API endpoint
echo "\nTest 2: Checking Gemini API endpoint...\n";
$ch = curl_init("https://generativelanguage.googleapis.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode > 0) {
    echo "✅ Can reach Google APIs (HTTP $httpCode)\n";
} else {
    echo "❌ Cannot reach Google APIs: $curlError\n";
}

// Test 3: Check DNS resolution
echo "\nTest 3: Checking DNS resolution...\n";
$host = "generativelanguage.googleapis.com";
$ip = gethostbyname($host);
if ($ip != $host) {
    echo "✅ DNS resolved: $host -> $ip\n";
} else {
    echo "❌ DNS resolution failed for $host\n";
}

echo "\n===========================================\n";
?>