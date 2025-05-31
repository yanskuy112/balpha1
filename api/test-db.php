<?php
// Test database connection
require_once 'config/database.php';

echo "<h2>Database Connection Test</h2>";

if ($use_database && $pdo) {
    echo "✅ Database connected successfully!<br>";
    
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "📋 Tables found: " . count($tables) . "<br>";
        
        foreach ($tables as $table) {
            echo "- " . implode(', ', $table) . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error checking tables: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Database connection failed. Using session storage.<br>";
}

echo "<br><strong>Environment Variables:</strong><br>";
echo "DB_HOST: " . (getenv('DB_HOST') ? '✅ Set' : '❌ Not set') . "<br>";
echo "DB_USERNAME: " . (getenv('DB_USERNAME') ? '✅ Set' : '❌ Not set') . "<br>";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '✅ Set' : '❌ Not set') . "<br>";
echo "DB_DATABASE: " . (getenv('DB_DATABASE') ? '✅ Set' : '❌ Not set') . "<br>";

echo "<br><a href='index.php'>← Back to App</a>";
?>