<?php view('header', ['title' => 'My Tickets']); ?>

<div class="container" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>My Tickets</h1>
        <a href="/dashboard" class="btn btn-outline">Back to Dashboard</a>
    </div>

    <div class="grid">
        <?php foreach ($tickets as $ticket): ?>
        <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex;">
            <div style="background: var(--primary); width: 10px;"></div>
            <div style="padding: 1.5rem; flex: 1;">
                <h3 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($ticket['title']) ?></h3>
                <div style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                    Date: <?= date('M j, Y', strtotime($ticket['event_date'] ?? 'now')) ?>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                    <div>
                        <span style="display: block; font-size: 0.8rem; color: #666;">Quantity</span>
                        <strong><?= $ticket['quantity'] ?></strong>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 0.8rem; color: #666;">Total Paid</span>
                        <strong style="color: var(--color-arm-red);"><?= number_format($ticket['total_price'], 2) ?> ֏</strong>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 1rem;">
                    <button class="btn btn-outline" style="width: 100%; border-style: dashed;">Download Ticket</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($tickets)): ?>
    <div style="text-align: center; padding: 4rem; background: white; border-radius: 10px;">
        <p style="color: #666; margin-bottom: 1rem;">You haven't purchased any tickets yet.</p>
        <a href="/events" class="btn btn-primary">Browse Events</a>
    </div>
    <?php endif; ?>
</div>

<?php view('footer'); ?>
