<?php

require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Lang.php';

$config = require __DIR__ . '/../src/config.php';
$db = new Database($config['db']);
Lang::init();
$auth = new Auth($db);
$router = new Router();

// Middleware helper to make auth available in views
function view($name, $data = []) {
    global $auth; 
    extract($data);
    require __DIR__ . "/../templates/{$name}.php";
}

// Routes
$router->get('/', function() {
    global $db;
    $stmt = $db->query("SELECT * FROM items WHERE is_approved = TRUE ORDER BY created_at DESC LIMIT 6");
    $featured = $stmt->fetchAll();

    // Fetch featured routes
    $stmt = $db->query("SELECT * FROM tour_routes ORDER BY created_at DESC LIMIT 4");
    $routes = $stmt->fetchAll();

    view('home', [
        'title' => __('site_title'), 
        'featured' => $featured,
        'routes' => $routes
    ]);
});

$router->get('/restaurants', function() {
    global $db;
    $stmt = $db->query("SELECT i.* FROM items i JOIN categories c ON i.category_id = c.id WHERE c.slug = 'restaurants' AND i.is_approved = TRUE");
    $items = $stmt->fetchAll();
    view('listing', ['title' => __('restaurants_title'), 'items' => $items, 'type' => 'restaurant']);
});

$router->get('/cafes', function() {
    global $db;
    $stmt = $db->query("SELECT i.* FROM items i JOIN categories c ON i.category_id = c.id WHERE c.slug = 'cafes' AND i.is_approved = TRUE");
    $items = $stmt->fetchAll();
    view('listing', ['title' => __('cafes_title'), 'items' => $items, 'type' => 'cafe']);
});

$router->get('/events', function() {
    global $db;
    $stmt = $db->query("SELECT i.* FROM items i JOIN categories c ON i.category_id = c.id WHERE c.slug = 'events' AND i.is_approved = TRUE");
    $items = $stmt->fetchAll();
    view('listing', ['title' => __('events_title'), 'items' => $items, 'type' => 'event']);
});

// Search Route
$router->get('/search', function() {
    global $db;
    $query = $_GET['q'] ?? '';
    
    if (empty($query)) {
        header('Location: /');
        exit;
    }
    
    // Search in title, description, and address
    $searchTerm = '%' . $query . '%';
    $stmt = $db->query(
        "SELECT i.*, c.name as category_name 
         FROM items i 
         LEFT JOIN categories c ON i.category_id = c.id 
         WHERE i.is_approved = TRUE 
         AND (i.title ILIKE ? OR i.description ILIKE ? OR i.address ILIKE ?)
         ORDER BY i.created_at DESC",
        [$searchTerm, $searchTerm, $searchTerm]
    );
    $items = $stmt->fetchAll();
    
    view('listing', [
        'title' => __('search_results_title') . ' "' . htmlspecialchars($query) . '"',
        'items' => $items,
        'type' => 'search',
        'query' => $query
    ]);
});

// Auth Routes (GET)
$router->get('/login', function() {
    view('login', ['title' => __('login_title')]);
});

$router->get('/register', function() {
    view('register', ['title' => __('create_account')]);
});

// Auth Routes (Post)
$router->post('/login', function() {
    global $auth;
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->login($email, $password)) {
        header('Location: /dashboard');
        exit;
    } else {
        view('login', ['title' => __('login_title'), 'error' => __('invalid_credentials')]);
    }
});

$router->post('/register', function() {
    global $auth, $db;
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->register($username, $email, $password)) {
        // Auto-login after registration
        if ($auth->login($email, $password)) {
            header('Location: /onboarding');
            exit;
        }
        header('Location: /login?msg=registered');
        exit;
    } else {
        view('register', ['title' => __('create_account'), 'error' => __('registration_failed')]);
    }
});

$router->get('/onboarding', function() {
    global $auth;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    view('onboarding', ['title' => __('onboarding_title')]);
});

$router->post('/onboarding/save', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $interests = $_POST['interests'] ?? [];
    $userId = $auth->getUser()['id'];
    
    $db->query("UPDATE users SET interests = ? WHERE id = ?", [json_encode($interests), $userId]);
    
    header('Location: /dashboard?onboarding=complete');
    exit;
});

// City Exploration Routes
$router->get('/explore', function() {
    global $auth, $db;
    
    // Get user interests if logged in
    $interests = [];
    if ($auth->isLoggedIn()) {
        $user = $auth->getUser();
        $stmt = $db->query("SELECT interests FROM users WHERE id = ?", [$user['id']]);
        $row = $stmt->fetch();
        $interests = json_decode($row['interests'] ?? '[]', true);
    }
    
    // Fetch all routes
    $stmt = $db->query("SELECT * FROM tour_routes ORDER BY created_at DESC");
    $routes = $stmt->fetchAll();
    
    // Split into personalized and others
    $personalized = [];
    $others = [];
    
    foreach ($routes as $route) {
        if (!empty($interests) && in_array($route['interest_tag'], $interests)) {
            $personalized[] = $route;
        } else {
            $others[] = $route;
        }
    }
    
    view('explore', [
        'title' => 'Explore Yerevan - Curated Routes',
        'personalized' => $personalized,
        'others' => $others
    ]);
});

