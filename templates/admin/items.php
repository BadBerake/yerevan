<?php view('admin/header', ['title' => 'All Items']); ?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <div class="admin-topbar" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin:0;">Manage Items</h2>
            <p style="margin:0; color:#666; font-size:0.9rem;">View and manage all platform listings.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
             <!-- Search could go here -->
             <input type="text" placeholder="Filter items..." style="padding: 10px; border: 1px solid #eee; border-radius: 25px; width: 250px;">
             <a href="/admin/items/new" class="btn btn-primary" style="text-decoration:none;">+ Add New Item</a>
        </div>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; background: #f8f9fa;">
                <th style="padding: 12px; border-bottom: 2px solid #eee;">ID</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Title</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Category</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">User</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Status</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Created</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?= $item['id'] ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; width: 30%;">
                    <a href="/place/<?= $item['id'] ?>" target="_blank" style="font-weight: 500; color: #333; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                        <?= htmlspecialchars($item['title']) ?> 
                        <span style="font-size: 0.75rem; color: #999;">↗</span>
                    </a>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($item['category_name'] ?? 'N/A') ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($item['username'] ?? 'Unknown') ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <?php if($item['is_approved']): ?>
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">Live</span>
                    <?php else: ?>
                        <span style="background: #fff3e0; color: #ef6c00; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">Pending</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666; font-size: 0.9rem;"><?= date('M j', strtotime($item['created_at'])) ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <a href="/admin/items/edit?id=<?= $item['id'] ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; margin-right: 5px;">Edit</a>
                    
                    <form action="/admin/items/toggle-status" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <input type="hidden" name="status" value="<?= $item['is_approved'] ? 0 : 1 ?>">
                        <button type="submit" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; border-color: #333; color: #333;">
                            <?= $item['is_approved'] ? 'Unpublish' : 'Publish' ?>
                        </button>
                    </form>

                    <form action="/admin/items/delete" method="POST" style="display: inline; margin-left: 5px;" onsubmit="return confirm('Delete this item permanently?');">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; border-color: #ef5350; color: #ef5350;">Del</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php view('admin/footer'); ?>
