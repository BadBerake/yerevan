<?php view('admin/header', ['title' => __('admin_dashboard')]); ?>

<!-- Stats Grid -->
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Users Widget -->
    <div class="admin-card" style="border-left: 5px solid #3498db;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div style="color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; font-weight: 600;"><?= __('total_users') ?></div>
                <div style="font-size: 2.5rem; font-weight: 700; color: #2c3e50; margin: 10px 0;"><?= $stats['users'] ?></div>
                <div style="color: #27ae60; font-size: 0.85rem;">Registered Users</div>
            </div>
            <div style="font-size: 2.5rem; opacity: 0.2;">👥</div>
        </div>
    </div>

    <!-- Items Widget -->
    <div class="admin-card" style="border-left: 5px solid #e67e22;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div style="color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; font-weight: 600;"><?= __('total_items') ?></div>
                <div style="font-size: 2.5rem; font-weight: 700; color: #2c3e50; margin: 10px 0;"><?= $stats['items'] ?></div>
                <div style="color: #27ae60; font-size: 0.85rem;">Places & Events</div>
            </div>
            <div style="font-size: 2.5rem; opacity: 0.2;">🏢</div>
        </div>
    </div>

    <!-- Pending Widget -->
    <div class="admin-card" style="border-left: 5px solid #e74c3c;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div style="color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; font-weight: 600;"><?= __('pending_items') ?></div>
                <div style="font-size: 2.5rem; font-weight: 700; color: #2c3e50; margin: 10px 0;"><?= $stats['pending'] ?></div>
                <a href="/admin/approvals" style="color: #e74c3c; font-size: 0.85rem; font-weight: 600; text-decoration: none;">Review Pending →</a>
            </div>
            <div style="font-size: 2.5rem; opacity: 0.2;">⏳</div>
        </div>
    </div>

    <!-- Revenue Widget -->
    <div class="admin-card" style="border-left: 5px solid #9b59b6;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div style="color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; font-weight: 600;">Revenue</div>
                <div style="font-size: 2.5rem; font-weight: 700; color: #2c3e50; margin: 10px 0;">$<?= number_format($stats['revenue']) ?></div>
                <div style="color: #27ae60; font-size: 0.85rem;">Ticket Sales</div>
            </div>
            <div style="font-size: 2.5rem; opacity: 0.2;">💰</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Main Activity Feed -->
    <div class="admin-card">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 15px; color: #2c3e50;">Platform Activity</h3>
        
        <?php foreach($recent_activity as $act): ?>
        <div style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3498db;">👤</div>
            <div>
                <div style="font-weight: 600; color: #2c3e50;">New User Registered</div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">User <strong><?= htmlspecialchars($act['username']) ?></strong> joined the platform.</div>
                <div style="color: #bdc3c7; font-size: 0.8rem; margin-top: 5px;"><?= date('M j, Y H:i', strtotime($act['created_at'])) ?></div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($recent_activity)): ?>
            <p style="color: #999; text-align: center;">No recent activity.</p>
        <?php endif; ?>
    </div>

    <!-- Quick Links / System Health -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="admin-card">
            <h3 style="margin-top: 0; margin-bottom: 1rem; color: #2c3e50;">Quick Actions</h3>
            <a href="/admin/categories/new" class="btn btn-primary" style="display: block; text-align: center; margin-bottom: 10px;">+ Add Category</a>
            <a href="/admin/users" class="btn btn-outline" style="display: block; text-align: center; width: 100%;">Manage Users</a>
        </div>

        <div class="admin-card" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: white;">
            <h3 style="margin-top: 0; margin-bottom: 0.5rem;">System Health</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>CPU Usage</span>
                <span>12%</span>
            </div>
            <div style="width: 100%; background: rgba(255,255,255,0.1); height: 6px; border-radius: 3px; margin-bottom: 15px;">
                <div style="width: 12%; background: #2ecc71; height: 100%; border-radius: 3px;"></div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span>Memory</span>
                <span>45%</span>
            </div>
            <div style="width: 100%; background: rgba(255,255,255,0.1); height: 6px; border-radius: 3px;">
                <div style="width: 45%; background: #f1c40f; height: 100%; border-radius: 3px;"></div>
            </div>
        </div>
    </div>
</div>

<?php view('admin/footer'); ?>
