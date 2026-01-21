<?php
/**
 * Review Form Component
 * Allows logged-in users to submit a review
 */
global $auth, $reviewService;
$isLoggedIn = $auth->isLoggedIn();
$hasReviewed = $isLoggedIn ? $reviewService->hasUserReviewed($auth->getUser()['id'], $item['id']) : false;
?>

<div class="review-form-container" id="write-review">
    <?php if (!$isLoggedIn): ?>
        <div style="text-align: center; padding: 1rem;">
            <h3><?= __('share_your_experience') ?></h3>
            <p><?= __('login_to_write_review') ?></p>
            <a href="/login" class="btn btn-primary" style="margin-top: 1rem;"><?= __('login') ?></a>
        </div>
    <?php elseif ($hasReviewed): ?>
        <div style="text-align: center; padding: 1rem;">
            <i class="fas fa-check-circle" style="font-size: 3rem; color: #10b981; margin-bottom: 1rem;"></i>
            <h3><?= __('thanks_for_review') ?></h3>
            <p><?= __('you_already_reviewed') ?></p>
        </div>
    <?php else: ?>
        <h3 style="margin-bottom: 2rem;"><?= __('write_a_review') ?></h3>
        <form id="review-submission-form">
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
            
            <div class="star-rating-input">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="1" id="star1" required><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
            </div>
            
            <div class="form-group mb-3">
                <label style="font-weight: 600; margin-bottom: 5px;"><?= __('title') ?></label>
                <input type="text" name="title" class="form-control" placeholder="Summarize your visit" style="border-radius: 12px; padding: 12px;">
            </div>
            
            <div class="form-group mb-4">
                <label style="font-weight: 600; margin-bottom: 5px;"><?= __('content') ?></label>
                <textarea name="content" class="form-control" rows="5" placeholder="Tell us what you liked (or didn't like)..." style="border-radius: 12px; padding: 12px;" required></textarea>
            </div>
            
            <!-- Optional: Image Upload Simulation for now -->
            <div class="form-group mb-4">
                <label style="font-weight: 600; margin-bottom: 5px;"><?= __('add_photos') ?></label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <div style="width: 80px; height: 80px; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #94a3b8;">
                        <i class="fas fa-plus"></i>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 200px; border-radius: 50px; font-weight: 700;">
                <?= __('submit_review') ?>
            </button>
        </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('review-submission-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    try {
        const response = await fetch('/api/reviews/create', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            window.location.reload();
        } else {
            alert('Error: ' + result.message);
            btn.disabled = false;
            btn.textContent = 'Submit Review';
        }
    } catch (error) {
        console.error('Error:', error);
        btn.disabled = false;
        btn.textContent = 'Submit Review';
    }
});

async function voteReview(reviewId, type, btn) {
    <?php if (!$isLoggedIn): ?>
    window.location.href = '/login';
    return;
    <?php endif; ?>
    
    try {
        const response = await fetch('/api/reviews/vote', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ review_id: reviewId, vote_type: type })
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            btn.classList.toggle('active');
            // Optimistic update of UI would go here
            window.location.reload();
        }
    } catch (error) {
        console.error('Error voting:', error);
    }
}
</script>
