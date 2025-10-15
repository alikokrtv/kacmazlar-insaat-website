<?php
/**
 * Railway Database Setup Script
 * Bu script Railway'de ilk deployment'ta çalıştırılmalı
 */

// Railway environment variables kontrolü
if (!isset($_ENV['DB_HOST'])) {
    die('Railway environment variables not set. Please configure database connection.');
}

echo "Railway Database Setup Starting...\n";

try {
    // Database connection
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful!\n";
    
    // Check if tables exist
    $tables = ['admins', 'projects', 'blog_posts', 'blog_categories', 'contact_messages', 'site_settings'];
    $existingTables = [];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existingTables[] = $table;
        }
    }
    
    if (count($existingTables) > 0) {
        echo "Database tables already exist: " . implode(', ', $existingTables) . "\n";
        echo "Setup completed!\n";
        exit;
    }
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("database.sql file not found!");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Database setup completed successfully!\n";
    echo "Admin credentials: admin / admin123\n";
    echo "Please change the default password after first login.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
