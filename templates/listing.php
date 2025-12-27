<?php view('header', ['title' => $title]); ?>

<div class="container mt-2">
    <div class="flex-between mb-2" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="section-title"><?= htmlspecialchars($title) ?></h1>
            <p class="section-subtitle"><?= __('hero_subtitle') ?></p>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <label for="sortSelector" style="font-size: 0.9rem; color: var(--text-muted);"><?= __('sort_by') ?>:</label>
            <select id="sortSelector" style="padding: 10px 15px; border-radius: 50px; border: 1px solid #e2e8f0; font-size: 0.9rem; background: white; cursor: pointer; box-shadow: var(--shadow-sm);">
                <option value="default"><?= __('popularity') ?></option>
                <option value="newest"><?= __('newest') ?></option>
                <option value="rating"><?= __('highest_rated') ?></option>
            </select>
        </div>
    </div>

    <div class="grid" id="itemsGrid">
        <?php foreach ($items as $item): ?>
        <a href="/place/<?= $item['id'] ?>" class="card" data-id="<?= $item['id'] ?>" data-created="<?= strtotime($item['created_at']) ?>" data-rating="4.8" style="text-decoration: none; color: inherit;">
            <div class="card-img" style="background-image: url('<?= htmlspecialchars($item['image_url'] ?? '') ?>');">
                <div style="padding: 15px; display: flex; justify-content: flex-end;">
                    <span style="background: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: var(--text-main); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        ⭐ 4.8
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="card-title"><?= htmlspecialchars(Lang::t($item['title_translations'], $item['title'])) ?></div>
                <div class="card-meta">
                    <span style="color: var(--primary);">📍</span> <?= htmlspecialchars($item['address']) ?>
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
    </div>
</div>

<script>
document.getElementById('sortSelector').addEventListener('change', function() {
    const grid = document.getElementById('itemsGrid');
    const items = Array.from(grid.getElementsByClassName('card'));
    const val = this.value;

    items.sort((a, b) => {
        if (val === 'newest') {
            return b.dataset.created - a.dataset.created;
        } else if (val === 'rating') {
            return b.dataset.rating - a.dataset.rating;
        }
        return a.dataset.id - b.dataset.id; // Default sort
    });

    // Re-append items in new order
    items.forEach(item => grid.appendChild(item));
});
</script>

<?php view('footer'); ?>
