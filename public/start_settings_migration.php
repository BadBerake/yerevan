<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key VARCHAR(50) PRIMARY KEY,
            value TEXT,
            category VARCHAR(30) DEFAULT 'general',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $initialSettings = [
        ['footer_email', 'info@yerevango.am', 'footer'],
        ['footer_phone', '+374 10 123456', 'footer'],
        ['footer_address', 'Mashtots Ave 1, Yerevan', 'footer'],
        ['footer_about', 'Yerevango is your ultimate guide to discovering the best of Yerevan.', 'footer'],
        ['social_instagram', 'https://instagram.com/yerevango', 'social'],
        ['social_facebook', 'https://facebook.com/yerevango', 'social'],
        ['social_whatsapp', 'https://wa.me/37410123456', 'social']
    ];

    $stmt = $pdo->prepare("INSERT INTO settings (key, value, category) VALUES (?, ?, ?) ON CONFLICT (key) DO NOTHING");
    foreach ($initialSettings as $s) {
        $stmt->execute($s);
    }
    
    echo "✓ Created settings table and seeded initial values successfully!<br>";
    echo "<br>Migration Complete. You can now <a href='/admin/settings'>go to settings</a>.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
