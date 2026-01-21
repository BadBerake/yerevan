<?php
/**
 * Filter Sidebar Component
 */
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<aside class="filter-sidebar">
    <form id="filters-form">
        <!-- Categories -->
        <div class="filter-section">
            <h4><i class="fas fa-list"></i> <?= __('categories') ?></h4>
            <div class="filter-options">
                <?php foreach ($categories as $category): ?>
                <label class="filter-checkbox-label">
                    <input type="checkbox" name="category[]" value="<?= $category['slug'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Price Range -->
        <div class="filter-section">
            <h4><i class="fas fa-tag"></i> <?= __('price_range') ?></h4>
            <div class="filter-options">
                <label class="filter-checkbox-label">
                    <input type="checkbox" name="price_range[]" value="budget"> $ (<?= __('budget') ?>)
                </label>
                <label class="filter-checkbox-label">
                    <input type="checkbox" name="price_range[]" value="moderate"> $$ (<?= __('moderate') ?>)
                </label>
                <label class="filter-checkbox-label">
                    <input type="checkbox" name="price_range[]" value="expensive"> $$$ (<?= __('expensive') ?>)
                </label>
                <label class="filter-checkbox-label">
                    <input type="checkbox" name="price_range[]" value="luxury"> $$$$ (<?= __('luxury') ?>)
                </label>
            </div>
        </div>

        <!-- Rating -->
        <div class="filter-section">
            <h4><i class="fas fa-star"></i> <?= __('min_rating') ?></h4>
            <input type="range" name="min_rating" min="0" max="5" step="0.5" value="0">
            <div class="range-labels">
                <span>0</span>
                <span>2.5</span>
                <span>5</span>
            </div>
        </div>

        <!-- Distance -->
        <div class="filter-section">
            <h4><i class="fas fa-location-arrow"></i> <?= __('max_distance') ?></h4>
            <input type="range" name="max_distance" min="1" max="50" step="1" value="50">
            <div class="range-labels">
                <span>1km</span>
                <span>25km</span>
                <span>50km+</span>
            </div>
        </div>

        <!-- Status -->
        <div class="filter-section">
            <h4><i class="fas fa-clock"></i> <?= __('status') ?></h4>
            <label class="filter-checkbox-label">
                <input type="checkbox" name="open_now" value="true">
                <?= __('open_now') ?>
            </label>
        </div>

        <!-- Sort By -->
        <div class="filter-section">
            <h4><i class="fas fa-sort"></i> <?= __('sort_by') ?></h4>
            <select name="sort" class="form-control" style="width: 100%; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <option value="relevance"><?= __('relevance') ?></option>
                <option value="rating"><?= __('highest_rated') ?></option>
                <option value="distance"><?= __('nearest') ?></option>
                <option value="newest"><?= __('newest') ?></option>
                <option value="name"><?= __('name') ?></option>
            </select>
        </div>
    </form>
</aside>
