<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add columns if they don't exist
    $columns = [
        'phone' => 'VARCHAR(50)',
        'instagram' => 'VARCHAR(255)',
        'whatsapp' => 'VARCHAR(50)'
    ];

    foreach ($columns as $col => $type) {
        // Check if column exists
        $stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name='items' AND column_name=?");
        $stmt->execute([$col]);
        
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE items ADD COLUMN $col $type");
            echo "Added column: $col<br>";
        } else {
            echo "Column exists: $col<br>";
        }
    }
    
    echo "Migration Complete.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
