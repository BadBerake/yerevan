<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/Database.php';

$config = require __DIR__ . '/../src/config.php';
$db = new Database($config['db']);

$email = 'rezamirmhrabi@proton.me';
$password = 'wolf2013@!';
$username = 'rezamirmhrabi'; // Derived or default

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = $db->getPdo();
    
    // Check if user exists by email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin' WHERE email = ?");
        $stmt->execute([$hash, $email]);
        echo "<h1>Success</h1><p>User <strong>$email</strong> updated to ADMIN successfully.</p>";
    } else {
        // Check if username exists to avoid collision
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $username = $username . '_' . time(); // Unique fallback
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$username, $email, $hash]);
        echo "<h1>Success</h1><p>Admin user <strong>$email</strong> created successfully.</p>";
    }
    
    echo "<p><a href='/admin/login'>Go to Admin Login</a></p>";

} catch (PDOException $e) {
    echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
}