$router->get('/route/{slug}', function($slug) {
    global $db;
    
    $stmt = $db->query("SELECT * FROM tour_routes WHERE slug = ?", [$slug]);
    $route = $stmt->fetch();
    
    if (!$route) { header('Location: /explore'); exit; }
    
    // Get Stops with Place details (lat/lng for map)
    $stmt = $db->query("SELECT rs.*, i.* 
                        FROM route_stops rs 
                        JOIN items i ON rs.item_id = i.id 
                        WHERE rs.route_id = ? 
                        ORDER BY rs.order_index ASC", [$route['id']]);
    $stops = $stmt->fetchAll();
    
    view('route-detail', [
        'title' => $route['name'],
        'route' => $route, 
        'stops' => $stops
    ]);
});

$router->get('/logout', function() {
    global $auth;
    $auth->logout();
    header('Location: /');
    exit;
});

// User Panel
$router->get('/dashboard', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $user = $auth->getUser();
    // Get full user data including interests
    $stmt = $db->query("SELECT * FROM users WHERE id = ?", [$user['id']]);
    $fullUser = $stmt->fetch();
    
    $interests = json_decode($fullUser['interests'] ?? '[]', true);
    $recommendations = [];
    
    if (!empty($interests)) {
        // Build query based on interests
        $placeholders = implode(',', array_fill(0, count($interests), '?'));
        $stmt = $db->query(
            "SELECT i.*, c.name as category_name 
             FROM items i 
             JOIN categories c ON i.category_id = c.id 
             WHERE i.is_approved = TRUE 
             AND c.slug IN ($placeholders)
             ORDER BY RANDOM() LIMIT 4", 
            $interests
        );
        $recommendations = $stmt->fetchAll();
    }
    
    view('dashboard', [
        'title' => 'My Dashboard', 
        'user' => $fullUser, 
        'recommendations' => $recommendations
    ]);
});

$router->get('/add-place', function() {
    global $auth;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    view('add-place', ['title' => 'Add New Place']);
});

$router->post('/add-place', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    // Process form (simplified)
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Insert into items
    $db->query("INSERT INTO items (user_id, category_id, title, slug, description, address, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)", [
        $_SESSION['user_id'], $cat_id, $title, $slug, $_POST['description'], $_POST['address'], $_POST['image_url']
    ]);
    
    header('Location: /dashboard?msg=submitted');
    exit;
});

$router->get('/place/{id}', function($id) {
    global $db;
    
    // Fetch Item
    $stmt = $db->query("SELECT i.*, c.name as category_name, u.username 
                        FROM items i 
                        LEFT JOIN categories c ON i.category_id = c.id 
                        LEFT JOIN users u ON i.user_id = u.id 
                        WHERE i.id = ?", [$id]);
    $item = $stmt->fetch();

    if (!$item) {
        http_response_code(404);
        echo "Place not found";
        return;
    }

    // Fetch Gallery
    $stmtGal = $db->query("SELECT * FROM item_images WHERE item_id = ? ORDER BY created_at DESC", [$id]);
    $gallery = $stmtGal->fetchAll();
    
    // Fetch Reviews
    $stmtRev = $db->query("
        SELECT r.*, u.username 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.item_id = ? 
        ORDER BY r.created_at DESC
    ", [$id]);
    $reviews = $stmtRev->fetchAll();
    
    view('detail', ['title' => $item['title'], 'item' => $item, 'gallery' => $gallery, 'reviews' => $reviews]); 
});

// Map Page
$router->get('/map', function() {
    global $db;
    // Fetch items with coordinates
    $stmt = $db->query("SELECT items.*, categories.slug as category_slug, categories.name as category_name 
                         FROM items 
                         JOIN categories ON items.category_id = categories.id
                         WHERE is_approved = TRUE AND latitude IS NOT NULL");
    $items = $stmt->fetchAll();
    view('map', ['title' => __('map'), 'items' => $items]);
});

// Admin Panel Routes
$router->get('/admin/login', function() {
    global $auth;
    if ($auth->isAdmin()) {
        header('Location: /admin');
        exit;
    }
    require __DIR__ . "/../templates/admin/login.php";
});

$router->post('/admin/login', function() {
    global $auth;
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($auth->login($email, $password) && $auth->isAdmin()) {
        header('Location: /admin');
        exit;
    } else {
        // If login failed or not an admin, logout effectively (to clear incomplete session) and redirect back
        $auth->logout();
        header('Location: /admin/login?error=1');
        exit;
    }
});

$router->get('/admin', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) {
        header('Location: /admin/login');
        exit;
    }
    
    // Fetch Real Stats
    $userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $itemCount = $db->query("SELECT COUNT(*) FROM items")->fetchColumn();
    $pendingCount = $db->query("SELECT COUNT(*) FROM items WHERE is_approved = FALSE")->fetchColumn();
    $revenue = $db->query("SELECT COALESCE(SUM(total_price), 0) FROM tickets")->fetchColumn();

    // Fetch Recent Activity (Mixed)
    // For simplicity, just showing latest 3 users
    $recentUsers = $db->query("SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 3")->fetchAll();

    view('admin/dashboard', [
        'title' => 'Admin Dashboard',
        'stats' => [
            'users' => $userCount,
            'items' => $itemCount,
            'pending' => $pendingCount,
            'revenue' => $revenue
        ],
        'recent_activity' => $recentUsers // Simplified for now
    ]);
});

$router->get('/admin/approvals', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) {
        header('Location: /admin/login');
        exit;
    }
    $stmt = $db->query("SELECT i.*, u.username FROM items i JOIN users u ON i.user_id = u.id WHERE i.is_approved = FALSE");
    $pending = $stmt->fetchAll();
    view('admin/approvals', ['title' => 'Content Approvals', 'pending' => $pending]);
});

