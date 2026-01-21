<?php view('header', ['title' => 'Community Hub']); ?>

<link rel="stylesheet" href="/css/components/community.css">

<div class="community-header">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Community Hub</h1>
        <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">Connect with fellow explorers, share experiences, and discover the best of Yerevan together.</p>
    </div>
</div>

<div class="container" style="margin-bottom: 5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.8rem; font-weight: 700;">Explore Groups</h2>
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-outline" style="padding: 8px 16px;">Trending</button>
            <button class="btn btn-outline" style="padding: 8px 16px;">Newest</button>
        </div>
    </div>

    <div class="group-grid">
        <?php foreach ($groups as $group): ?>
            <div class="group-card">
                <img src="<?= htmlspecialchars($group['image_url']) ?>" alt="<?= htmlspecialchars($group['name']) ?>" class="group-banner">
                <div class="group-info">
                    <div class="group-category"><?= htmlspecialchars($group['category']) ?></div>
                    <h3 style="margin-bottom: 10px; font-size: 1.3rem;"><a href="/communities/<?= $group['slug'] ?>" style="text-decoration: none; color: var(--text-main);"><?= htmlspecialchars($group['name']) ?></a></h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5; height: 4.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                        <?= htmlspecialchars($group['description']) ?>
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="member-count">
                            <i class="fas fa-users"></i> <?= number_format($group['member_count']) ?> members
                        </div>
                        <a href="/communities/<?= $group['slug'] ?>" class="btn btn-primary" style="padding: 8px 20px; border-radius: 10px; font-size: 0.9rem;">View Group</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php view('footer'); ?>
