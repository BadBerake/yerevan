<?php
// Load config
$config = require __DIR__ . '/../src/config.php';
$db = $config['db'];

try {
    $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Communities Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS communities (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            creator_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            image_url VARCHAR(255),
            status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'disabled')),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // 2. Community Members Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS community_members (
            community_id INTEGER NOT NULL REFERENCES communities(id) ON DELETE CASCADE,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (community_id, user_id)
        );
    ");

    // 3. Messages Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id SERIAL PRIMARY KEY,
            community_id INTEGER NOT NULL REFERENCES communities(id) ON DELETE CASCADE,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            message_text TEXT,
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    echo "✓ Social Network database tables created successfully!<br>";
    echo "<br>Migration Complete. <a href='/communities'>Discover Communities</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
