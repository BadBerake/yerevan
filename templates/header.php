<!DOCTYPE html>
<html lang="<?= Lang::current() ?>" dir="<?= Lang::getDir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google tag (ga.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XXXXXXXXXX');
    </script>
    
    <?php
    // Initialize SEO Service
    require_once __DIR__ . '/../src/SEOService.php';
    $seoService = new \App\Services\SEOService();
    
    // Generate SEO meta tags
    echo $seoService->generateMetaTags([
        'title' => $title ?? 'Yerevango',
        'description' => $seoDescription ?? 'Discover the best places, restaurants, cafes, and events in Yerevan, Armenia. Your complete guide to exploring the capital of Armenia.',
        'image' => $ogImage ?? null,
        'url' => $_SERVER['REQUEST_URI'] ?? '/',
        'type' => $ogType ?? 'website'
    ]);
    ?>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#D90012">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Yerevango">
    
    <!-- Favicons & App Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logo-icon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/logo-icon.png">
    <link rel="apple-touch-icon" href="/assets/images/logo-icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/css/style.css">
    
    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Leaflet Plugins -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@3.0.2/Control.FullScreen.css" />
    <script src="https://unpkg.com/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.css" />
    <script src="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.js"></script>
    
    <!-- Custom Yerevango Maps Library -->
    <link rel="stylesheet" href="/css/yerevango-maps.css">
    <script src="/js/yerevango-maps.js"></script>
    
    <!-- Search & Filters -->
    <link rel="stylesheet" href="/css/components/search.css">
    <link rel="stylesheet" href="/css/components/reviews.css">
    <script src="/js/search.js" defer></script>
    <script src="/js/lazy.js" defer></script>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- PWA Service Worker -->
    <script src="/js/pwa.js" defer></script>
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="/" class="logo">
                <span class="logo-text">Yerevan</span><span class="logo-highlight">Go</span> <span class="logo-flag">🇦🇲</span>
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
                    <li><a href="/tours" style="color: var(--primary); font-weight: 700;"><?= __('tours') ?? 'Tours' ?></a></li>
                    <li><a href="/transport"><?= __('transport') ?? 'Transport' ?></a></li>
                    <li><a href="/communities"><?= __('communities') ?></a></li>
                    <li><a href="/leaderboard" title="Community Rankings"><i class="fas fa-trophy" style="color: #facc15; margin-right: 5px;"></i> Leaderboard</a></li>
                    
                    <!-- Mobile Auth Links (Visible only on mobile via CSS) -->
                    <li class="mobile-only-auth" style="display: none; border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem; width: 100%;">
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; align-items: center;">
                             <!-- Mobile Language Selector -->
                            <form action="" method="GET" class="lang-form" style="margin-bottom: 15px;">
                                <div class="lang-select-wrapper" style="background: #f1f5f9;">
                                    <select name="lang" onchange="this.form.submit()" class="lang-select">
                                        <option value="en" <?= Lang::current() == 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                                        <option value="hy" <?= Lang::current() == 'hy' ? 'selected' : '' ?>>🇦🇲 Հայերեն</option>
                                        <option value="ru" <?= Lang::current() == 'ru' ? 'selected' : '' ?>>🇷🇺 Русский</option>
                                        <option value="fa" <?= Lang::current() == 'fa' ? 'selected' : '' ?>>🇮🇷 فارسی</option>
                                        <option value="ar" <?= Lang::current() == 'ar' ? 'selected' : '' ?>>🇸🇦 العربية</option>
                                    </select>
                                    <i class="fas fa-chevron-down lang-arrow"></i>
                                </div>
                            </form>

                            <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                                <a href="/dashboard" class="btn btn-primary" style="width: 100%; justify-content: center;"><?= __('dashboard') ?></a>
                                <a href="/logout" class="btn btn-outline" style="width: 100%; justify-content: center;"><?= __('logout') ?></a>
                            <?php else: ?>
                                <a href="/login" class="btn btn-outline" style="width: 100%; justify-content: center;"><?= __('login') ?></a>
                                <a href="/register" class="btn btn-primary" style="width: 100%; justify-content: center;"><?= __('register') ?></a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="auth-buttons" style="display: flex; gap: 15px; align-items: center;">
                <form action="" method="GET" class="lang-form" style="margin: 0; display: flex; align-items: center;">
                    <div class="lang-select-wrapper">
                        <select name="lang" onchange="this.form.submit()" class="lang-select">
                            <option value="en" <?= Lang::current() == 'en' ? 'selected' : '' ?>>🇬🇧 EN</option>
                            <option value="hy" <?= Lang::current() == 'hy' ? 'selected' : '' ?>>🇦🇲 HY</option>
                            <option value="ru" <?= Lang::current() == 'ru' ? 'selected' : '' ?>>🇷🇺 RU</option>
                            <option value="fa" <?= Lang::current() == 'fa' ? 'selected' : '' ?>>🇮🇷 FA</option>
                            <option value="ar" <?= Lang::current() == 'ar' ? 'selected' : '' ?>>🇸🇦 AR</option>
                        </select>
                        <i class="fas fa-chevron-down lang-arrow"></i>
                    </div>
                </form>

                <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                    <?php $user = $auth->getUser(); ?>
                    <div class="user-rank" style="display: flex; align-items: center; gap: 6px; margin-right: 10px; background: rgba(0,0,0,0.05); padding: 4px 10px; border-radius: 20px;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Lvl <?= $user['level'] ?></span>
                        <div style="width: 1px; height: 15px; background: #e2e8f0;"></div>
                        <span style="font-weight: 800; color: var(--primary); font-size: 0.9rem;">✨ <?= number_format($user['points']) ?></span>
                    </div>
                    <button id="pwa-install-btn" class="btn btn-outline" style="display: none; align-items: center; gap: 8px; border-color: #D90012; color: #D90012; margin-right: 5px;">
                        <i class="fas fa-download"></i> Install App
                    </button>
                    <a href="/dashboard" class="btn btn-primary"><?= __('dashboard') ?></a>
                    <a href="/logout" class="btn btn-outline" style="border: none;"><?= __('logout') ?></a>
                <?php else: ?>
                    <button id="pwa-install-btn" class="btn btn-outline" style="display: none; align-items: center; gap: 8px; border-color: #D90012; color: #D90012; margin-right: 5px;">
                        <i class="fas fa-download"></i> Install App
                    </button>
                    <a href="/login" class="btn btn-outline" style="border: none;"><?= __('login') ?></a>
                    <a href="/register" class="btn btn-primary"><?= __('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="menu-overlay" id="menuOverlay"></div>

    <script>
        document.getElementById('mobileToggle').addEventListener('click', function() {
            this.classList.toggle('active');
            const nav = document.getElementById('mainNav');
            const overlay = document.getElementById('menuOverlay');
            nav.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = nav.classList.contains('active') ? 'hidden' : '';
            
            // Toggle visibility of mobile auth
            const mobileAuth = nav.querySelector('.mobile-only-auth');
            if (window.innerWidth <= 768) {
                mobileAuth.style.display = nav.classList.contains('active') ? 'block' : 'none';
            }
        });

        // Close menu on click outside or overlay click
        function closeMenu() {
            const nav = document.getElementById('mainNav');
            const toggle = document.getElementById('mobileToggle');
            const overlay = document.getElementById('menuOverlay');
            toggle.classList.remove('active');
            nav.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('menuOverlay').addEventListener('click', closeMenu);

        document.addEventListener('click', function(e) {
            const nav = document.getElementById('mainNav');
            const toggle = document.getElementById('mobileToggle');
            if (nav.classList.contains('active') && !nav.contains(e.target) && !toggle.contains(e.target) && !e.target.classList.contains('menu-overlay')) {
                closeMenu();
            }
        });
    </script>
    <main>
