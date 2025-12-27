<?php view('admin/header', ['title' => 'Reviews Management']); ?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h2>Reviews Management</h2>
    <p style="color: var(--text-muted);">Manage user reviews for all places</p>
</div>

<div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc;">
            <tr>
                <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Place</th>
                <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">User</th>
                <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Rating</th>
                <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Comment</th>
                <th style="padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Date</th>
                <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #e2e8f0;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        No reviews yet
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($reviews as $review): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px;">
                            <a href="/place/<?= $review['item_id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                <?= htmlspecialchars($review['item_title']) ?>
                            </a>
                        </td>
                        <td style="padding: 15px;">
                            <?= htmlspecialchars($review['username']) ?>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 2px;">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span style="color: <?= $i <= $review['rating'] ? '#fbbf24' : '#e5e7eb' ?>;">★</span>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td style="padding: 15px; max-width: 300px;">
                            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($review['comment']) ?>
                            </div>
                        </td>
                        <td style="padding: 15px; color: var(--text-muted); font-size: 0.9rem;">
                            <?= date('M d, Y', strtotime($review['created_at'])) ?>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <form action="/admin/reviews/delete" method="POST" style="display: inline;" onsubmit="return confirm('Delete this review?');">
                                <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php view('admin/footer'); ?>
