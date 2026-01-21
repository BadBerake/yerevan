<?php ob_start(); ?>

<div class="card">
    <div class="card-header">
        <h3>Top Pages</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table style="margin: 0;">
            <thead>
                <tr>
                    <th>Page URL</th>
                    <th class="text-right">Views</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($topPages as $page): ?>
                <tr>
                    <td style="font-family: monospace; color: var(--primary);">
                        <?= htmlspecialchars($page['page_url']) ?>
                    </td>
                    <td class="text-right" style="font-weight: 700;">
                        <?= number_format($page['views']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
include __DIR__ . '/layout.php'; 
?>
