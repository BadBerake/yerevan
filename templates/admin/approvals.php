<?php view('admin/header', ['title' => __('approvals')]); ?>

<div style="background: white; padding: 2rem; border-radius: 8px;">

<div style="background: white; padding: 2rem; border-radius: 8px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; background: #f8f9fa;">
                <th style="padding: 10px;">ID</th>
                <th style="padding: 10px;">Title</th>
                <th style="padding: 10px;">Category</th>
                <th style="padding: 10px;">User</th>
                <th style="padding: 10px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pending as $item): ?>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= $item['id'] ?></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($item['title']) ?></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= $item['category_id'] ?></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($item['username']) ?></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">
                    <form action="/admin/approve" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Approve</button>
                    </form>
                    <button class="btn btn-outline" style="padding: 5px 10px; font-size: 0.8rem; border-color: red; color: red;">Reject</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pending)): ?>
                <tr><td colspan="5" style="padding: 20px; text-align: center;">No pending approvals.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php view('admin/footer'); ?>
