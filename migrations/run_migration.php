<?php
require_once __DIR__ . '/../src/Database.php';
$config = require __DIR__ . '/../src/config.php';
$db = new Database($config['db']);

$sql = file_get_contents(__DIR__ . '/20260123_add_tours_table.sql');

try {
    $db->query($sql);
    echo "Migration successful: tours table created.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