$router->get('/admin/users', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    view('admin/users', ['title' => 'User Management', 'users' => $users]);
});

$router->post('/admin/users/toggle-status', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $db->query("UPDATE users SET status = ? WHERE id = ?", [$status, $id]);
    header('Location: /admin/users');
    exit;
});

$router->post('/admin/users/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $id = $_POST['id'];
    // Prevent self-deletion
    if ($id == $auth->getUser()['id']) {
        header('Location: /admin/users?error=self_delete');
        exit;
    }
    
    $db->query("DELETE FROM users WHERE id = ?", [$id]);
    header('Location: /admin/users');
    exit;
});

// Static Pages & Forms
$router->get('/about', function() {
    view('about', ['title' => 'About Us']);
});

$router->get('/privacy', function() {
    view('privacy', ['title' => 'Privacy Policy']);
});

$router->get('/terms', function() {
    view('terms', ['title' => 'Terms & Conditions']);
});

$router->get('/contact', function() {
    global $auth;
    view('contact', ['title' => 'Contact Support', 'user' => $auth->getUser()]);
});

$router->post('/contact', function() {
    header('Location: /?msg=ticket_sent');
    exit;
});

$router->post('/admin/approve', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) {
        header('Location: /admin/login');
        exit;
    }
    $id = $_POST['id'];
    $db->query("UPDATE items SET is_approved = TRUE WHERE id = ?", [$id]);
    header('Location: /admin/approvals');
    exit;
});



$router->get('/dashboard/reservations', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    $user = $auth->getUser();
    $stmt = $db->query("SELECT r.*, i.title FROM reservations r JOIN items i ON r.item_id = i.id WHERE r.user_id = ? ORDER BY r.reservation_date DESC", [$user['id']]);
    $reservations = $stmt->fetchAll();
    view('my-reservations', ['title' => 'My Reservations', 'reservations' => $reservations]);
});

$router->get('/dashboard/tickets', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    $user = $auth->getUser();
    $stmt = $db->query("SELECT t.*, i.title, i.event_date FROM tickets t JOIN items i ON t.item_id = i.id WHERE t.user_id = ? ORDER BY t.purchase_date DESC", [$user['id']]);
    $tickets = $stmt->fetchAll();
    view('my-tickets', ['title' => 'My Tickets', 'tickets' => $tickets]);
});

// Admin Settings
$router->get('/admin/settings', function() {
    global $auth;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    view('admin/settings');
});

$router->post('/admin/settings/update', function() {
    // Mock save
    header('Location: /admin/settings?msg=saved');
    exit;
});

// Admin Category Management
$router->get('/admin/categories', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
    view('admin/categories', ['categories' => $stmt->fetchAll()]);
});

$router->get('/admin/categories/new', function() {
    global $auth;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    view('admin/category-form');
});

$router->post('/admin/categories/store', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $db->query("INSERT INTO categories (name, slug, type) VALUES (?, ?, ?)", [$_POST['name'], $_POST['slug'], $_POST['type']]);
    header('Location: /admin/categories');
    exit;
});

$router->get('/admin/categories/edit', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $stmt = $db->query("SELECT * FROM categories WHERE id = ?", [$_GET['id']]);
    view('admin/category-form', ['category' => $stmt->fetch()]);
});

$router->post('/admin/categories/update', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $db->query("UPDATE categories SET name = ?, slug = ?, type = ? WHERE id = ?", [$_POST['name'], $_POST['slug'], $_POST['type'], $_POST['id']]);
    header('Location: /admin/categories');
    exit;
});

$router->post('/admin/categories/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    // Ideally check relations first, but basic delete for now
    try {
        $db->query("DELETE FROM categories WHERE id = ?", [$_POST['id']]);
    } catch (Exception $e) {
        // Ignored for now, likely FK constraint
    }
    header('Location: /admin/categories');
    exit;
});

// Admin Item Management
$router->get('/admin/items', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    // Join with category and user
    $stmt = $db->query("SELECT i.*, c.name as category_name, u.username FROM items i LEFT JOIN categories c ON i.category_id = c.id LEFT JOIN users u ON i.user_id = u.id ORDER BY i.created_at DESC");
    view('admin/items', ['items' => $stmt->fetchAll()]);
});

// Helper for uploads
function uploadFile($file, $targetDir) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return null; // Invalid extension
    }

    $targetDirObj = __DIR__ . '/uploads/' . $targetDir . '/';
    if (!file_exists($targetDirObj)) mkdir($targetDirObj, 0777, true);
    
    $fileName = $targetDir . '_' . time() . '_' . uniqid() . '.' . $ext; // uniform naming
    $targetPath = $targetDirObj . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return '/uploads/' . $targetDir . '/' . $fileName;
    }
    return null;
}

