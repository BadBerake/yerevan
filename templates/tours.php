<?php include 'header.php'; ?>

<section class="page-hero" style="background: linear-gradient(135deg, #D90012 0%, #a8000e 100%); padding: 6rem 0 4rem; color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;"><?= __('explore_tours') ?? 'Exquisite Tours' ?></h1>
        <p style="font-size: 1.2rem; opacity: 0.9; max-width: 700px; margin: 0 auto;"><?= __('tours_subtitle') ?? 'Discover the hidden gems of Armenia with our handpicked professional tours.' ?></p>
    </div>
</section>

<section class="tours-section" style="padding: 4rem 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <?php foreach ($tours as $tour): ?>
                <div class="tour-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: transform 0.3s ease;">
                    <div style="position: relative; height: 240px;">
                        <img src="<?= htmlspecialchars($tour['image_url'] ?: '/assets/images/default-tour.jpg') ?>" alt="<?= htmlspecialchars($tour['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.95); padding: 8px 15px; border-radius: 50px; font-weight: 700; color: var(--primary); font-size: 1.1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            $<?= number_format($tour['price']) ?>
                        </div>
                        <?php if ($tour['duration']): ?>
                            <div style="position: absolute; bottom: 20px; left: 20px; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); color: white; padding: 5px 12px; border-radius: 50px; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                                <i class="far fa-clock"></i> <?= htmlspecialchars($tour['duration']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.4rem; margin-bottom: 0.8rem; font-weight: 700; color: #1a1a1a;"><?= htmlspecialchars($tour['title']) ?></h3>
                        <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($tour['short_description']) ?>
                        </p>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <a href="/tour/<?= $tour['slug'] ?>" class="btn btn-primary" style="border-radius: 12px; padding: 10px 20px; width: 100%; justify-content: center; font-weight: 600;">
                                <?= __('view_details') ?? 'View Details' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($tours)): ?>
            <div style="text-align: center; padding: 4rem 0;">
                <img src="/assets/images/no-results.svg" alt="No tours" style="width: 200px; opacity: 0.5; margin-bottom: 2rem;">
                <h3><?= __('no_tours_found') ?? 'No tours available at the moment.' ?></h3>
                <p><?= __('check_back_later') ?? 'Please check back later for exciting new adventures.' ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.tour-card:hover {
    transform: translateY(-10px);
}
</style>

<?php include 'footer.php'; ?>
