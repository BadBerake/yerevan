<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    // Determine DSN based on config type (PostgreSQL vs SQLite)
    // The checking logic matches Database.php
    if (isset($db['driver']) && $db['driver'] === 'sqlite') {
        $dsn = "sqlite:" . $db['path'];
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } else {
        $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s;", 
            $db['host'], $db['port'], $db['dbname']
        );
        $pdo = new PDO($dsn, $db['user'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    // Bus Routes Table
    // Using generic SQL compatible with both PG and SQLite (except SERIAL/AUTOINCREMENT)
    
    // Check driver first to use correct auto-increment syntax
    $isSqlite = isset($db['driver']) && $db['driver'] === 'sqlite';
    $idType = $isSqlite ? "INTEGER PRIMARY KEY AUTOINCREMENT" : "SERIAL PRIMARY KEY";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bus_routes (
            id $idType,
            route_number VARCHAR(50) NOT NULL,
            type VARCHAR(50) NOT NULL, -- bus, minibus, metro, trolleybus
            origin VARCHAR(100) NOT NULL,
            destination VARCHAR(100) NOT NULL,
            stops BOOLean DEFAULT FALSE, -- is complete list? simplified
            working_hours VARCHAR(100),
            frequency VARCHAR(50),
            price VARCHAR(50),
            color VARCHAR(20) DEFAULT '#000000',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Seed Data
    $routes = [
        ['100', 'bus', 'France Square', 'Zvartnots Airport', '07:00 - 22:00', 'Every 10-15 mins', '300 AMD', '#3b82f6'],
        ['1', 'bus', 'Ajapnyak', 'Jrvej', '06:30 - 23:00', 'Every 10 mins', '100 AMD', '#ef4444'],
        ['Metro', 'metro', 'Barekamutyun', 'Garegin Nzhdeh Sq.', '07:00 - 23:00', 'Every 5-7 mins', '100 AMD', '#f59e0b'],
        ['2', 'trolleybus', 'Klavdiya Shulzhenko St', 'Museum of Erebuni', '07:00 - 21:00', 'Every 15-20 mins', '50 AMD', '#10b981'],
        ['99', 'minibus', 'Babajanyan St', 'Zeytun', '07:00 - 22:00', 'Every 10 mins', '100 AMD', '#6366f1']
    ];

    $stmt = $pdo->prepare("INSERT INTO bus_routes (route_number, type, origin, destination, working_hours, frequency, price, color) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($routes as $route) {
        // Check if exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM bus_routes WHERE route_number = ?");
        $check->execute([$route[0]]);
        if ($check->fetchColumn() == 0) {
            $stmt->execute($route);
        }
    }

    echo "✓ Created bus_routes table and seeded data successfully!<br>";
    echo "<br>Migration Complete. <a href='/transport'>Go to Transport Page</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
