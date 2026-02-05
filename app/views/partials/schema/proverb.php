<?php
/**
 * Schema.org - Proverb (Quotation)
 * Schema for Amazigh proverbs
 * 
 * Required variables:
 * - $proverb: array with proverb data
 */

if (!isset($proverb) || empty($proverb)) {
    return;
}

function jsonEncode($str) {
    return htmlspecialchars(json_encode($str, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
}
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Quotation",
  "@id": "<?= BASE_URL ?>/proverb/<?= $proverb['id'] ?>#quotation",
  "text": <?= jsonEncode($proverb['proverb_tfng']) ?>,
  "inLanguage": "zgh",
  "url": "<?= BASE_URL ?>/proverb/<?= $proverb['id'] ?>",
  
  <?php if (!empty($proverb['proverb_lat'])): ?>
  "alternateName": <?= jsonEncode($proverb['proverb_lat']) ?>,
  <?php endif; ?>
  
  <?php if (!empty($proverb['translation_fr'])): ?>
  "translation": {
    "@type": "CreativeWork",
    "text": <?= jsonEncode($proverb['translation_fr']) ?>,
    "inLanguage": "fr"
  },
  <?php endif; ?>
  
  <?php if (!empty($proverb['explanation'])): ?>
  "about": {
    "@type": "Thing",
    "description": <?= jsonEncode($proverb['explanation']) ?>
  },
  <?php endif; ?>
  
  "inDefinedTermSet": {
    "@id": "<?= BASE_URL ?>/#dataset"
  },
  
  "publisher": {
    "@id": "<?= BASE_URL ?>/#organization"
  }
}
</script>
