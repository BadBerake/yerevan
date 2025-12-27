<!DOCTYPE html>
<html lang="<?= Lang::current() ?>" dir="<?= Lang::getDir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Yerevango') ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Flag_of_Armenia.svg/320px-Flag_of_Armenia.svg.png">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="/" class="logo">
                YerevanGo <span>🇦🇲</span>
            </a>

            <button class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav id="mainNav">
                <ul>
                    <li><a href="/"><?= __('home') ?></a></li>
                    <li><a href="/restaurants"><?= __('restaurants') ?></a></li>
                    <li><a href="/cafes"><?= __('cafes') ?></a></li>
                    <li><a href="/events"><?= __('events') ?></a></li>
                    <li><a href="/explore" style="font-weight: 700; color: var(--primary);"><?= __('explore') ?></a></li>
                    <li><a href="/map"><?= __('map') ?></a></li>
                    <li><a href="/communities"><?= __('communities') ?></a></li>
                    
                    <!-- Mobile Auth Links (Visible only on mobile via CSS if needed, or JS) -->
                    <li class="mobile-only-auth" style="display: none; border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem; width: 100%;">
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                            <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                                <a href="/dashboard" class="btn btn-primary"><?= __('dashboard') ?></a>
                                <a href="/logout" class="btn btn-outline"><?= __('logout') ?></a>
                            <?php else: ?>
                                <a href="/login" class="btn btn-outline"><?= __('login') ?></a>
                                <a href="/register" class="btn btn-primary"><?= __('register') ?></a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="auth-buttons" style="display: flex; gap: 15px; align-items: center;">
                <form action="" method="GET" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                    <select name="lang" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 20px; border: 1px solid #e2e8f0; font-size: 0.85rem; background: white; cursor: pointer; border-right: 8px solid transparent;">
                        <option value="en" <?= Lang::current() == 'en' ? 'selected' : '' ?>>🇬🇧 EN</option>
                        <option value="hy" <?= Lang::current() == 'hy' ? 'selected' : '' ?>>🇦🇲 HY</option>
                        <option value="ru" <?= Lang::current() == 'ru' ? 'selected' : '' ?>>🇷🇺 RU</option>
                        <option value="fa" <?= Lang::current() == 'fa' ? 'selected' : '' ?>>🇮🇷 FA</option>
                        <option value="ar" <?= Lang::current() == 'ar' ? 'selected' : '' ?>>🇸🇦 AR</option>
                    </select>
                </form>

                <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                    <a href="/dashboard" class="btn btn-primary"><?= __('dashboard') ?></a>
                    <a href="/logout" class="btn btn-outline" style="border: none;"><?= __('logout') ?></a>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline" style="border: none;"><?= __('login') ?></a>
                    <a href="/register" class="btn btn-primary"><?= __('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        document.getElementById('mobileToggle').addEventListener('click', function() {
            this.classList.toggle('active');
            const nav = document.getElementById('mainNav');
            nav.classList.toggle('active');
            
            // Toggle visibility of mobile auth
            const mobileAuth = nav.querySelector('.mobile-only-auth');
            if (window.innerWidth <= 768) {
                mobileAuth.style.display = nav.classList.contains('active') ? 'block' : 'none';
            }
        });
    </script>
    <main>
