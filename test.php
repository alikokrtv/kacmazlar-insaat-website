<?php
// Simple test page for Railway
echo "PHP is working!<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "SQLite available: " . (extension_loaded('pdo_sqlite') ? 'Yes' : 'No') . "<br>";

// Test SQLite
try {
    $pdo = new PDO('sqlite::memory:');
    echo "SQLite connection: OK<br>";
} catch (Exception $e) {
    echo "SQLite error: " . $e->getMessage() . "<br>";
}

// Test file permissions
echo "Data directory exists: " . (is_dir('/app/data') ? 'Yes' : 'No') . "<br>";
echo "Data directory writable: " . (is_writable('/app/data') ? 'Yes' : 'No') . "<br>";

phpinfo();
?>
