<?php
// Load config
$config = require __DIR__ . '/src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create item_images table
    $sql = "
    CREATE TABLE IF NOT EXISTS item_images (
        id SERIAL PRIMARY KEY,
        item_id INTEGER REFERENCES items(id) ON DELETE CASCADE,
        image_url VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    
    $pdo->exec($sql);
    echo "Migration Successful: item_images table created.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
