<?php view('header', ['title' => $title]); ?>

<section class="hero" style="margin-bottom: 0;">
    <h1><?= __('hero_title') ?></h1>
    <p><?= __('hero_subtitle') ?></p>
</section>

<div class="container" style="margin-top: -3rem; position: relative; z-index: 10;">
    
    <!-- Personalized Recommendations -->
    <?php if (!empty($personalized)): ?>
        <div class="mt-5 mb-2">
            <h2 class="section-title" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <span style="background: var(--color-arm-orange); color: #000; padding: 4px 16px; border-radius: 50px; font-size: 0.9rem; font-weight: 800; box-shadow: 0 0 15px rgba(242, 168, 0, 0.3);"><?= __('for_you') ?></span>
                <span style="color: var(--text-main);"><?= __('tailored_interests') ?></span>
            </h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 5rem;">
            <?php foreach ($personalized as $route): ?>
                <a href="/route/<?= $route['slug'] ?>" style="text-decoration: none; color: inherit;">
                    <div class="card" style="border: 2px solid var(--color-arm-orange);">
                        <div class="card-img lazy-bg" data-src="<?= $route['image_url'] ?: 'https://via.placeholder.com/600x400' ?>" style="position: relative; background-position: center; background-size: cover;">
                            <div style="position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); color: white; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                <?= ucfirst($route['difficulty']) ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="color: var(--primary); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;"><?= htmlspecialchars($route['interest_tag'] ?: 'Local Guide') ?></div>
                            <h3 class="card-title"><?= htmlspecialchars(Lang::t($route['name_translations'], $route['name'])) ?></h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars(Lang::t($route['description_translations'], $route['description'])) ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                                <span style="font-size: 0.85rem; color: #94a3b8;">🕑 <?= $route['estimated_time'] ?></span>
                                <span style="font-weight: 700; color: var(--primary); font-size: 0.9rem;"><?= __('view_route') ?> →</span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- All Other Routes -->
    <div class="mt-5 mb-2">
        <h2 class="section-title" style="color: var(--text-main); font-size: 2.2rem;"><?= __('explore_more_routes') ?></h2>
        <p class="section-subtitle"><?= __('explore_map') ?></p>
    </div>

    <?php if (empty($others) && empty($personalized)): ?>
        <div style="text-align: center; padding: 5rem; background: white; border-radius: 24px; box-shadow: var(--shadow-md); border: 2px dashed #eee;">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">🏗️</div>
            <h3><?= __('coming_soon') ?></h3>
            <p style="color: #64748b;"><?= __('coming_soon_desc') ?></p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($others as $route): ?>
                <a href="/route/<?= $route['slug'] ?>" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <div class="card-img lazy-bg" data-src="<?= $route['image_url'] ?: 'https://via.placeholder.com/500x300' ?>" style="background-position: center; background-size: cover;"></div>
                        <div class="card-body">
                            <div style="color: var(--primary); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;"><?= htmlspecialchars($route['interest_tag'] ?: 'Local Guide') ?></div>
                            <h3 class="card-title"><?= htmlspecialchars(Lang::t($route['name_translations'], $route['name'])) ?></h3>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; color: #94a3b8; font-size: 0.85rem;">
                                <span>🕑 <?= $route['estimated_time'] ?></span>
                                <span style="background: #f8fafc; padding: 2px 8px; border-radius: 6px;"><?= ucfirst($route['difficulty']) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php view('footer'); ?>
