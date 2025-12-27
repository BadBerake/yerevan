<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add interests column if it doesn't exist
    $pdo->exec("
        DO $$ 
        BEGIN 
            IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='users' AND column_name='interests') THEN
                ALTER TABLE users ADD COLUMN interests JSONB DEFAULT '[]';
            END IF;
        END $$;
    ");

    echo "✓ Added 'interests' column to users table successfully!<br>";
    echo "<br>Migration Complete. <a href='/'>Go to Home</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
