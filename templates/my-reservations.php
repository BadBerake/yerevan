<?php view('header', ['title' => 'My Reservations']); ?>

<div class="container" style="max-width: 1000px; margin: 3rem auto; padding: 0 1.5rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 2rem;">My Reservations</h1>
    
    <?php if (empty($reservations)): ?>
        <div style="background: #f8fafc; padding: 4rem 2rem; border-radius: 20px; text-align: center; border: 2px dashed #cbd5e1;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📅</div>
            <h3 style="color: var(--text-main); margin-bottom: 1rem;">No Reservations Yet</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Start exploring and book your first table!</p>
            <a href="/" class="btn btn-primary">Explore Places</a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach($reservations as $res): ?>
                <?php
                $statusColors = [
                    'pending' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                    'confirmed' => ['bg' => '#dcfce7', 'text' => '#166534'],
                    'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                    'completed' => ['bg' => '#e0e7ff', 'text' => '#3730a3']
                ];
                $color = $statusColors[$res['status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                $isPast = strtotime($res['reservation_date']) < strtotime('today');
                ?>
                
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-lg); border: 1px solid #f1f5f9;">
                    <div style="display: flex; gap: 2rem; align-items: start;">
                        <?php if (!empty($res['item_image'])): ?>
                            <img src="<?= htmlspecialchars($res['item_image']) ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 12px;">
                        <?php endif; ?>
                        
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                <div>
                                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.5rem;">
                                        <a href="/place/<?= $res['item_id'] ?>" style="color: var(--text-main); text-decoration: none;">
                                            <?= htmlspecialchars($res['item_title']) ?>
                                        </a>
                                    </h3>
                                    <div style="display: inline-block; background: <?= $color['bg'] ?>; color: <?= $color['text'] ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; text-transform: capitalize;">
                                        <?= $res['status'] ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">Date & Time</div>
                                    <div style="font-weight: 600;">📅 <?= date('M d, Y', strtotime($res['reservation_date'])) ?> at <?= date('g:i A', strtotime($res['reservation_time'])) ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">Guests</div>
                                    <div style="font-weight: 600;">👥 <?= $res['guests'] ?> <?= $res['guests'] == 1 ? 'Person' : 'People' ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">Contact</div>
                                    <div style="font-weight: 600;">📧 <?= htmlspecialchars($res['email']) ?></div>
                                </div>
                            </div>
                            
                            <?php if (!empty($res['special_requests'])): ?>
                                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">Special Requests</div>
                                    <div style="color: var(--text-main);"><?= nl2br(htmlspecialchars($res['special_requests'])) ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($res['status'] == 'pending' || $res['status'] == 'confirmed'): ?>
                                <?php if (!$isPast): ?>
                                    <form action="/reservations/cancel" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                            Cancel Reservation
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div style="height: 4rem;"></div>

<?php view('footer'); ?>
