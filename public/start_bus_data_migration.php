<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
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

    // Define Routes Data
    $routes = [
        '100' => [
            'stops' => [
                'Zvartnots Airport',
                'Parakar',
                'Meqenagortsner St.',
                'Argavand',
                'Police Academy',
                'U.S. Embassy',
                'Central Bus Station (Kilikia)',
                'Ararat Brandy Factory',
                'Blue Mosque',
                'Mashtots / Amiryan',
                'France Square (Opera)'
            ],
            'working_hours' => '24/7 (07:00 - 22:00 frequent)',
            'frequency' => 'Every 30-40 mins'
        ],
        '1' => [
            'stops' => [
                'Jrvej',
                'Banavan',
                'Nor Nork 3rd Microdistrict',
                'Gai Statue',
                'Water World',
                'Yerevan Zoo',
                'Heratsi St.',
                'Medical University',
                'France Square',
                'Pak Shuka (Covered Market)',
                'Yerevan Circus',
                'Garegin Nzhdeh Sq.'
            ],
            'working_hours' => '07:00 - 23:00',
            'frequency' => 'Every 10-12 mins'
        ],
        'Metro' => [
            'stops' => [
                'Barekamutyun',
                'Marshal Baghramyan',
                'Yeritasardakan',
                'Republic Square',
                'Zoravar Andranik',
                'David of Sassoun',
                'Gortsaranayin',
                'Shengavit',
                'Garegin Nzhdeh Sq.',
                'Charbakh (Branch)'
            ],
            'working_hours' => '07:00 - 23:00',
            'frequency' => 'Every 5-7 mins'
        ]
    ];

    // FIX: Change 'stops' column from BOOLEAN to TEXT
    // We check if it needs changing or blindly try to change it.
    // Ideally we drop and re-add or alter.
    // For SQLite and PG compatibility:
    
    // Check if it's integer/boolean (SQLite doesn't have strict boolean, but PG does)
    // Let's simple ALTER.
    
    // NUCLEAR OPTION: Drop and Recreate Table
    // We utilize a NEW table name to avoid any ghost schema issues.
    
    $pdo->exec("DROP TABLE IF EXISTS bus_routes_v2");
    
    // Check driver for ID type
    $isSqlite = isset($db['driver']) && $db['driver'] === 'sqlite';
    $idType = $isSqlite ? "INTEGER PRIMARY KEY AUTOINCREMENT" : "SERIAL PRIMARY KEY";
    
    $pdo->exec("
        CREATE TABLE bus_routes_v2 (
            id $idType,
            route_number VARCHAR(50) NOT NULL,
            type VARCHAR(50) NOT NULL,
            origin VARCHAR(100) NOT NULL,
            destination VARCHAR(100) NOT NULL,
            stops TEXT, 
            working_hours VARCHAR(100),
            frequency VARCHAR(50),
            price VARCHAR(50),
            color VARCHAR(20) DEFAULT '#000000',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $stmt = $pdo->prepare("INSERT INTO bus_routes_v2 (route_number, type, origin, destination, working_hours, frequency, price, color, stops) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Need to reconstitute the full rows since we dropped the table
    // We merge our detailed data with the base data
    
    $baseRoutes = [
        '100' => ['bus', 'France Square', 'Zvartnots Airport', '300 AMD', '#3b82f6'],
        '1' => ['bus', 'Ajapnyak', 'Jrvej', '100 AMD', '#ef4444'],
        'Metro' => ['metro', 'Barekamutyun', 'Garegin Nzhdeh Sq.', '100 AMD', '#f59e0b'],
        // Add others if we want to keep them, e.g. 2, 99
    ];

    foreach ($routes as $number => $details) {
        if (!isset($baseRoutes[$number])) continue;
        
        $base = $baseRoutes[$number];
        $jsonStops = json_encode($details['stops']);
        
        $stmt->execute([
            $number,
            $base[0], // type
            $base[1], // origin
            $base[2], // dest
            $details['working_hours'],
            $details['frequency'],
            $base[3], // price
            $base[4], // color
            $jsonStops
        ]);
    }
    
    // Add remaining routes that we didn't update (if any)? 
    // For now we only care about the ones we have data for.

    echo "✓ Bus routes table RECREATED and seeded with accurate data!<br>";
    echo "<br><a href='/transport'>Go to Transport Page</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
