<?php
/**
 * config/database.php - Database Connection
 */

// Database configuration
$host = 'localhost';
$dbname = 'ai_quiz_builder';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Uncomment to test connection
    // echo "✅ Database connected successfully!";
} catch(PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>