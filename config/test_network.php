<?php
/**
 * test_network.php - Network Diagnostic Tool
 * Helps identify connection issues
 */

echo "===========================================\n";
echo "     NETWORK DIAGNOSTIC TOOL\n";
echo "===========================================\n\n";

// Test 1: Check internet connection
echo "📌 Test 1: Checking internet connection...\n";
$google = @fsockopen("google.com", 80, $errno, $errstr, 5);
if ($google) {
    echo "✅ Internet connection is working\n";
    fclose($google);
} else {
    echo "❌ Cannot connect to internet! Please check your network.\n";
}
echo "\n";

// Test 2: Check DNS resolution
echo "📌 Test 2: Checking DNS resolution for Google APIs...\n";
$host = "generativelanguage.googleapis.com";
$ip = gethostbyname($host);
if ($ip != $host) {
    echo "✅ DNS resolution successful: $host -> $ip\n";
} else {
    echo "❌ Cannot resolve DNS for $host. Check your DNS settings.\n";
}
echo "\n";

// Test 3: Check if port 443 is open (HTTPS)
echo "📌 Test 3: Checking HTTPS connection to Google APIs...\n";
$fp = @fsockopen("ssl://generativelanguage.googleapis.com", 443, $errno, $errstr, 10);
if ($fp) {
    echo "✅ Can connect to Google APIs on port 443 (HTTPS)\n";
    fclose($fp);
} else {
    echo "❌ Cannot connect to Google APIs on port 443\n";
    echo "   Error: $errstr ($errno)\n";
    echo "   This could be due to:\n";
    echo "   - Firewall blocking port 443\n";
    echo "   - Proxy settings\n";
    echo "   - Corporate network restrictions\n";
}
echo "\n";

// Test 4: Check cURL
echo "📌 Test 4: Checking cURL configuration...\n";
if (function_exists('curl_version')) {
    $version = curl_version();
    echo "✅ cURL is installed\n";
    echo "   Version: " . $version['version'] . "\n";
    echo "   SSL Version: " . $version['ssl_version'] . "\n";
    echo "   Protocols: " . implode(", ", $version['protocols']) . "\n";
    
    // Check SSL support
    if (in_array('https', $version['protocols'])) {
        echo "✅ HTTPS protocol is supported\n";
    } else {
        echo "❌ HTTPS protocol is NOT supported\n";
    }
} else {
    echo "❌ cURL is NOT installed!\n";
    echo "   Install php-curl: sudo apt-get install php-curl (Linux) or enable in php.ini (Windows)\n";
}
echo "\n";

// Test 5: Test connection with cURL
echo "📌 Test 5: Testing actual connection to Google API...\n";
$ch = curl_init("https://generativelanguage.googleapis.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode > 0) {
    echo "✅ Successfully connected to Google API (HTTP $httpCode)\n";
} else {
    echo "❌ Failed to connect to Google API\n";
    echo "   Error: $curlError\n";
}
echo "\n";

// Test 6: Check PHP configuration
echo "📌 Test 6: Checking PHP configuration...\n";
echo "   PHP Version: " . phpversion() . "\n";
echo "   Max Execution Time: " . ini_get('max_execution_time') . " seconds\n";
echo "   Memory Limit: " . ini_get('memory_limit') . "\n";
echo "   Allow URL fopen: " . (ini_get('allow_url_fopen') ? "Enabled" : "Disabled") . "\n";

echo "\n===========================================\n";
echo "     DIAGNOSTIC COMPLETE\n";
echo "===========================================\n";
?>