<?php

require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/Database.php';

$config = require __DIR__ . '/../src/config.php';

try {
    $db = new Database($config['db']);
    echo "✅ Database connection successful!\n\n";
    
    // Test query
    $result = $db->query("SELECT * FROM categories");
    $categories = $result->fetchAll();
    
    echo "📊 Categories in database:\n";
    foreach ($categories as $cat) {
        echo "  - {$cat['name']} ({$cat['slug']}) - Type: {$cat['type']}\n";
    }
    
    echo "\n✅ All tests passed!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
