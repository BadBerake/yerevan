<?php view('header', ['title' => $title]); ?>

<div style="display: flex; gap: 2rem; padding: 2rem 0;">
    <!-- Sidebar -->
    <div style="flex: 0 0 250px; background: white; padding: 1.5rem; border-radius: 10px; height: fit-content; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 80px; height: 80px; background: var(--bg-light); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem;">👤</div>
            <h3><?= htmlspecialchars($user['username']) ?></h3>
            <span style="font-size: 0.8rem; padding: 3px 8px; background: #eee; border-radius: 10px;"><?= htmlspecialchars($user['role']) ?></span>
        </div>
        
        <nav style="display: flex; flex-direction: column; gap: 5px;">
            <a href="/dashboard" class="btn btn-primary" style="text-align: left;"><?= __('overview') ?></a>
            <a href="/dashboard/reservations" class="btn btn-outline" style="text-align: left; border: none;"><?= __('my_reservations') ?></a>
            <a href="/dashboard/tickets" class="btn btn-outline" style="text-align: left; border: none;"><?= __('my_tickets') ?></a>
            <a href="/dashboard/support" class="btn btn-outline" style="text-align: left; border: none;"><?= __('support_tickets') ?></a>
            <?php if ($user['role'] === 'admin'): ?>
            <hr style="margin: 10px 0; border-top: 1px solid #eee;">
            <a href="/admin" class="btn btn-outline" style="text-align: left; border: none; color: var(--color-arm-red);"><?= __('admin_panel') ?></a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1;">
        <h2 style="margin-bottom: 1.5rem;"><?= __('dashboard') ?></h2>
        
        <div class="grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #666;"><?= __('upcoming_reservations') ?></div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">0</div>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #666;"><?= __('active_tickets') ?></div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--secondary);">0</div>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #666;"><?= __('favorites') ?></div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--accent);">5</div>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0;">✨ <?= __('recommended_for_you') ?></h3>
                <?php if (empty($recommendations)): ?>
                    <a href="/onboarding" style="font-size: 0.85rem; color: var(--primary); text-decoration: none;"><?= __('set_interests') ?></a>
                <?php endif; ?>
            </div>
            
            <?php if (empty($recommendations)): ?>
                <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <p style="color: #64748b; margin-bottom: 1rem;"><?= __('select_interests_desc') ?></p>
                    <a href="/onboarding" class="btn btn-outline" style="font-size: 0.9rem;"><?= __('pick_my_interests') ?></a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                    <?php foreach ($recommendations as $item): ?>
                        <a href="/place/<?= $item['id'] ?>" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div style="border-radius: 8px; overflow: hidden; border: 1px solid #eee; height: 100%;">
                                <img src="<?= $item['image_url'] ?: 'https://via.placeholder.com/200x120' ?>" style="width: 100%; height: 120px; object-fit: cover;">
                                <div style="padding: 10px;">
                                    <div style="font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: uppercase; margin-bottom: 4px;"><?= htmlspecialchars($item['category_name']) ?></div>
                                    <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 2px;"><?= htmlspecialchars($item['title']) ?></div>
                                    <div style="font-size: 0.8rem; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($item['address']) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3><?= __('recent_activity') ?></h3>
            <p style="color: #666; font-style: italic;"><?= __('no_recent_activity') ?></p>
        </div>
    </div>
</div>

<?php view('footer'); ?>
