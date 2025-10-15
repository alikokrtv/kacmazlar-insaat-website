<?php
/**
 * Railway SQLite Database Setup Script (No Volume)
 * Bu script Railway'de ilk deployment'ta çalıştırılmalı
 */

echo "Railway SQLite Database Setup Starting...\n";

try {
    // SQLite database path (no volume needed)
    $dbFile = __DIR__ . '/data/kacmazlar.db';
    $dbDir = dirname($dbFile);
    
    // Ensure directory exists
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
        echo "Created directory: $dbDir\n";
    }
    
    // Check if database already exists
    if (file_exists($dbFile)) {
        echo "Database already exists: $dbFile\n";
        echo "Setup completed!\n";
        exit;
    }
    
    // Create database connection
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "SQLite database created: $dbFile\n";
    
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
