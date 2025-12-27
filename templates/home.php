<?php view('header', ['title' => $title]); ?>

<section class="hero">
    <h1><?= __('hero_title') ?></h1>
    <p><?= __('hero_subtitle') ?></p>
    
    <form action="/search" method="GET" class="search-bar">
        <input type="text" name="q" placeholder="<?= __('search_placeholder') ?>" required>
        <button type="submit"><?= __('search') ?></button>
    </form>
</section>

<!-- Map removed as per request -->

<div class="container">
    <div class="flex-between mb-2">
        <h2 style="font-size: 1.8rem; color: var(--text-main); margin: 0;"><?= __('explore_by_category') ?></h2>
    </div>
    
    <div class="category-grid">
        <a href="/restaurants" class="cat-card">
            <span class="cat-icon">🍽️</span>
            <span class="cat-title"><?= __('restaurants') ?></span>
            <span class="cat-desc">Best Armenian & Int. Cuisine</span>
        </a>
        
        <a href="/cafes" class="cat-card">
            <span class="cat-icon">☕</span>
            <span class="cat-title"><?= __('cafes') ?></span>
            <span class="cat-desc">Cozy Spots & Workspaces</span>
        </a>
        
        <a href="/events" class="cat-card">
            <span class="cat-icon">🎉</span>
            <span class="cat-title"><?= __('events') ?></span>
            <span class="cat-desc">Live Music & Festivals</span>
        </a>
    </div>

    <div class="mt-5 mb-2 text-center">
        <h2 class="section-title"><?= __('curated_routes_title') ?></h2>
        <p class="section-subtitle"><?= __('curated_routes_subtitle') ?></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 5rem;">
        <?php foreach ($routes as $route): ?>
            <a href="/route/<?= $route['slug'] ?>" style="text-decoration: none; color: inherit;">
                <div class="card" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="height: 180px; background: #f1f5f9 url('<?= $route['image_url'] ?: 'https://via.placeholder.com/500x300' ?>') center/cover;"></div>
                    <div style="padding: 1.5rem;">
                        <div style="color: var(--primary); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;"><?= htmlspecialchars($route['interest_tag'] ?: 'Local Guide') ?></div>
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.2rem; font-weight: 700;"><?= htmlspecialchars(Lang::t($route['name_translations'], $route['name'])) ?></h3>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; color: #94a3b8; font-size: 0.85rem;">
                            <span>🕑 <?= $route['estimated_time'] ?></span>
                            <span style="background: #f8fafc; padding: 2px 8px; border-radius: 6px;"><?= ucfirst($route['difficulty']) ?></span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if (empty($routes)): ?>
            <p style="text-align: center; grid-column: 1/-1; padding: 3rem; background: #fff; border-radius: 20px; border: 1px dashed #eee;">New routes coming soon!</p>
        <?php endif; ?>
    </div>

    <div class="text-center mb-5">
        <a href="/explore" class="btn btn-primary" style="padding: 12px 40px; border-radius: 50px; font-weight: 600;"><?= __('view_all_routes') ?></a>
    </div>

    <div class="mt-5 mb-2 text-center">
        <h2 class="section-title"><?= __('featured_places') ?></h2>
        <p class="section-subtitle"><?= __('featured_places_subtitle') ?></p>
    </div>

    <div class="grid">
        <?php foreach ($featured as $item): ?>
        <a href="/place/<?= $item['id'] ?>" class="card" style="text-decoration: none; color: inherit;">
            <div class="card-img" style="background-image: url('<?= htmlspecialchars($item['image_url'] ?? '') ?>');"></div>
            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars(Lang::t($item['title_translations'], $item['title'])) ?></div>
                <div class="card-meta">
                    <span style="color: var(--primary);">📍</span> <?= htmlspecialchars($item['address']) ?>
                </div>
                <div class="btn btn-outline" style="align-self: flex-start; margin-top: auto; padding: 5px 15px; font-size: 0.8rem;"><?= __('explore') ?></div>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($featured)): ?>
            <p style="text-align: center; grid-column: 1/-1; padding: 3rem; background: #fff; border-radius: 20px;">No featured places yet.</p>
        <?php endif; ?>
    </div>
</div>

<div style="height: 4rem;"></div>

<?php view('footer'); ?>
