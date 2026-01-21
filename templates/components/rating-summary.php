<?php
/**
 * Rating Summary Component
 * Displays overall score and bar distribution
 */
$itemId = $item['id'];
$distribution = $reviewService->getRatingDistribution($itemId);
$total = $item['review_count'] ?? 0;
$avg = $item['rating_average'] ?? 0;
?>

<div class="rating-summary-card">
    <div class="rating-large-score">
        <div class="rating-score-num"><?= $avg ?: '0' ?></div>
        <div class="rating-stars-large">
            <?php for($i=1; $i<=5; $i++): ?>
                <i class="<?= $i <= round($avg) ? 'fas' : 'far' ?> fa-star"></i>
            <?php endfor; ?>
        </div>
        <div class="rating-count-total"><?= $total ?> <?= __('reviews') ?></div>
    </div>
    
    <div class="rating-bars">
        <?php for($i=5; $i>=1; $i--): 
            $count = $distribution[$i] ?? 0;
            $percent = $total > 0 ? round(($count / $total) * 100) : 0;
        ?>
        <div class="rating-bar-row">
            <span class="rating-bar-label"><?= $i ?> <i class="fas fa-star" style="font-size: 0.7rem; color: #f59e0b;"></i></span>
            <div class="rating-bar-bg">
                <div class="rating-bar-fill" style="width: <?= $percent ?>%"></div>
            </div>
            <span class="rating-bar-percent"><?= $percent ?>%</span>
        </div>
        <?php endfor; ?>
    </div>
    
    <div>
        <a href="#write-review" class="btn btn-primary" style="white-space: nowrap;">
            <?= __('write_review') ?>
        </a>
    </div>
</div>
