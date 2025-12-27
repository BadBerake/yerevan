<?php view('admin/header', ['title' => isset($category) ? 'Edit Category' : 'New Category']); ?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h2><?= isset($category) ? 'Edit Category' : 'New Category' ?></h2>
</div>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); max-width: 600px;">
    <form action="<?= isset($category) ? '/admin/categories/update' : '/admin/categories/store' ?>" method="POST">
        <?php if (isset($category)): ?>
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
        <?php endif; ?>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug (URL friendly)</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #666;">e.g., "fine-dining" or "music-festivals"</small>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Type</label>
            <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="place" <?= (isset($category) && $category['type'] === 'place') ? 'selected' : '' ?>>Place (Restaurant, Cafe, etc.)</option>
                <option value="event" <?= (isset($category) && $category['type'] === 'event') ? 'selected' : '' ?>>Event</option>
            </select>
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><?= isset($category) ? 'Update Category' : 'Create Category' ?></button>
            <a href="/admin/categories" class="btn btn-outline" style="margin-left: 10px; border: none;">Cancel</a>
        </div>
    </form>
</div>

<?php view('admin/footer'); ?>
