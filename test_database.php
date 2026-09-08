<?php
require_once __DIR__ . '/config/database.php';

echo "===========================================\n";
echo "     DATABASE CONNECTION TEST\n";
echo "===========================================\n\n";

// Test connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection successful!\n";
    echo "Database: ai_quiz_builder\n";
    
    // Show tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "\nTables found:\n";
    foreach ($tables as $row) {
        echo "  - " . implode(', ', $row) . "\n";
    }
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n===========================================\n";
?>