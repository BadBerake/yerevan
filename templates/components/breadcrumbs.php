<?php
/**
 * Component: Breadcrumbs
 * Generates SEO-friendly breadcrumbs based on the current URI.
 */

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$breadcrumbs = [['name' => 'Home', 'url' => '/']];

$currentUrl = '';
foreach ($parts as $part) {
    if (empty($part)) continue;
    $currentUrl .= '/' . $part;
    
    // Remove query string for labeling
    $labelPart = explode('?', $part)[0];
    
    // Customize labels if possible
    $name = ucfirst(str_replace(['-', '_'], ' ', $labelPart));
    
    // If it's a numeric ID, try to get the item name (optional but nice)
    if (is_numeric($labelPart) && isset($item)) {
        $name = htmlspecialchars(Lang::t($item['title_translations'], $item['title']));
    }
    
    $breadcrumbs[] = ['name' => $name, 'url' => $currentUrl];
}

if (count($breadcrumbs) <= 1) return;
?>

<nav aria-label="breadcrumb" style="margin-bottom: 2rem;">
    <ol style="display: flex; list-style: none; padding: 0; margin: 0; font-size: 0.9rem; flex-wrap: wrap; gap: 8px; color: #64748b;">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <li style="display: flex; align-items: center; gap: 8px;">
                <?php if ($index > 0): ?>
                    <span style="opacity: 0.5;">/</span>
                <?php endif; ?>
                
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <span style="color: var(--text-main); font-weight: 600;"><?= $crumb['name'] ?></span>
                <?php else: ?>
                    <a href="<?= $crumb['url'] ?>" style="color: inherit; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'"><?= $crumb['name'] ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>

    <!-- Breadcrumb List Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
        {
          "@type": "ListItem",
          "position": <?= $index + 1 ?>,
          "name": "<?= htmlspecialchars($crumb['name']) ?>",
          "item": "<?= 'https://' . $_SERVER['HTTP_HOST'] . $crumb['url'] ?>"
        }<?= ($index < count($breadcrumbs) - 1) ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    }
    </script>
</nav>
