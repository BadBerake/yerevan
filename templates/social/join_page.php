<?php view('header', ['title' => __('join') . ' ' . $community['name']]); ?>
    <div class="container" style="max-width: 800px; margin: 4rem auto; padding: 0 1.5rem;">
        
        <div style="padding: 3rem; text-align: center;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem; font-weight: 800;"><?= htmlspecialchars($community['name']) ?></h1>
            <div style="display: flex; gap: 1rem; justify-content: center; align-items: center; margin-bottom: 2rem;">
                <span style="background: #eff6ff; color: #3b82f6; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">👤 <?= __('created_by') ?> <?= htmlspecialchars($community['creator_name']) ?></span>
                <span style="background: #f8fafc; color: #64748b; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">📅 <?= __('since') ?> <?= date('F Y', strtotime($community['created_at'])) ?></span>
            </div>
            
            <p style="font-size: 1.15rem; line-height: 1.8; color: #4b5563; margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                <?= nl2br(htmlspecialchars($community['description'] ?: __('welcome_community_desc'))) ?>
            </p>
            
            <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <form action="/communities/join" method="POST">
                    <input type="hidden" name="community_id" value="<?= $community['id'] ?>">
                    <button type="submit" class="btn btn-primary" style="padding: 16px 60px; font-size: 1.2rem; border-radius: 50px; font-weight: 700; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);">
                        <?= __('join_community_btn') ?>
                    </button>
                </form>
                <a href="/communities" style="color: var(--text-muted); text-decoration: none; font-weight: 500;"><?= __('back_to_discovery') ?></a>
            </div>
         </div>
     </div>

<?php view('footer'); ?>
