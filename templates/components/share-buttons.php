<?php
/**
 * Social Sharing Buttons Component
 */
$shareUrl = urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$shareTitle = urlencode($item['title'] . " - Discover on Yerevango");
?>

<div class="share-container" style="margin-top: 2rem; padding: 1.5rem; background: white; border-radius: 16px; border: 1px solid #f1f5f9;">
    <h4 style="margin-bottom: 1rem; font-size: 1rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
        <i class="fas fa-share-alt"></i> <?= __('share_this_place') ?? 'Share this place' ?>
    </h4>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="share-btn fb" title="Share on Facebook" style="background: #1877f2; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;">
            <i class="fab fa-facebook-f"></i>
        </a>
        
        <!-- Twitter (X) -->
        <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="share-btn tw" title="Share on X" style="background: #000000; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;">
            <i class="fab fa-x-twitter"></i>
        </a>
        
        <!-- Telegram -->
        <a href="https://t.me/share/url?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="share-btn tg" title="Share on Telegram" style="background: #0088cc; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;">
            <i class="fab fa-telegram-plane"></i>
        </a>
        
        <!-- Pinterest -->
        <a href="https://pinterest.com/pin/create/button/?url=<?= $shareUrl ?>&description=<?= $shareTitle ?>" target="_blank" class="share-btn pin" title="Share on Pinterest" style="background: #bd081c; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;">
            <i class="fab fa-pinterest-p"></i>
        </a>

        <!-- Copy Link -->
        <button onclick="copyToClipboard('<?= $_SERVER['REQUEST_URI'] ?>')" class="share-btn link" title="Copy Link" style="background: #e2e8f0; color: #475569; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: transform 0.2s;">
            <i class="fas fa-link"></i>
        </button>
    </div>
</div>

<script>
function copyToClipboard(url) {
    const fullUrl = window.location.origin + url;
    navigator.clipboard.writeText(fullUrl).then(() => {
        // Simple toast or alert
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check" style="color: #10b981;"></i>';
        setTimeout(() => {
            btn.innerHTML = originalContent;
        }, 2000);
    });
}
</script>

<style>
.share-btn:hover {
    transform: translateY(-3px) scale(1.1);
}
</style>
