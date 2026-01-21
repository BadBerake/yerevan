<?php
$config = require __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/Database.php';

try {
    $db = new Database($config['db']);
    $pdo = $db->getPdo();
    
    // Check if column exists
    // For SQLite, we can use PRAGMA. For Postgres, we query information_schema or just catch the exception on add.
    // The previous script used PRAGMA which suggests SQLite, but the config says port 5432 (Postgres).
    // Let's assume Postgres based on config.
    
    // Attempt to add column, catch if exists
    try {
        echo "Attempting to add avatar_url column...<br>";
        $pdo->exec("ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255) DEFAULT NULL");
        echo "Column added successfully!<br>";
    } catch (PDOException $e) {
        // Postgres error code 42701 is duplicate column
        if ($e->getCode() == '42701' || strpos($e->getMessage(), 'types do not match') !== false || strpos($e->getMessage(), 'already exists') !== false) {
             echo "Column avatar_url already exists (or error ignored).<br>";
        } else {
             throw $e;
        }
    }
    
    echo "Migration completed.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
