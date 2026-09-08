<?php
/**
 * config/config.php - Gemini API Configuration
 */

// ============================================
// Gemini API Configuration
// ============================================

// 🔑 YOUR NEW API KEY (AQ. format - this is correct)
define("GEMINI_API_KEY", "AQ.Ab8RN6JKtp92eugGZSPa_QNIaTXU3tVBxL-rKR_41qJ9XRzWQw");

// ✅ CORRECT API URL (using v1beta)
define("GEMINI_API_URL", "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent");

// ✅ Model name
define("GEMINI_MODEL", "gemini-3.6-flash");
// Add this after your other definitions
define("API_TYPE", "gemini");

// ============================================
// Application Settings
// ============================================
// Application Settings
// Application Settings
define("MAX_QUESTIONS", 20);
define("MIN_QUESTIONS", 1);
define("API_TIMEOUT", 120);        // Increased to 120 seconds
if (!defined('CONNECTION_TIMEOUT')) {
    define("CONNECTION_TIMEOUT", 60);
}

// ============================================
// Environment
// ============================================

define("IS_DEVELOPMENT", true);

if (IS_DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// Security
// ============================================

if (basename($_SERVER['PHP_SELF']) == 'config.php') {
    die('Direct access not permitted.');
}
?>