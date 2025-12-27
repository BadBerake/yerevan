<?php view('admin/header', ['title' => 'Manage Communities']); ?>

<div class="header-section" style="margin-bottom: 2rem;">
    <div>
        <h2>Community Management</h2>
        <p style="color: var(--text-muted);">Moderate social groups and members</p>
    </div>
</div>

<div style="background: white; border-radius: 12px; box-shadow: var(--shadow-sm); overflow: hidden; border: 1px solid #f1f5f9;">
    <?php if (empty($communities)): ?>
        <div style="padding: 4rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🏘️</div>
            <p>No communities created yet.</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Community</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Creator</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Stats</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Status</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($communities as $c): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fbfcfe'" onmouseout="this.style.background='white'">
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="<?= $c['image_url'] ?: '/public/img/placeholder.jpg' ?>" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($c['name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">/community/<?= $c['slug'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 500;"><?= htmlspecialchars($c['creator_name']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($c['created_at'])) ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <span style="background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                👥 <?= $c['member_count'] ?>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; background: <?= $c['status'] == 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $c['status'] == 'active' ? '#166534' : '#991b1b' ?>;">
                                <?= $c['status'] ?>
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <form action="/admin/communities/toggle-status" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $c['status'] == 'active' ? 'disabled' : 'active' ?>">
                                    <button type="submit" class="btn" style="padding: 6px 14px; font-size: 0.85rem; background: <?= $c['status'] == 'active' ? '#f59e0b' : '#10b981' ?>; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                        <?= $c['status'] == 'active' ? 'Disable' : 'Enable' ?>
                                    </button>
                                </form>
                                
                                <form action="/admin/communities/delete" method="POST" style="display: inline;" onsubmit="return confirm('ARE YOU SURE? This will delete all messages and member records.')">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn" style="padding: 6px 14px; font-size: 0.85rem; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                        Delete
                                    </button>
                                </form>
                                
                                <a href="/community/<?= $c['slug'] ?>" class="btn" style="padding: 6px 14px; font-size: 0.85rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none;">View</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php view('admin/footer'); ?>
