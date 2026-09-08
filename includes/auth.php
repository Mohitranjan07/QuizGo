<?php
/**
 * includes/auth.php - Authentication with Database
 */

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ This function must exist!
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserName() {
    return $_SESSION['user_name'] ?? 'User';
}

function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? '';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
}

function logoutUser() {
    session_destroy();
    header('Location: login.php');
    exit();
}

function registerUser($username, $email, $password) {
    global $pdo;
    // ... your registration code (make sure it uses the correct column names)
}

function loginUser($username, $password) {
    global $pdo;
    // ... your login code
}
?>