<?php
// Bypass PDO/Database wrapper for raw execution to ensure boolean literal is sent
$config = require __DIR__ . '/../src/config.php';
$dsn = "pgsql:host={$config['db']['host']};port=5432;dbname={$config['db']['dbname']};";
$pdo = new PDO($dsn, $config['db']['user'], $config['db']['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_verified BOOLEAN DEFAULT FALSE");
    echo "Success.";
} catch (Exception $e) {
    echo $e->getMessage();
}
