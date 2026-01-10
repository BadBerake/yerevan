<?php

require_once __DIR__ . '/src/Database.php';
$config = require __DIR__ . '/src/config.php';

try {
    $db = new Database($config['db']);
    $pdo = $db->getPdo();

    echo "🔌 Connected to database.\n";

    // 1. Get or Create User
    $stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
    $user = $stmt->fetch();
    
    if (!$user) {
        // Create a default admin user if none exists
        echo "👤 No users found. Creating default admin...\n";
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', password_hash('admin', PASSWORD_DEFAULT), 'admin']);
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $user['id'];
    }
    echo "✅ Using User ID: $userId\n";

    // 2. Get or Create Category
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $stmt->execute(['cafes']);
    $category = $stmt->fetch();

    if (!$category) {
        echo "📂 Category 'Cafes' not found. Creating...\n";
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, type) VALUES (?, ?, ?)");
        $stmt->execute(['Cafes', 'cafes', 'place']);
        $categoryId = $pdo->lastInsertId();
    } else {
        $categoryId = $category['id'];
    }
    echo "✅ Using Category ID: $categoryId\n";

    // 3. Read JSON Data
    $jsonFile = __DIR__ . '/yerevan_cafes_selenium.json';
    if (!file_exists($jsonFile)) {
        die("❌ JSON file not found: $jsonFile\n");
    }

    $jsonData = file_get_contents($jsonFile);
    $cafes = json_decode($jsonData, true);

    if (!$cafes) {
        die("❌ Failed to decode JSON.\n");
    }

    echo "📦 Found " . count($cafes) . " cafes in JSON.\n";

    // 4. Import Items
    $insertedCount = 0;
    $skippedCount = 0;

    $stmtInsert = $pdo->prepare("
        INSERT INTO items (user_id, category_id, title, slug, description, address, is_approved) 
        VALUES (:user_id, :category_id, :title, :slug, :description, :address, :is_approved)
    ");

    $stmtCheck = $pdo->prepare("SELECT id FROM items WHERE slug = ?");

    foreach ($cafes as $cafe) {
        $name = trim($cafe['name']);
        if (empty($name)) continue;

        // Generate Slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (true) {
            $stmtCheck->execute([$slug]);
            if (!$stmtCheck->fetch()) {
                break;
            }
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Build Description
        $description = "";
        if (!empty($cafe['phone'])) $description .= "Phone: {$cafe['phone']}\n";
        if (!empty($cafe['working_hours'])) $description .= "Hours: {$cafe['working_hours']}\n";
        if (!empty($cafe['website'])) $description .= "Website: {$cafe['website']}\n";
        if (!empty($cafe['url'])) $description .= "2GIS Link: {$cafe['url']}\n";
        
        // Address
        $address = !empty($cafe['address']) ? $cafe['address'] : 'Yerevan';

        try {
            $stmtInsert->execute([
                ':user_id' => $userId,
                ':category_id' => $categoryId,
                ':title' => $name,
                ':slug' => $slug,
                ':description' => trim($description),
                ':address' => $address,
                ':is_approved' => 'true'
            ]);
            $insertedCount++;
            echo "   ➕ Imported: $name\n";
        } catch (PDOException $e) {
            echo "   ❌ Failed to import $name: " . $e->getMessage() . "\n";
            $skippedCount++;
        }
    }

    echo "\n========================================\n";
    echo "✅ Import Complete!\n";
    echo "📊 Inserted: $insertedCount\n";
    echo "⏭️  Skipped: $skippedCount\n";
    echo "========================================\n";

} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
