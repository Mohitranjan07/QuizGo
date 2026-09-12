<?php
/**
 * includes/auth.php - Authentication with Database
 * 
 * Uses: name, email, password columns in users table
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Register a new user
 * 
 * @param string $name User's full name or username
 * @param string $email User's email address
 * @param string $password User's password (plain text, will be hashed)
 * @return array ['success' => bool, 'user_id' => int|string|null, 'error' => string|null]
 */
function registerUser($name, $email, $password) {
    global $pdo;
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'All fields are required.'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email address.'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Password must be at least 6 characters.'];
    }
    
    try {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE name = ? OR email = ?");
        $stmt->execute([$name, $email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Username or email already exists.'];
        }
        
        // Hash password and insert user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        
        return [
            'success' => true,
            'user_id' => $pdo->lastInsertId(),
            'error' => null
        ];
        
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Login user
 * 
 * @param string $username Username or email
 * @param string $password Plain text password
 * @return array ['success' => bool, 'user' => array|null, 'error' => string|null]
 */
function loginUser($username, $password) {
    global $pdo;
    
    if (empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Username and password are required.'];
    }
    
    try {
        // Find user by name or email
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE name = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        return [
            'success' => true,
            'user' => $user,
            'error' => null
        ];
        
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 * 
 * @return string
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? 'User';
}

/**
 * Get current user email
 * 
 * @return string
 */
function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? '';
}

/**
 * Get full user data by ID
 * 
 * @param int $userId
 * @return array|null
 */
function getUserById($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Require login - redirect to login page if not logged in
 * 
 * @param string $redirect Optional redirect URL after login
 */
function requireLogin($redirect = null) {
    if (!isLoggedIn()) {
        $url = 'login.php';
        if ($redirect) {
            $url .= '?redirect=' . urlencode($redirect);
        }
        header('Location: ' . $url);
        exit();
    }
}

/**
 * Logout user - destroy session and redirect to login
 */
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    
    // Redirect to login
    header('Location: login.php');
    exit();
}
?>