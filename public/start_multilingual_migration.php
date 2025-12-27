<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Update items table
    $pdo->exec("
        ALTER TABLE items 
        ADD COLUMN IF NOT EXISTS title_translations JSONB DEFAULT '{}',
        ADD COLUMN IF NOT EXISTS description_translations JSONB DEFAULT '{}';
    ");

    // 2. Update tour_routes table
    $pdo->exec("
        ALTER TABLE tour_routes 
        ADD COLUMN IF NOT EXISTS name_translations JSONB DEFAULT '{}',
        ADD COLUMN IF NOT EXISTS description_translations JSONB DEFAULT '{}';
    ");

    // 3. Update route_stops table
    $pdo->exec("
        ALTER TABLE route_stops 
        ADD COLUMN IF NOT EXISTS note_translations JSONB DEFAULT '{}';
    ");

    echo "✓ Added multi-language JSONB columns to items, tour_routes, and route_stops successfully!<br>";
    echo "<br>Migration Complete. <a href='/admin/items'>Manage Items</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
