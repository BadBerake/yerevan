<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if column exists
    $stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name='items' AND column_name='opening_hours'");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE items ADD COLUMN opening_hours VARCHAR(100)");
        echo "Added column: opening_hours<br>";
    } else {
        echo "Column exists: opening_hours<br>";
    }
    
    echo "Migration Complete.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
