<?php
/**
 * Schema.org - Breadcrumb
 * Dynamic breadcrumb navigation for SEO
 * 
 * Usage: include this file and pass $breadcrumbs array
 * Example: $breadcrumbs = [
 *   ['name' => 'Home', 'url' => BASE_URL],
 *   ['name' => 'Word', 'url' => BASE_URL . '/word/example']
 * ];
 */

if (!isset($breadcrumbs) || empty($breadcrumbs)) {
    return;
}
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    <?php foreach ($breadcrumbs as $index => $crumb): ?>
    {
      "@type": "ListItem",
      "position": <?= $index + 1 ?>,
      "name": "<?= htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8') ?>",
      "item": "<?= htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8') ?>"
    }<?= $index < count($breadcrumbs) - 1 ? ',' : '' ?>
    
    <?php endforeach; ?>
  ]
}
</script>
