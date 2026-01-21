<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/config.php';

$config = require __DIR__ . '/../src/config.php';
$dsn = "pgsql:host={$config['db']['host']};port=5432;dbname={$config['db']['dbname']};";
$pdo = new PDO($dsn, $config['db']['user'], $config['db']['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Connected to DB.<br>";

// 1. Check if column exists
echo "Checking columns in 'users' table...<br>";
$stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users'");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$exists = false;
foreach ($columns as $col) {
    echo " - " . $col['column_name'] . " (" . $col['data_type'] . ")<br>";
    if ($col['column_name'] === 'is_verified') {
        $exists = true;
    }
}

if ($exists) {
    echo "Column 'is_verified' ALREADY EXISTS.<br>";
} else {
    echo "Column 'is_verified' DOES NOT EXIST. Attempting to create...<br>";
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_verified BOOLEAN DEFAULT FALSE");
        echo "SUCCESS: Column added.<br>";
    } catch (Exception $e) {
        echo "ERROR adding column: " . $e->getMessage() . "<br>";
    }
}