$router->get('/admin/items/new', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    // Get categories for dropdown
    $stmtCat = $db->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmtCat->fetchAll();

    view('admin/item-form', ['categories' => $categories]);
});

$router->post('/admin/items/store', function() {
    global $auth, $db;
    error_log("=== STORE ROUTE HIT ===");
    error_log("POST data: " . print_r($_POST, true));
    
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    try {
        $title = $_POST['title'];
        $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Check for duplicate slug
        $count = $db->query("SELECT COUNT(*) FROM items WHERE slug = ?", [$slug])->fetchColumn();
        if ($count > 0) {
            $slug .= '-' . time();
        }

        // Handle Main Image Upload
        $image_url = $_POST['image_url'] ?? ''; 
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $uploaded = uploadFile($_FILES['main_image'], 'items');
            if ($uploaded) {
                 $image_url = $uploaded;
            }
        }
        
        // Handle amenities
        $amenities = isset($_POST['amenities']) ? json_encode($_POST['amenities']) : '[]';
        $title_translations = json_encode($_POST['title_trans'] ?? []);
        $description_translations = json_encode($_POST['desc_trans'] ?? []);

        $db->query(
            "INSERT INTO items (user_id, category_id, title, slug, address, latitude, longitude, description, image_url, phone, instagram, whatsapp, opening_hours, amenities, title_translations, description_translations, is_approved) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?::jsonb, ?::jsonb, ?::jsonb, TRUE)", 
            [
                $_SESSION['user_id'], 
                $_POST['category_id'], 
                $title, 
                $slug, 
                $_POST['address'] ?? '', 
                $_POST['latitude'] ?: null, 
                $_POST['longitude'] ?: null, 
                $_POST['description'] ?? '', 
                $image_url,
                $_POST['phone'] ?? '', 
                $_POST['instagram'] ?? '', 
                $_POST['whatsapp'] ?? '',
                $_POST['opening_hours'] ?? '',
                $amenities,
                $title_translations,
                $description_translations
            ]
        );
        
        $itemId = $db->getPdo()->lastInsertId();

        // Handle Gallery Uploads
        if (isset($_FILES['gallery'])) {
            $files = $_FILES['gallery'];
            // Re-structure files array
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] == 0) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                    $uploaded = uploadFile($file, 'gallery');
                    if ($uploaded) {
                         $db->query("INSERT INTO item_images (item_id, image_url) VALUES (?, ?)", [$itemId, $uploaded]);
                    }
                }
            }
        }

        header('Location: /admin/items');
        exit;
    } catch (Exception $e) {
        error_log("Error creating item: " . $e->getMessage());
        header('Location: /admin/items/new?error=' . urlencode($e->getMessage()));
        exit;
    }
});

$router->get('/admin/items/edit', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $stmt = $db->query("SELECT * FROM items WHERE id = ?", [$_GET['id']]);
    $item = $stmt->fetch();
    
    // Get categories
    $stmtCat = $db->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmtCat->fetchAll();

    // Get Gallery
    $stmtGal = $db->query("SELECT * FROM item_images WHERE item_id = ?", [$_GET['id']]);
    $gallery = $stmtGal->fetchAll();

    view('admin/item-form', ['item' => $item, 'categories' => $categories, 'gallery' => $gallery]);
});

$router->post('/admin/items/update', function() {
    global $auth, $db;
    error_log("=== UPDATE ROUTE HIT ===");
    error_log("POST data: " . print_r($_POST, true));
    
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    try {
        $id = $_POST['id'];
        $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));

        // Handle Main Image (Update if new file)
        $stmt = $db->query("SELECT image_url FROM items WHERE id = ?", [$id]);
        $currentImage = $stmt->fetchColumn();
        $image_url = $currentImage; // Default keep existing

        // If new file uploaded
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
             $uploaded = uploadFile($_FILES['main_image'], 'items');
             if ($uploaded) {
                 $image_url = $uploaded;
             }
        } 
        // If text input provided (legacy) and no new file
        elseif (!empty($_POST['image_url'])) {
            $image_url = $_POST['image_url']; 
        }

        // Handle amenities
        $amenities = isset($_POST['amenities']) ? json_encode($_POST['amenities']) : '[]';
        $title_translations = json_encode($_POST['title_trans'] ?? []);
        $description_translations = json_encode($_POST['desc_trans'] ?? []);

        $db->query(
            "UPDATE items SET title = ?, category_id = ?, slug = ?, address = ?, latitude = ?, longitude = ?, description = ?, image_url = ?, phone = ?, instagram = ?, whatsapp = ?, opening_hours = ?, amenities = ?::jsonb, title_translations = ?::jsonb, description_translations = ?::jsonb WHERE id = ?", 
            [
                $_POST['title'], 
                $_POST['category_id'], 
                $slug, 
                $_POST['address'] ?? '', 
                $_POST['latitude'] ?: null, 
                $_POST['longitude'] ?: null, 
                $_POST['description'] ?? '', 
                $image_url,
                $_POST['phone'] ?? '', 
                $_POST['instagram'] ?? '', 
                $_POST['whatsapp'] ?? '',
                $_POST['opening_hours'] ?? '',
                $amenities,
                $title_translations,
                $description_translations,
                $id
            ]
        );
        
        // Handle Gallery Uploads (Add new ones)
        if (isset($_FILES['gallery'])) {
            $files = $_FILES['gallery'];
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] == 0) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                    $uploaded = uploadFile($file, 'gallery');
                    if ($uploaded) {
                         $db->query("INSERT INTO item_images (item_id, image_url) VALUES (?, ?)", [$id, $uploaded]);
                    }
                }
            }
        }

        header('Location: /admin/items');
        exit;
    } catch (Exception $e) {
        error_log("Error updating item: " . $e->getMessage());
        header('Location: /admin/items/edit?id=' . $id . '&error=' . urlencode($e->getMessage()));
        exit;
    }
});

