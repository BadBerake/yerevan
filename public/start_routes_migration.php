<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Tour Routes Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tour_routes (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            interest_tag VARCHAR(50), -- Mapping to user interests (e.g., 'cafes', 'outdoor')
            estimated_time VARCHAR(50), -- e.g., '2-3 hours'
            difficulty VARCHAR(20) DEFAULT 'medium', -- easy, medium, hard
            image_url TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // 2. Route Stops Table (Many-to-Many with items, with ordering)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS route_stops (
            id SERIAL PRIMARY KEY,
            route_id INTEGER REFERENCES tour_routes(id) ON DELETE CASCADE,
            item_id INTEGER REFERENCES items(id) ON DELETE CASCADE,
            order_index INTEGER NOT NULL,
            stop_note TEXT, -- Optional tip for this specific stop
            UNIQUE(route_id, order_index)
        );
    ");

    echo "✓ Created tour_routes and route_stops tables successfully!<br>";
    echo "<br>Migration Complete. <a href='/admin/routes'>Manage Routes</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
