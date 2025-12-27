<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add status column if it doesn't exist
    $pdo->exec("
        DO $$ 
        BEGIN 
            IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='users' AND column_name='status') THEN
                ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'disabled'));
            END IF;
        END $$;
    ");

    echo "✓ Added 'status' column to users table successfully!<br>";
    echo "<br>Migration Complete. <a href='/admin/users'>Back to User Management</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
