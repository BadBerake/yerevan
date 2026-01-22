<?php 
$isEdit = isset($tour['id']);
$title = $isEdit ? 'Edit Tour: ' . $tour['title'] : 'Add New Tour Sale';
$action = $isEdit ? '/admin/tours/update' : '/admin/tours/store';
view('admin/header', ['title' => $title]); 
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h2><?= htmlspecialchars($title) ?></h2>
</div>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); max-width: 900px;">
    <form action="<?= $action ?>" method="POST" enctype="multipart/form-data">
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?= $tour['id'] ?>">
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Tour Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($tour['title'] ?? '') ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Price ($)</label>
                <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($tour['price'] ?? '') ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($tour['slug'] ?? '') ?>" placeholder="leave-empty-to-auto-generate" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                <small style="color: #888;">Unique URL identifier.</small>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Duration</label>
                <input type="text" name="duration" value="<?= htmlspecialchars($tour['duration'] ?? '') ?>" placeholder="e.g. 3 Days, 5 Hours" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Short Description</label>
            <textarea name="short_description" rows="2" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;"><?= htmlspecialchars($tour['short_description'] ?? '') ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Description</label>
            <textarea name="description" rows="6" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;"><?= htmlspecialchars($tour['description'] ?? '') ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Inclusions (one per line)</label>
                <textarea name="inclusions" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;"><?= isset($tour['inclusions']) ? implode("\n", json_decode($tour['inclusions'], true)) : "" ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Exclusions (one per line)</label>
                <textarea name="exclusions" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;"><?= isset($tour['exclusions']) ? implode("\n", json_decode($tour['exclusions'], true)) : "" ?></textarea>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Tour Banner Image</label>
            <?php if (!empty($tour['image_url'])): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= htmlspecialchars($tour['image_url']) ?>" alt="Current Image" style="height: 120px; border-radius: 8px; object-fit: cover;">
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*, image/webp" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?= (!isset($tour['is_active']) || $tour['is_active']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <span style="font-weight: 500;">Is Active (Visible on site)</span>
            </label>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 25px;"><?= $isEdit ? 'Update Tour' : 'Create Tour' ?></button>
            <a href="/admin/tours" class="btn btn-outline" style="padding: 12px 25px; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>

<?php view('admin/footer'); ?>
