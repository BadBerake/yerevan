<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';

$config = require __DIR__ . '/config.php';
$db = new Database($config['db']);

$username = 'rezamirmhrabi';
$password = 'wolf2013@!';
$email = 'rezamirmhrabi@admin.com'; // Generated email to satisfy constraint

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = $db->getPdo();
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin' WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "User '$username' updated to Admin successfully.\n";
    } else {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$username, $email, $hash]);
        echo "Admin user '$username' created successfully.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
