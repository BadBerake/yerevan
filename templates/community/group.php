<?php view('header', ['title' => $group['name']]); ?>

<link rel="stylesheet" href="/css/components/community.css">

<div class="container" style="margin-top: 2rem;">
    <!-- Breadcrumbs -->
    <nav style="margin-bottom: 2rem; font-size: 0.9rem; color: var(--text-muted);">
        <a href="/communities" style="color: var(--primary); text-decoration: none;">Community Hub</a> / 
        <span><?= htmlspecialchars($group['name']) ?></span>
    </nav>

    <div class="group-hero">
        <img src="<?= htmlspecialchars($group['image_url']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <div class="group-hero-overlay">
            <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 2px; color: #fbbf24;"><?= htmlspecialchars($group['category']) ?></div>
            <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0;"><?= htmlspecialchars($group['name']) ?></h1>
            <div style="display: flex; align-items: center; gap: 20px; margin-top: 15px;">
                <span style="display: flex; align-items: center; gap: 6px;"><i class="fas fa-users"></i> <?= number_format($group['member_count']) ?> members</span>
                
                <?php if ($isMember): ?>
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; backdrop-filter: blur(5px);">Member ✓</span>
                <?php else: ?>
                    <button onclick="joinGroup(<?= $group['id'] ?>)" id="joinBtn" class="btn btn-primary" style="padding: 10px 25px; border-radius: 20px;">Join Community</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;">
        <!-- Feed Column -->
        <div class="feed-column">
            <!-- Create Post Box -->
            <?php if ($isMember): ?>
                <div class="discussion-box">
                    <h4 style="margin-bottom: 1rem;">Start a discussion</h4>
                    <form id="postForm">
                        <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                        <input type="text" name="title" placeholder="Topic title (optional)" class="discussion-input" style="font-weight: 600; padding: 12px;">
                        <textarea name="content" rows="4" placeholder="What's on your mind about <?= htmlspecialchars($group['name']) ?>?" class="discussion-input" required></textarea>
                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary">Post Discussion</button>
                        </div>
                    </form>
                </div>
            <?php elseif (global_auth()->isLoggedIn()): ?>
                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; text-align: center; border: 2px dashed #cbd5e1; margin-bottom: 2rem;">
                    <p style="margin-bottom: 1rem; color: var(--text-muted);">You must be a member to participate in discussions.</p>
                    <button onclick="joinGroup(<?= $group['id'] ?>)" class="btn btn-outline">Join to Post</button>
                </div>
            <?php else: ?>
                <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; text-align: center; border: 2px dashed #cbd5e1; margin-bottom: 2rem;">
                    <p style="margin-bottom: 1rem; color: var(--text-muted);">Log in to join the conversation.</p>
                    <a href="/login" class="btn btn-primary">Sign In</a>
                </div>
            <?php endif; ?>

            <!-- Posts List -->
            <div id="postsList">
                <?php if (empty($posts)): ?>
                    <div style="text-align: center; padding: 4rem; color: var(--text-muted);">
                        <i class="far fa-comments" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem; display: block;"></i>
                        <p>No discussions yet. Be the first to start one!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <div class="post-user">
                                <img src="<?= $post['avatar_url'] ?: 'https://via.placeholder.com/50' ?>" class="user-avatar">
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= htmlspecialchars($post['username']) ?>
                                        <?php if ($post['is_verified']): ?>
                                            <i class="fas fa-check-circle" style="color: var(--primary); font-size: 0.8rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('M d, Y • H:i', strtotime($post['created_at'])) ?></div>
                                </div>
                            </div>
                            <?php if ($post['title']): ?>
                                <h4 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-main);"><?= htmlspecialchars($post['title']) ?></h4>
                            <?php endif; ?>
                            <div class="post-content">
                                <?= nl2br(htmlspecialchars($post['content'])) ?>
                            </div>
                            <div class="post-actions">
                                <button class="action-btn"><i class="far fa-thumbs-up"></i> <?= $post['likes_count'] ?> Likes</button>
                                <button class="action-btn"><i class="far fa-comment"></i> <?= $post['comments_count'] ?> Comments</button>
                                <button class="action-btn"><i class="far fa-share-square"></i> Share</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="sidebar-column">
            <div style="background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem;">About Group</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">
                    <?= htmlspecialchars($group['description']) ?>
                </p>
                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #f1f5f9;">
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    <div style="margin-bottom: 10px;"><i class="fas fa-calendar-alt" style="width: 20px;"></i> Founded <?= date('M Y', strtotime($group['created_at'])) ?></div>
                    <div><i class="fas fa-tag" style="width: 20px;"></i> <?= htmlspecialchars($group['category']) ?></div>
                </div>
            </div>

            <!-- Group Rules -->
            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 16px; border: 1px solid #f1f5f9;">
                <h4 style="margin-bottom: 1rem;">Group Rules</h4>
                <ul style="font-size: 0.85rem; color: var(--text-muted); padding-left: 20px; line-height: 1.8;">
                    <li>Be respectful to others</li>
                    <li>No spam or self-promotion</li>
                    <li>Keep it related to Yerevan</li>
                    <li>Help each other out!</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
async function joinGroup(groupId) {
    try {
        const response = await fetch('/api/community/join', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ group_id: groupId })
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            const btn = document.getElementById('joinBtn');
            if (btn) {
                btn.innerHTML = 'Member ✓';
                btn.className = 'btn btn-outline';
                btn.disabled = true;
            }
            window.location.reload(); // To show the post form
        } else if (data.status === 'error' && data.message === 'Not logged in') {
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Error joining group:', error);
    }
}

document.getElementById('postForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/api/community/post', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert('Failed to post discussion. Please try again.');
        }
    } catch (error) {
        console.error('Error posting:', error);
    }
});
</script>

<?php view('footer'); ?>
