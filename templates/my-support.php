<?php view('header', ['title' => 'My Support Tickets']); ?>

<div class="container" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Support Tickets</h1>
        <div>
            <a href="/dashboard" class="btn btn-outline" style="margin-right: 10px;">Back</a>
            <a href="/dashboard/support/new" class="btn btn-primary">New Ticket</a>
        </div>
    </div>

    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Subject</th>
                    <th style="padding: 1rem;">Last Updated</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;">#<?= $t['id'] ?></td>
                    <td style="padding: 1rem; font-weight: 500;"><?= htmlspecialchars($t['subject']) ?></td>
                    <td style="padding: 1rem;"><?= date('M j, Y H:i', strtotime($t['updated_at'])) ?></td>
                    <td style="padding: 1rem;">
                        <?php 
                        $statusColor = match($t['status']) {
                            'open' => '#28a745',
                            'answered' => '#007bff',
                            'closed' => '#6c757d',
                            default => '#ccc'
                        };
                        ?>
                        <span style="padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; background: <?= $statusColor ?>20; color: <?= $statusColor ?>;">
                            <?= htmlspecialchars(ucfirst($t['status'])) ?>
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        <a href="/dashboard/support/view?id=<?= $t['id'] ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 0.8rem;">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="5" style="padding: 2rem; text-align: center; color: #666;">
                        No tickets found. Need help? Create a new ticket.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php view('footer'); ?>
