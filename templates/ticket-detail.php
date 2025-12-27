<?php view('header', ['title' => 'Ticket #' . $ticket['id']]); ?>

<div class="container" style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h1><?= htmlspecialchars($ticket['subject']) ?></h1>
            <span style="color: #666;">Ticket #<?= $ticket['id'] ?> • <?= date('M j, Y', strtotime($ticket['created_at'])) ?></span>
        </div>
        <a href="/dashboard/support" class="btn btn-outline">Back to List</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem;">
        <?php foreach ($messages as $msg): 
            $isMe = $msg['sender_id'] == $user['id'];
        ?>
        <div style="display: flex; <?= $isMe ? 'justify-content: flex-end;' : 'justify-content: flex-start;' ?>">
            <div style="max-width: 70%; background: <?= $isMe ? 'var(--color-arm-blue)' : '#f1f1f1' ?>; color: <?= $isMe ? 'white' : '#333' ?>; padding: 1rem; border-radius: 15px; border-<?= $isMe ? 'bottom-right' : 'bottom-left' ?>-radius: 2px;">
                <div style="font-weight: 600; font-size: 0.8rem; margin-bottom: 5px; opacity: 0.8;">
                    <?= $isMe ? 'You' : 'Support' ?>
                </div>
                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                <div style="text-align: right; font-size: 0.7rem; margin-top: 5px; opacity: 0.7;">
                    <?= date('H:i', strtotime($msg['created_at'])) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($ticket['status'] !== 'closed'): ?>
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); position: sticky; bottom: 20px;">
        <form action="/dashboard/support/reply" method="POST">
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
            <div style="display: flex; gap: 10px;">
                <textarea name="message" rows="2" required placeholder="Type your reply..." style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                <button class="btn btn-primary" style="padding: 0 2rem;">Send</button>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 1rem; background: #eee; border-radius: 5px; color: #666;">
        This ticket is closed.
    </div>
    <?php endif; ?>
</div>

<?php view('footer'); ?>
