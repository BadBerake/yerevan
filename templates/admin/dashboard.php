<?php ob_start(); ?>

<div class="grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Stat Card 1 -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 500;">Total Users</div>
            <div style="font-size: 2rem; font-weight: 700; color: #111827;"><?= number_format($stats['total_users']) ?></div>
            <div style="font-size: 0.75rem; color: #10b981; margin-top: 0.5rem;">
                <i class="fas fa-arrow-up"></i> <?= $stats['new_users_24h'] ?? 0 ?> last 24h
            </div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 500;">Total Reviews</div>
            <div style="font-size: 2rem; font-weight: 700; color: #111827;"><?= number_format($stats['total_reviews']) ?></div>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 500;">Total Events</div>
            <div style="font-size: 2rem; font-weight: 700; color: #111827;"><?= number_format($stats['total_events']) ?></div>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body">
            <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 500;">Page Views</div>
            <div style="font-size: 2rem; font-weight: 700; color: #111827;"><?= number_format($stats['total_views']) ?></div>
        </div>
    </div>
</div>

<div class="grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Activity</h3>
            <a href="/admin/users" style="font-size: 0.875rem; color: var(--primary); text-decoration: none;">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <table style="margin: 0;">
                <thead>
                    <tr>
                        <th style="border-top: none;">User</th>
                        <th style="border-top: none;">Action</th>
                        <th style="border-top: none;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr><td colspan="3" style="text-align: center; color: #9ca3af;">No recent activity</td></tr>
                    <?php else: ?>
                        <?php foreach($recentActivity as $activity): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($activity['name']) ?></div>
                            </td>
                            <td>
                                <?php if($activity['type'] == 'joined'): ?>
                                    <span class="status-badge success">Joined Platform</span>
                                <?php elseif($activity['type'] == 'reviewed'): ?>
                                    <span class="status-badge warning">Wrote Review</span>
                                <?php else: ?>
                                    <span class="status-badge"><?= $activity['type'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #6b7280;"><?= date('M d, H:i', strtotime($activity['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3>Quick Actions</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="/admin/users" class="btn btn-outline" style="justify-content: flex-start;">
                    <i class="fas fa-user-plus" style="margin-right: 10px; color: var(--primary);"></i> Verify Users
                </a>
                <a href="/admin/content" class="btn btn-outline" style="justify-content: flex-start;">
                    <i class="fas fa-check-circle" style="margin-right: 10px; color: #10b981;"></i> Approve Reviews
                </a>
                <a href="/admin/settings" class="btn btn-outline" style="justify-content: flex-start;">
                    <i class="fas fa-cog" style="margin-right: 10px; color: #6b7280;"></i> System Settings
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
include __DIR__ . '/layout.php'; 
?>
