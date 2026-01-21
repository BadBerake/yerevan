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
        ],
        '2' => [
            'stops' => [
                'Racecourse II',
                'Charbakh Town Centre',
                'Mika Stadium',
                'Shengavit',
                'Garegin Nzhdeh Square',
                'Yerevan Circus',
                'City Hall',
                'Central Railway Station',
                'Erebuni Museum'
            ],
            'working_hours' => '07:00 - 21:00',
            'frequency' => 'Every 15-20 mins'
        ],
        '99' => [
            'stops' => [
                'National Oncology Center',
                'Kanaker-Zeytun Medical Center',
                'Victory Park',
                'State Medical University',
                'Yerevan Circus',
                'Kilikia Bus Station',
                'Malatia-Sebastia'
            ],
            'working_hours' => '07:00 - 22:00',
            'frequency' => 'Every 10-12 mins'
        ]
    ];

    // NUCLEAR OPTION: Drop and Recreate Table
    // We utilize a NEW table name to avoid any ghost schema issues.
    
    $pdo->exec("DROP TABLE IF EXISTS bus_routes_final");
    
    // Check driver for ID type
    $isSqlite = isset($db['driver']) && $db['driver'] === 'sqlite';
    $idType = $isSqlite ? "INTEGER PRIMARY KEY AUTOINCREMENT" : "SERIAL PRIMARY KEY";
    
    $pdo->exec("
        CREATE TABLE bus_routes_final (
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

    $stmt = $pdo->prepare("INSERT INTO bus_routes_final (route_number, type, origin, destination, working_hours, frequency, price, color, stops) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Base routes
    $baseRoutes = [
        '100' => ['bus', 'France Square', 'Zvartnots Airport', '300 AMD', '#3b82f6'],
        '1' => ['bus', 'Ajapnyak', 'Jrvej', '100 AMD', '#ef4444'],
        'Metro' => ['metro', 'Barekamutyun', 'Garegin Nzhdeh Sq.', '100 AMD', '#f59e0b'],
        '2' => ['trolleybus', 'Klavdiya Shulzhenko St', 'Museum of Erebuni', '50 AMD', '#10b981'],
        '99' => ['minibus', 'Babajanyan St', 'Zeytun', '100 AMD', '#6366f1']
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

    echo "✓ Bus routes table RECREATED (v3) and seeded with accurate data!<br>";
    echo "<br><a href='/transport'>Go to Transport Page</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