// Delete Gallery Image
$router->post('/admin/images/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $imageId = $_POST['image_id'];
    $itemId = $_POST['item_id'];
    
    // Get path to delete file
    $stmt = $db->query("SELECT image_url FROM item_images WHERE id = ?", [$imageId]);
    $path = $stmt->fetchColumn();
    
    // Delete record
    $db->query("DELETE FROM item_images WHERE id = ?", [$imageId]);
    
    // Delete file (Optional, but good practice)
    // $fullPath = __DIR__ . str_replace('/public', '', $path); // Adjust depending on path structure
    // if(file_exists($fullPath)) unlink($fullPath);
    
    header("Location: /admin/items/edit?id=$itemId");
    exit;
});
$router->post('/admin/items/toggle-status', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    $db->query("UPDATE items SET is_approved = ? WHERE id = ?", [$_POST['status'], $_POST['id']]);
    header('Location: /admin/items');
    exit;
});

$router->post('/admin/items/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    try {
        $db->query("DELETE FROM items WHERE id = ?", [$_POST['id']]);
    } catch (Exception $e) {
        // Ignored
    }
    header('Location: /admin/items');
    exit;
});

// Support System Routes
$router->get('/dashboard/support', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    $user = $auth->getUser();
    $stmt = $db->query("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY updated_at DESC", [$user['id']]);
    $tickets = $stmt->fetchAll();
    view('my-support', ['title' => 'My Support Tickets', 'tickets' => $tickets]);
});

$router->get('/dashboard/support/new', function() {
    global $auth;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    view('new-ticket', ['title' => 'New Support Ticket']);
});

$router->post('/dashboard/support/new', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    $user = $auth->getUser();
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Create ticket
    $db->query("INSERT INTO support_tickets (user_id, subject) VALUES (?, ?)", [$user['id'], $subject]);
    $ticketId = $db->getPdo()->lastInsertId();
    
    // Add initial message
    $db->query("INSERT INTO ticket_messages (ticket_id, sender_id, message) VALUES (?, ?, ?)", [$ticketId, $user['id'], $message]);
    
    header('Location: /dashboard/support');
    exit;
});

$router->get('/dashboard/support/1', function() { // Hack for dynamic routing without regex router
     // In a real router, this would be /dashboard/support/{id}
     // For this simple demo router, we'll just handle ID 1 hardcoded or query param would be better
     // But let's try to parse the actual URI in the closure if possible?
     // No, the simple router matches exact strings. 
     // Let's implement a quick hack in index.php to handle this dynamic route or update Router.php
     // Updating Router.php is better, but risky mid-flight.
     // I will use query param for detail view: /dashboard/support/view?id=1
     global $auth, $db;
     if (!isset($_GET['id'])) { header('Location: /dashboard/support'); exit; }
     $id = $_GET['id'];
     
     if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
     
     $stmt = $db->query("SELECT * FROM support_tickets WHERE id = ?", [$id]);
     $ticket = $stmt->fetch();
     
     // basic auth check
     if ($ticket['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
         header('Location: /dashboard/support'); exit;
     }

     $stmt = $db->query("SELECT * FROM ticket_messages WHERE ticket_id = ? ORDER BY created_at ASC", [$id]);
     $messages = $stmt->fetchAll();
     
     view('ticket-detail', ['title' => 'Ticket #' . $id, 'ticket' => $ticket, 'messages' => $messages, 'user' => $auth->getUser()]);
});

// Since the router is simple map, I need to register the specific view route
// Let's change the pattern in my-support.php to use ?id=X
// And register:
$router->get('/dashboard/support/view', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $id = $_GET['id'] ?? 0;
    $stmt = $db->query("SELECT * FROM support_tickets WHERE id = ?", [$id]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) { header('Location: /dashboard/support'); exit; }

    $stmt = $db->query("SELECT * FROM ticket_messages WHERE ticket_id = ? ORDER BY created_at ASC", [$id]);
    $messages = $stmt->fetchAll();
    
    view('ticket-detail', ['title' => 'Ticket #' . $id, 'ticket' => $ticket, 'messages' => $messages, 'user' => $auth->getUser()]);
});

