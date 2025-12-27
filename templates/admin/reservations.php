<?php view('admin/header', ['title' => 'Reservations Management']); ?>

<div class="header-section" style="margin-bottom: 2rem;">
    <div>
        <h2>Reservations Management</h2>
        <p style="color: var(--text-muted);">Manage all customer reservations</p>
    </div>
    
    <div style="display: flex; gap: 10px;">
        <a href="/admin/reservations" class="btn <?= $filter == 'all' ? 'btn-primary' : 'btn-outline' ?>">All</a>
        <a href="/admin/reservations?status=pending" class="btn <?= $filter == 'pending' ? 'btn-primary' : 'btn-outline' ?>">Pending</a>
        <a href="/admin/reservations?status=confirmed" class="btn <?= $filter == 'confirmed' ? 'btn-primary' : 'btn-outline' ?>">Confirmed</a>
        <a href="/admin/reservations?status=completed" class="btn <?= $filter == 'completed' ? 'btn-outline' : 'btn-outline' ?>">Completed</a>
    </div>
</div>

<div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden;">
    <?php if (empty($reservations)): ?>
        <div style="padding: 4rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
            <p>No reservations found</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Place</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Customer</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Date & Time</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Guests</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Contact</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Status</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reservations as $res): ?>
                    <?php
                    $statusColors = [
                        'pending' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                        'confirmed' => ['bg' => '#dcfce7', 'text' => '#166534'],
                        'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                        'completed' => ['bg' => '#e0e7ff', 'text' => '#3730a3']
                    ];
                    $color = $statusColors[$res['status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px;">
                            <a href="/place/<?= $res['item_id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                <?= htmlspecialchars($res['item_title']) ?>
                            </a>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 500;"><?= htmlspecialchars($res['name']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">@<?= htmlspecialchars($res['username']) ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <div><?= date('M d, Y', strtotime($res['reservation_date'])) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);"><?= date('g:i A', strtotime($res['reservation_time'])) ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <?= $res['guests'] ?> <?= $res['guests'] == 1 ? 'person' : 'people' ?>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-size: 0.85rem;"><?= htmlspecialchars($res['email']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($res['phone']) ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <span style="display: inline-block; background: <?= $color['bg'] ?>; color: <?= $color['text'] ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; text-transform: capitalize;">
                                <?= $res['status'] ?>
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <?php if ($res['status'] == 'pending'): ?>
                                    <form action="/admin/reservations/update-status" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;" title="Confirm">
                                            ✓
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($res['status'] == 'confirmed'): ?>
                                    <form action="/admin/reservations/update-status" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" style="background: #6366f1; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;" title="Mark Completed">
                                            ✓✓
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($res['status'] != 'cancelled' && $res['status'] != 'completed'): ?>
                                    <form action="/admin/reservations/update-status" method="POST" style="display: inline;" onsubmit="return confirm('Cancel this reservation?');">
                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;" title="Cancel">
                                            ✕
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php if (!empty($res['special_requests'])): ?>
                        <tr style="background: #f8fafc;">
                            <td colspan="7" style="padding: 10px 15px; font-size: 0.85rem; color: var(--text-muted);">
                                <strong>Special Requests:</strong> <?= nl2br(htmlspecialchars($res['special_requests'])) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php view('admin/footer'); ?>
