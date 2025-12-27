<?php view('admin/header', ['title' => 'Categories']); ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
    <a href="/admin/categories/new" class="btn btn-primary">+ New Category</a>
</div>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; background: #f8f9fa;">
                <th style="padding: 12px; border-bottom: 2px solid #eee;">ID</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Name</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Slug</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Type</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?= $cat['id'] ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: 500;"><?= htmlspecialchars($cat['name']) ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;"><?= htmlspecialchars($cat['slug']) ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; background: <?= $cat['type'] === 'place' ? '#e3f2fd' : '#fff3e0' ?>; color: <?= $cat['type'] === 'place' ? '#0d47a1' : '#e65100' ?>;">
                        <?= ucfirst($cat['type']) ?>
                    </span>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <a href="/admin/categories/edit?id=<?= $cat['id'] ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; margin-right: 5px;">Edit</a>
                    <form action="/admin/categories/delete" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This might affect items attached to this category.');">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <button type="submit" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; border-color: #ef5350; color: #ef5350;">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php view('admin/footer'); ?>
