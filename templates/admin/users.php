<?php view('admin/header', ['title' => __('users')]); ?>

<div style="background: white; padding: 2rem; border-radius: 8px;">

<div style="background: white; padding: 2rem; border-radius: 8px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; background: #f8f9fa;">
                <th style="padding: 15px;">ID</th>
                <th style="padding: 15px;">User</th>
                <th style="padding: 15px;">Role</th>
                <th style="padding: 15px;">Status</th>
                <th style="padding: 15px;">Joined</th>
                <th style="padding: 15px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 15px; font-weight: 600; color: #64748b;"><?= $u['id'] ?></td>
                <td style="padding: 15px;">
                    <div style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($u['username']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></div>
                </td>
                <td style="padding: 15px;">
                    <span style="background: <?= $u['role'] === 'admin' ? '#eff6ff' : '#f8fafc' ?>; color: <?= $u['role'] === 'admin' ? '#3b82f6' : '#64748b' ?>; font-size: 0.75rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; text-transform: uppercase;">
                        <?= htmlspecialchars($u['role']) ?>
                    </span>
                </td>
                <td style="padding: 15px;">
                    <span style="background: <?= ($u['status'] ?? 'active') === 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= ($u['status'] ?? 'active') === 'active' ? '#166534' : '#991b1b' ?>; font-size: 0.75rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; text-transform: uppercase;">
                        <?= htmlspecialchars($u['status'] ?? 'active') ?>
                    </span>
                </td>
                <td style="padding: 15px; color: #64748b; font-size: 0.9rem;">
                    <?= date('M j, Y', strtotime($u['created_at'])) ?>
                </td>
                <td style="padding: 15px; text-align: right;">
                    <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                        <!-- Toggle Status -->
                        <form action="/admin/users/toggle-status" method="POST" style="margin: 0;">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="status" value="<?= ($u['status'] ?? 'active') === 'active' ? 'disabled' : 'active' ?>">
                            <button type="submit" class="btn" style="padding: 6px 12px; font-size: 0.8rem; background: <?= ($u['status'] ?? 'active') === 'active' ? '#f59e0b' : '#10b981' ?>; color: white; border: none; border-radius: 6px;">
                                <?= ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable' ?>
                            </button>
                        </form>

                        <!-- Delete -->
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <form action="/admin/users/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this user?')">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn" style="padding: 6px 12px; font-size: 0.8rem; background: #ef4444; color: white; border: none; border-radius: 6px;">
                                Delete
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php view('admin/footer'); ?>
