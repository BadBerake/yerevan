<?php ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h3>User Management</h3>
        <div style="display: flex; gap: 10px;">
            <input type="text" placeholder="Search users..." style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <table style="margin: 0;">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Points</th>
                    <th>Joined</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user" style="font-size: 0.75rem; color: #6b7280;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem;"><?= htmlspecialchars($u['username']) ?></div>
                                <div style="font-size: 0.75rem; color: #6b7280;"><?= htmlspecialchars($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge" style="background: #e0e7ff; color: #3730a3;"><?= $u['role'] ?></span>
                    </td>
                    <td>
                        <?php if(!empty($u['is_verified'])): ?>
                            <span class="status-badge success">Verified</span>
                        <?php else: ?>
                            <span class="status-badge warning">Unverified</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 600; color: #6b7280;"><?= number_format($u['points']) ?></td>
                    <td style="color: #6b7280;"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                    <td class="text-right">
                        <form action="/admin/users/action" method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <?php if(empty($u['is_verified'])): ?>
                                <button type="submit" name="action" value="verify" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                    Verify
                                </button>
                            <?php endif; ?>
                            <button type="submit" name="action" value="ban" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: #ef4444; border-color: #fecaca;">
                                Ban
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
include __DIR__ . '/layout.php'; 
?>
