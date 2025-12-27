<?php view('admin/header', ['title' => 'Tour Routes Management']); ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Tour Routes</h2>
    <a href="/admin/routes/new" class="btn btn-primary">+ Create New Route</a>
</div>

<div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <?php if (empty($routes)): ?>
        <div style="text-align: center; padding: 3rem; color: #64748b;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🗺️</div>
            <h3>No routes created yet</h3>
            <p>Start by creating your first curated tour route.</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px;">Interest</th>
                    <th style="padding: 12px;">Difficulty</th>
                    <th style="padding: 12px;">Time</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $route): ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 12px; color: #64748b; font-size: 0.9rem;">#<?= $route['id'] ?></td>
                    <td style="padding: 12px; font-weight: 600;"><?= htmlspecialchars($route['name']) ?></td>
                    <td style="padding: 12px;">
                        <span style="font-size: 0.8rem; padding: 4px 10px; background: #eff6ff; color: #3b82f6; border-radius: 20px; font-weight: 500;">
                            <?= htmlspecialchars($route['interest_tag'] ?: 'General') ?>
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <span style="font-size: 0.8rem; padding: 4px 10px; border-radius: 20px; font-weight: 500; background: <?= $route['difficulty'] == 'easy' ? '#dcfce7' : ($route['difficulty'] == 'medium' ? '#fef9c3' : '#fee2e2') ?>; color: <?= $route['difficulty'] == 'easy' ? '#166534' : ($route['difficulty'] == 'medium' ? '#854d0e' : '#991b1b') ?>;">
                            <?= ucfirst($route['difficulty']) ?>
                        </span>
                    </td>
                    <td style="padding: 12px; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($route['estimated_time']) ?></td>
                    <td style="padding: 12px; text-align: right;">
                        <a href="/admin/routes/edit?id=<?= $route['id'] ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">Edit</a>
                        <form action="/admin/routes/delete" method="POST" style="display: inline;" onsubmit="return confirm('Search delete this route?')">
                            <input type="hidden" name="id" value="<?= $route['id'] ?>">
                            <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem; border-color: #ef4444; color: #ef4444;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php view('admin/footer'); ?>