$router->post('/dashboard/support/reply', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $ticket_id = $_POST['ticket_id'];
    $message = $_POST['message'];
    $user = $auth->getUser();

    $db->query("INSERT INTO ticket_messages (ticket_id, sender_id, message) VALUES (?, ?, ?)", [$ticket_id, $user['id'], $message]);
    $db->query("UPDATE support_tickets SET updated_at = NOW(), status = 'open' WHERE id = ?", [$ticket_id]);
    
    header("Location: /dashboard/support/view?id=$ticket_id");
    exit;
});

// Reservations & Tickets (POST)
// Reservation System Submit
$router->post('/reservations/submit', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    $user = $auth->getUser();
    $item_id = $_POST['item_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = intval($_POST['guests']);
    $special_requests = trim($_POST['special_requests'] ?? '');
    
    // Validate date is not in the past
    if (strtotime($date) < strtotime('today')) {
        header('Location: /place/' . $item_id . '?error=invalid_date');
        exit;
    }
    
    $db->query(
        "INSERT INTO reservations (item_id, user_id, name, email, phone, reservation_date, reservation_time, guests, special_requests, status) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
        [$item_id, $user['id'], $name, $email, $phone, $date, $time, $guests, $special_requests]
    );
    
    header('Location: /place/' . $item_id . '?msg=reservation_submitted');
    exit;
});

$router->post('/buy-ticket', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price']; 
    $total = $quantity * $price;
    
    $db->query("INSERT INTO tickets (user_id, item_id, quantity, total_price) VALUES (?, ?, ?, ?)", [
        $_SESSION['user_id'], $item_id, $quantity, $total
    ]);
    
    header('Location: /dashboard?msg=ticket_bought');
    exit;
});

// Reviews System
$router->post('/reviews/submit', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    $user = $auth->getUser();
    $item_id = $_POST['item_id'];
    $rating = max(1, min(5, intval($_POST['rating']))); // Ensure 1-5
    $comment = trim($_POST['comment']);
    
    if (empty($comment)) {
        header('Location: /place/' . $item_id . '?error=comment_required');
        exit;
    }
    
    $db->query(
        "INSERT INTO reviews (item_id, user_id, rating, comment) VALUES (?, ?, ?, ?)",
        [$item_id, $user['id'], $rating, $comment]
    );
    
    header('Location: /place/' . $item_id . '?msg=review_submitted');
    exit;
});

// Admin Reviews Management
$router->get('/admin/reviews', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $stmt = $db->query("
        SELECT r.*, i.title as item_title, u.username 
        FROM reviews r 
        JOIN items i ON r.item_id = i.id 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC
    ");
    $reviews = $stmt->fetchAll();
    view('admin/reviews', ['reviews' => $reviews]);
});

$router->post('/admin/reviews/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $db->query("DELETE FROM reviews WHERE id = ?", [$_POST['id']]);
    header('Location: /admin/reviews');
    exit;
});

// (Moved above to avoid duplication)

// User Reservations Dashboard
$router->get('/my-reservations', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    $user = $auth->getUser();
    $stmt = $db->query(
        "SELECT r.*, i.title as item_title, i.image_url as item_image 
         FROM reservations r 
         JOIN items i ON r.item_id = i.id 
         WHERE r.user_id = ? 
         ORDER BY r.reservation_date DESC, r.reservation_time DESC",
        [$user['id']]
    );
    $reservations = $stmt->fetchAll();
    
    view('my-reservations', ['reservations' => $reservations]);
});

// Cancel Reservation (User)
$router->post('/reservations/cancel', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    $user = $auth->getUser();
    $id = $_POST['id'];
    
    // Verify ownership
    $stmt = $db->query("SELECT user_id FROM reservations WHERE id = ?", [$id]);
    $reservation = $stmt->fetch();
    
    if ($reservation && $reservation['user_id'] == $user['id']) {
        $db->query("UPDATE reservations SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$id]);
    }
    
    header('Location: /my-reservations');
    exit;
});

// Admin Reservations Management
$router->get('/admin/reservations', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $filter = $_GET['status'] ?? 'all';
    
    $sql = "SELECT r.*, i.title as item_title, u.username 
            FROM reservations r 
            JOIN items i ON r.item_id = i.id 
            JOIN users u ON r.user_id = u.id";
    
    if ($filter !== 'all') {
        $sql .= " WHERE r.status = ?";
        $stmt = $db->query($sql . " ORDER BY r.reservation_date DESC, r.reservation_time DESC", [$filter]);
    } else {
        $stmt = $db->query($sql . " ORDER BY r.reservation_date DESC, r.reservation_time DESC");
    }
    
    $reservations = $stmt->fetchAll();
    view('admin/reservations', ['reservations' => $reservations, 'filter' => $filter]);
});

// Update Reservation Status (Admin)
$router->post('/admin/reservations/update-status', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $db->query("UPDATE reservations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$status, $id]);
    header('Location: /admin/reservations');
    exit;
});

// Site Settings (Admin)
$router->get('/admin/settings', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $stmt = $db->query("SELECT * FROM settings ORDER BY category, key");
    $settings_raw = $stmt->fetchAll();
    
    $settings = [];
    foreach ($settings_raw as $s) {
        $settings[$s['key']] = $s['value'];
    }
    
    view('admin/settings', ['settings' => $settings]);
});

