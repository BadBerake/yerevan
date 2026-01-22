<?php view('admin/header', ['title' => 'Manage Tours']); ?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <div class="admin-topbar" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin:0;">Manage Tours Sale</h2>
            <p style="margin:0; color:#666; font-size:0.9rem;">Create and manage tours for users to buy.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
             <a href="/admin/tours/new" class="btn btn-primary" style="text-decoration:none;">+ Add New Tour</a>
        </div>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; background: #f8f9fa;">
                <th style="padding: 12px; border-bottom: 2px solid #eee;">ID</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Title</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Price</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Duration</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Status</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tours as $tour): ?>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?= $tour['id'] ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; width: 40%;">
                    <a href="/tour/<?= $tour['slug'] ?>" target="_blank" style="font-weight: 500; color: #333; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                        <?= htmlspecialchars($tour['title']) ?> 
                        <span style="font-size: 0.75rem; color: #999;">↗</span>
                    </a>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">$<?= number_format($tour['price'], 2) ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($tour['duration'] ?? 'N/A') ?></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <?php if($tour['is_active']): ?>
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">Active</span>
                    <?php else: ?>
                        <span style="background: #ffebee; color: #c62828; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">Inactive</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <a href="/admin/tours/edit?id=<?= $tour['id'] ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; margin-right: 5px;">Edit</a>
                    
                    <form action="/admin/tours/toggle-status" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                        <input type="hidden" name="status" value="<?= $tour['is_active'] ? 0 : 1 ?>">
                        <button type="submit" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; border-color: #333; color: #333;">
                            <?= $tour['is_active'] ? 'Deactivate' : 'Activate' ?>
                        </button>
                    </form>

                    <form action="/admin/tours/delete" method="POST" style="display: inline; margin-left: 5px;" onsubmit="return confirm('Delete this tour permanently?');">
                        <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                        <button type="submit" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.85rem; border-color: #ef5350; color: #ef5350;">Del</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($tours)): ?>
            <tr>
                <td colspan="6" style="padding: 2rem; text-align: center; color: #999;">No tours found. Create your first tour!</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php view('admin/footer'); ?>
