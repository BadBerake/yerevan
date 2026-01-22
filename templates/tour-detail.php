<?php include 'header.php'; ?>

<section class="tour-detail-hero" style="height: 60vh; position: relative; overflow: hidden; display: flex; align-items: center; background: #000;">
    <img src="<?= htmlspecialchars($tour['image_url'] ?: '/assets/images/default-tour.jpg') ?>" alt="<?= htmlspecialchars($tour['title']) ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
    <div class="container" style="position: relative; z-index: 2; color: white;">
        <div style="max-width: 800px;">
            <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1.5rem; line-height: 1.2;"><?= htmlspecialchars($tour['title']) ?></h1>
            <div style="display: flex; gap: 20px; font-size: 1.1rem; flex-wrap: wrap;">
                <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 8px 20px; border-radius: 50px; display: flex; align-items: center; gap: 8px;">
                    <i class="far fa-clock"></i> <?= htmlspecialchars($tour['duration']) ?>
                </div>
                <div style="background: var(--primary); padding: 8px 25px; border-radius: 50px; display: flex; align-items: center; gap: 8px; font-weight: 700;">
                    <i class="fas fa-tag"></i> $<?= number_format($tour['price']) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="padding: 4rem 0; background: #f9f9f9;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
            <div class="tour-content">
                <div style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 2rem;">
                    <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; font-weight: 700; color: #1a1a1a;">Tour Description</h2>
                    <div style="color: #444; line-height: 1.8; font-size: 1.1rem; white-space: pre-line;">
                        <?= htmlspecialchars($tour['description']) ?>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                        <h3 style="font-size: 1.3rem; margin-bottom: 1.5rem; font-weight: 700; color: #2e7d32; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle"></i> What's Included
                        </h3>
                        <ul style="list-style: none; padding: 0;">
                            <?php 
                            $inclusions = json_decode($tour['inclusions'], true);
                            if ($inclusions):
                                foreach ($inclusions as $item): ?>
                                    <li style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px; color: #555;">
                                        <i class="fas fa-check" style="color: #2e7d32; margin-top: 4px; font-size: 0.9rem;"></i>
                                        <?= htmlspecialchars($item) ?>
                                    </li>
                            <?php endforeach; 
                            else: ?>
                                <li style="color: #999;">No specific inclusions listed.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                        <h3 style="font-size: 1.3rem; margin-bottom: 1.5rem; font-weight: 700; color: #c62828; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-times-circle"></i> What's Excluded
                        </h3>
                        <ul style="list-style: none; padding: 0;">
                            <?php 
                            $exclusions = json_decode($tour['exclusions'], true);
                            if ($exclusions):
                                foreach ($exclusions as $item): ?>
                                    <li style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px; color: #555;">
                                        <i class="fas fa-times" style="color: #c62828; margin-top: 4px; font-size: 0.9rem;"></i>
                                        <?= htmlspecialchars($item) ?>
                                    </li>
                            <?php endforeach; 
                            else: ?>
                                <li style="color: #999;">No specific exclusions listed.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tour-sidebar">
                <div style="background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: sticky; top: 100px;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <span style="display: block; font-size: 0.9rem; color: #888; text-transform: uppercase; letter-spacing: 1px;">Price per person</span>
                        <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary); margin: 0.5rem 0;">$<?= number_format($tour['price']) ?></h2>
                    </div>
                    
                    <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #444;">
                            <span><i class="far fa-clock"></i> Duration</span>
                            <span style="font-weight: 600;"><?= htmlspecialchars($tour['duration']) ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; color: #444;">
                            <span><i class="far fa-calendar-alt"></i> Availability</span>
                            <span style="font-weight: 600;">Contact us</span>
                        </div>
                    </div>

                    <a href="/contact?subject=Booking Tour: <?= urlencode($tour['title']) ?>" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 15px; border-radius: 12px; font-size: 1.1rem; font-weight: 700;">
                        Book This Tour
                    </a>
                    
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee; text-align: center;">
                        <p style="font-size: 0.85rem; color: #888;">Need help with your booking?</p>
                        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 10px;">
                            <a href="https://wa.me/374000000" style="color: #25D366; font-size: 1.5rem;"><i class="fab fa-whatsapp"></i></a>
                            <a href="tel:+374000000" style="color: #333; font-size: 1.5rem;"><i class="fas fa-phone"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
