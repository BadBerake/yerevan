<?php
/**
 * Review Card Component
 * Displays a single user review
 */
?>
<div class="review-card" id="review-<?= $review['id'] ?>">
    <div class="review-header">
        <div class="review-user">
            <img src="<?= $review['avatar_url'] ?: '/public/img/default-avatar.png' ?>" alt="<?= htmlspecialchars($review['username']) ?>" class="user-avatar">
            <div class="user-info">
                <div class="user-name">
                    <?= htmlspecialchars($review['username']) ?>
                    <?php if (!empty($review['is_verified'])): ?>
                        <i class="fas fa-check-circle" style="color: #3b82f6; font-size: 0.8rem;" title="Verified Visit"></i>
                    <?php endif; ?>
                </div>
                <div class="review-date"><?= date('M d, Y', strtotime($review['created_at'])) ?></div>
            </div>
        </div>
        <div class="review-rating-stars">
            <?php for($i=1; $i<=5; $i++): ?>
                <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
            <?php endfor; ?>
        </div>
    </div>
    
    <div class="review-content">
        <?php if (!empty($review['title'])): ?>
            <h4 class="review-title"><?= htmlspecialchars($review['title']) ?></h4>
        <?php endif; ?>
        <p class="review-text"><?= nl2br(htmlspecialchars($review['content'])) ?></p>
    </div>
    
    <?php if (!empty($review['images'])): ?>
    <div class="review-images">
        <?php foreach ($review['images'] as $img): ?>
            <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Review photo" class="review-img" onclick="openLightbox(this.src)">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="review-footer">
        <button class="helpful-btn <?= (isset($userVotes[$review['id']]) && $userVotes[$review['id']] == 'helpful') ? 'active' : '' ?>" 
                onclick="voteReview(<?= $review['id'] ?>, 'helpful', this)">
            <i class="far fa-thumbs-up"></i>
            <?= __('helpful') ?> <span class="vote-count"><?= $review['helpful_count'] ?: '' ?></span>
        </button>
        
        <button class="btn btn-outline" style="border: none; color: #94a3b8; font-size: 0.8rem;">
            <i class="fas fa-flag"></i> <?= __('report') ?>
        </button>
    </div>
</div>