$router->post('/admin/settings/update', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    foreach ($_POST['settings'] as $key => $value) {
        $db->query("UPDATE settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE key = ?", [$value, $key]);
    }
    
    header('Location: /admin/settings?msg=updated');
    exit;
});

// --- SOCIAL NETWORK & CHAT SYSTEM ---

// 1. Community Discovery & Search
$router->get('/communities', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $user = $auth->getUser();
    $search = $_GET['q'] ?? '';
    
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM community_members WHERE community_id = c.id) as member_count,
            (SELECT 1 FROM community_members WHERE community_id = c.id AND user_id = ?) as is_member
            FROM communities c 
            WHERE c.status = 'active'";
    
    $params = [$user['id']];
    if (!empty($search)) {
        $sql .= " AND (c.name ILIKE ? OR c.description ILIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $stmt = $db->query($sql . " ORDER BY member_count DESC", $params);
    $communities = $stmt->fetchAll();
    
    view('social/list', ['communities' => $communities, 'search' => $search]);
});

// 2. Create Community
$router->post('/communities/create', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $user = $auth->getUser();
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    
    // Check if slug exists
    $stmt = $db->query("SELECT id FROM communities WHERE slug = ?", [$slug]);
    if ($stmt->fetch()) {
        $slug .= '-' . rand(100, 999);
    }
    
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_url = uploadFile($_FILES['image'], 'public/uploads/communities');
    }
    
    $db->query("INSERT INTO communities (name, slug, description, creator_id, image_url) VALUES (?, ?, ?, ?, ?)", [
        $name, $slug, $description, $user['id'], $image_url
    ]);
    
    $community_id = $db->getPdo()->lastInsertId();
    
    // Auto-join creator
    $db->query("INSERT INTO community_members (community_id, user_id) VALUES (?, ?)", [$community_id, $user['id']]);
    
    header('Location: /community/' . $slug);
    exit;
});

// 3. Join / Leave Community
$router->post('/communities/join', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    $db->query("INSERT INTO community_members (community_id, user_id) VALUES (?, ?) ON CONFLICT DO NOTHING", [$_POST['community_id'], $auth->getUser()['id']]);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
});

$router->post('/communities/leave', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    $db->query("DELETE FROM community_members WHERE community_id = ? AND user_id = ?", [$_POST['community_id'], $auth->getUser()['id']]);
    header('Location: /communities');
    exit;
});

// 4. Community Chat View
$router->get('/community/{slug}', function($slug) {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { header('Location: /login'); exit; }
    
    $user = $auth->getUser();
    
    // Get Community Info
    $stmt = $db->query("SELECT c.*, u.username as creator_name 
                        FROM communities c 
                        JOIN users u ON c.creator_id = u.id 
                        WHERE c.slug = ?", [$slug]);
    $community = $stmt->fetch();
    
    if (!$community || $community['status'] !== 'active') {
        header('Location: /communities?error=not_found');
        exit;
    }
    
    // Check if member
    $stmt = $db->query("SELECT 1 FROM community_members WHERE community_id = ? AND user_id = ?", [$community['id'], $user['id']]);
    $is_member = $stmt->fetch();
    
    if (!$is_member) {
        view('social/join_page', ['community' => $community]);
        return;
    }
    
    // Get Members
    $stmt = $db->query("SELECT u.username, u.id FROM users u JOIN community_members cm ON u.id = cm.user_id WHERE cm.community_id = ?", [$community['id']]);
    $members = $stmt->fetchAll();
    
    // Initial Messages (Last 50)
    $stmt = $db->query("SELECT m.*, u.username 
                        FROM messages m 
                        JOIN users u ON m.user_id = u.id 
                        WHERE m.community_id = ? 
                        ORDER BY m.created_at ASC 
                        LIMIT 50", [$community['id']]);
    $messages = $stmt->fetchAll();
    
    view('social/chat', ['community' => $community, 'members' => $members, 'messages' => $messages, 'user' => $user]);
});

// 5. AJAX Polling & Message Sending
$router->post('/messages/send', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { echo json_encode(['success' => false]); exit; }
    
    $user = $auth->getUser();
    $community_id = $_POST['community_id'];
    $text = trim($_POST['message_text'] ?? '');
    
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_url = uploadFile($_FILES['image'], 'public/uploads/chat');
    }
    
    if (empty($text) && empty($image_url)) {
        echo json_encode(['success' => false]); exit;
    }
    
    $db->query("INSERT INTO messages (community_id, user_id, message_text, image_url) VALUES (?, ?, ?, ?)", [
        $community_id, $user['id'], $text, $image_url
    ]);
    
    echo json_encode(['success' => true]);
    exit;
});

