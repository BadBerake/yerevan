<?php view('header', ['title' => $title]); ?>

<div class="container mt-2">
    <!-- Search Bar -->
    <?php view('components/search-bar'); ?>
    <?php view('components/breadcrumbs'); ?>

    <div class="search-layout">
        <!-- Filter Sidebar -->
        <?php view('components/filter-sidebar', ['db' => $GLOBALS['db']]); ?>

        <div class="main-content">
            <div class="result-header">
                <h1 class="section-title"><?= htmlspecialchars($title) ?></h1>
                <span id="result-count"><?= $totalCount ?? count($items) ?> <?= __('results_found') ?></span>
            </div>

            <!-- Active Filters -->
            <div id="active-filters" class="active-filters" style="display: none;"></div>

    <div class="grid" id="itemsGrid">
        <?php foreach ($items as $item): ?>
        <a href="/place/<?= $item['id'] ?>" class="card" data-id="<?= $item['id'] ?>" data-created="<?= strtotime($item['created_at']) ?>" data-rating="4.8" style="text-decoration: none; color: inherit;">
            <div class="card-img lazy-bg" data-src="<?= htmlspecialchars($item['image_url'] ?? '') ?>" style="background-position: center; background-size: cover;">
                <div style="padding: 15px; display: flex; justify-content: flex-end;">
                    <span style="background: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: var(--text-main); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        ⭐ 4.8
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars(Lang::t($item['title_translations'], $item['title'])) ?></div>
                <div class="card-meta">
                    <span style="color: var(--primary);">📍</span> <?= htmlspecialchars($item['address'] ?? '') ?>
                </div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?= htmlspecialchars(Lang::t($item['description_translations'], $item['description'] ?? '')) ?>
                </p>
                <div class="btn btn-outline" style="align-self: flex-start; margin-top: auto; width: 100%;"><?= __('view_details') ?></div>
            </div>
        </a>
        <?php endforeach; ?>
        
        <?php if (empty($items)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 5rem; background: white; border-radius: 24px; border: 1px dashed #cbd5e1;">
                <h3 style="color: var(--text-muted); margin-bottom: 1rem;"><?= __('no_items') ?></h3>
                <a href="/" class="btn btn-primary" style="padding: 10px 30px; border-radius: 50px;"><?= __('go_back_home') ?></a>
            </div>
        <?php endif; ?>
        </div> <!-- .main-content -->
    </div> <!-- .search-layout -->
</div> <!-- .container -->

<?php view('footer'); ?>
