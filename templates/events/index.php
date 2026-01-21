<?php view('header', ['title' => __('events_title')]); ?>

<div class="events-hero" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 4rem 0; margin-bottom: 3rem; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem;"><?= __('events_title') ?></h1>
        <p style="font-size: 1.25rem; opacity: 0.9; max-width: 700px; margin: 0 auto;">Discover concerts, festivals, and cultural gatherings in the heart of Yerevan.</p>
    </div>
</div>

<div class="container" style="margin-bottom: 5rem;">
    <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
        <?php foreach ($events as $event): ?>
            <div class="event-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid #f1f5f9; transition: transform 0.3s ease;">
                <div style="position: relative; height: 200px;">
                    <img src="<?= htmlspecialchars($event['image_url']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); padding: 8px 15px; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                        <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--primary);">
                            <?= date('M', strtotime($event['event_date'])) ?>
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 800; color: #1e293b;">
                            <?= date('d', strtotime($event['event_date'])) ?>
                        </div>
                    </div>
                    <?php if ($event['ticket_price'] > 0): ?>
                        <div style="position: absolute; bottom: 15px; right: 15px; background: var(--primary); color: white; padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                            From <?= number_format($event['ticket_price']) ?> AMD
                        </div>
                    <?php else: ?>
                        <div style="position: absolute; bottom: 15px; right: 15px; background: #10b981; color: white; padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.9rem;">
                            FREE
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 10px; font-size: 1.4rem;">
                        <a href="/place/<?= $event['id'] ?>" style="text-decoration: none; color: #1e293b;"><?= htmlspecialchars($event['title']) ?></a>
                    </h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.6; height: 3rem; overflow: hidden;">
                        <?= htmlspecialchars($event['description']) ?>
                    </p>
                    
                    <div style="display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['address']) ?>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            <i class="far fa-clock"></i> <?= date('H:i', strtotime($event['event_date'])) ?>
                        </div>
                        <a href="/place/<?= $event['id'] ?>" class="btn btn-primary" style="padding: 10px 20px; border-radius: 12px;">Get Tickets</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($events)): ?>
        <div style="text-align: center; padding: 5rem 0;">
            <i class="far fa-calendar-times" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3>No upcoming events found</h3>
            <p style="color: var(--text-muted);">Check back later for new experiences!</p>
        </div>
    <?php endif; ?>
</div>

<style>
.event-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}
</style>

<?php view('footer'); ?>