$router->get('/messages/fetch', function() {
    global $auth, $db;
    if (!$auth->isLoggedIn()) { echo json_encode([]); exit; }
    
    $community_id = $_GET['community_id'];
    $last_id = intval($_GET['last_id'] ?? 0);
    
    $stmt = $db->query("SELECT m.*, u.username 
                        FROM messages m 
                        JOIN users u ON m.user_id = u.id 
                        WHERE m.community_id = ? AND m.id > ? 
                        ORDER BY m.created_at ASC", [$community_id, $last_id]);
    
    echo json_encode($stmt->fetchAll());
    exit;
});

// 6. Admin Community Management
$router->get('/admin/communities', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $stmt = $db->query("SELECT c.*, u.username as creator_name, 
                        (SELECT COUNT(*) FROM community_members WHERE community_id = c.id) as member_count
                        FROM communities c 
                        JOIN users u ON c.creator_id = u.id 
                        ORDER BY c.created_at DESC");
    $communities = $stmt->fetchAll();
    
    view('admin/social/communities', ['communities' => $communities]);
});

// 7. Admin Tour Routes Management
$router->get('/admin/routes', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $stmt = $db->query("SELECT * FROM tour_routes ORDER BY created_at DESC");
    $routes = $stmt->fetchAll();
    
    view('admin/routes', ['routes' => $routes]);
});

$router->get('/admin/routes/new', function() {
    global $auth;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    view('admin/route-form', ['title' => 'New Tour Route']);
});

$router->get('/admin/routes/edit', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $id = $_GET['id'];
    $stmt = $db->query("SELECT * FROM tour_routes WHERE id = ?", [$id]);
    $route = $stmt->fetch();
    
    if (!$route) { header('Location: /admin/routes'); exit; }
    
    // Get Stops
    $stmt = $db->query("SELECT rs.*, i.*, i.title as place_name 
                        FROM route_stops rs 
                        JOIN items i ON rs.item_id = i.id 
                        WHERE rs.route_id = ? 
                        ORDER BY rs.order_index ASC", [$id]);
    $stops = $stmt->fetchAll();
    
    // Get all places for selection
    $stmt = $db->query("SELECT id, title FROM items WHERE is_approved = TRUE ORDER BY title ASC");
    $places = $stmt->fetchAll();
    
    view('admin/route-form', [
        'title' => 'Edit Route: ' . $route['name'],
        'route' => $route, 
        'stops' => $stops,
        'places' => $places
    ]);
});

$router->post('/admin/routes/save', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $interest = $_POST['interest_tag'];
    $time = $_POST['estimated_time'];
    $difficulty = $_POST['difficulty'];
    $image_url = $_POST['image_url'];
    
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    $name_translations = json_encode($_POST['name_trans'] ?? []);
    $description_translations = json_encode($_POST['desc_trans'] ?? []);
    
    if ($id) {
        $db->query("UPDATE tour_routes SET name = ?, slug = ?, description = ?, interest_tag = ?, estimated_time = ?, difficulty = ?, image_url = ?, name_translations = ?::jsonb, description_translations = ?::jsonb WHERE id = ?", [
            $name, $slug, $description, $interest, $time, $difficulty, $image_url, $name_translations, $description_translations, $id
        ]);
    } else {
        $db->query("INSERT INTO tour_routes (name, slug, description, interest_tag, estimated_time, difficulty, image_url, name_translations, description_translations) VALUES (?, ?, ?, ?, ?, ?, ?, ?::jsonb, ?::jsonb)", [
            $name, $slug, $description, $interest, $time, $difficulty, $image_url, $name_translations, $description_translations
        ]);
        $id = $db->getPdo()->lastInsertId();
    }
    
    header('Location: /admin/routes/edit?id=' . $id . '&msg=saved');
    exit;
});

$router->post('/admin/routes/stops/add', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $route_id = $_POST['route_id'];
    $item_id = $_POST['item_id'];
    $note = $_POST['stop_note'];
    $note_translations = json_encode($_POST['note_trans'] ?? []);
    
    // Get next order index
    $stmt = $db->query("SELECT MAX(order_index) as last_order FROM route_stops WHERE route_id = ?", [$route_id]);
    $row = $stmt->fetch();
    $next_order = ($row['last_order'] ?? -1) + 1;
    
    $db->query("INSERT INTO route_stops (route_id, item_id, order_index, stop_note, note_translations) VALUES (?, ?, ?, ?, ?::jsonb)", [
        $route_id, $item_id, $next_order, $note, $note_translations
    ]);
    
    header('Location: /admin/routes/edit?id=' . $route_id . '&msg=stop_added');
    exit;
});

$router->post('/admin/routes/stops/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $stop_id = $_POST['stop_id'];
    $route_id = $_POST['route_id'];
    
    $db->query("DELETE FROM route_stops WHERE id = ?", [$stop_id]);
    
    header('Location: /admin/routes/edit?id=' . $route_id . '&msg=stop_removed');
    exit;
});

$router->post('/admin/routes/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $db->query("DELETE FROM tour_routes WHERE id = ?", [$_POST['id']]);
    header('Location: /admin/routes');
    exit;
});

$router->post('/admin/communities/toggle-status', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $id = $_POST['id'];
    $status = $_POST['status']; // 'active' or 'disabled'
    
    $db->query("UPDATE communities SET status = ? WHERE id = ?", [$status, $id]);
    header('Location: /admin/communities');
    exit;
});

$router->post('/admin/communities/delete', function() {
    global $auth, $db;
    if (!$auth->isAdmin()) { header('Location: /admin/login'); exit; }
    
    $db->query("DELETE FROM communities WHERE id = ?", [$_POST['id']]);
    header('Location: /admin/communities');
    exit;
});

// Run
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
