<!DOCTYPE html>
<html lang="<?= Lang::current() ?>" dir="<?= Lang::getDir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Panel') ?> - YerevanGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { font-family: 'Outfit', sans-serif; margin: 0; background-color: #f4f7f6; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="/admin" class="admin-brand">
                <span style="font-size: 1.8rem;">💎</span> YerevanGo
            </a>
            
            <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                <div style="width: 40px; height: 40px; background: #e74c3c; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">A</div>
                <div style="overflow: hidden;">
                    <div style="font-weight: 600; white-space: nowrap;">Admin Panel</div>
                    <div style="font-size: 0.8rem; color: #7f8c8d;"><?= date('M j, Y') ?></div>
                </div>
            </div>

            <ul class="admin-menu">
                <li><a href="/admin" class="<?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                    <span class="admin-menu-icon">📊</span> <?= __('admin_dashboard') ?>
                </a></li>
                <li style="margin-top: 1rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #7f8c8d; padding-left: 15px;">Content</li>
                <li><a href="/admin/items" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/items') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">🏢</span> <?= __('total_items') ?>
                </a></li>
                <li><a href="/admin/categories" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">🏷️</span> Categories
                </a></li>
                <li><a href="/admin/approvals" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/approvals') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">✓</span> <?= __('pending_approvals') ?>
                </a></li>
                <li><a href="/admin/reviews" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/reviews') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">⭐</span> Reviews
                </a></li>
                <li><a href="/admin/reservations" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/reservations') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">📅</span> Reservations
                </a></li>
                <li><a href="/admin/communities" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/communities') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">🏘️</span> Communities
                </a></li>
                <li><a href="/admin/routes" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/routes') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">🗺️</span> Tour Routes
                </a></li>
                <li><a href="/admin/tours" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/tours') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">🎟️</span> Tours Sale
                </a></li>
                
                <li style="margin-top: 1rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #7f8c8d; padding-left: 15px;">System</li>
                <li><a href="/admin/users" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/users') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">👥</span> <?= __('users') ?>
                </a></li>
                <li><a href="/admin/settings" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') === 0 ? 'active' : '' ?>">
                    <span class="admin-menu-icon">⚙️</span> Settings
                </a></li>
            </ul>

            <div style="margin-top: auto;">
                <a href="/logout" style="color: #e74c3c; padding: 10px 15px; display: flex; align-items: center; gap: 10px; text-decoration: none; border-radius: 6px; hover: background: rgba(231,76,60,0.1);">
                    <span>🚪</span> <?= __('logout') ?>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Topbar -->
            <div class="admin-topbar">
                <h2 style="margin: 0; font-size: 1.5rem; color: #2c3e50;"><?= $title ?? 'Dashboard' ?></h2>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <a href="/" target="_blank" class="btn btn-outline" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">
                        Global Site ↗
                    </a>
                    
                    <form action="" method="GET" style="margin: 0;">
                        <select name="lang" onchange="this.form.submit()" style="padding: 8px; border-radius: 8px; border: 1px solid #ddd; background: white;">
                            <option value="en" <?= Lang::current() == 'en' ? 'selected' : '' ?>>🇬🇧 EN</option>
                            <option value="hy" <?= Lang::current() == 'hy' ? 'selected' : '' ?>>🇦🇲 HY</option>
                            <option value="ru" <?= Lang::current() == 'ru' ? 'selected' : '' ?>>🇷🇺 RU</option>
                            <option value="fa" <?= Lang::current() == 'fa' ? 'selected' : '' ?>>🇮🇷 FA</option>
                            <option value="ar" <?= Lang::current() == 'ar' ? 'selected' : '' ?>>🇸🇦 AR</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Page Content -->
            <div class="admin-content">
