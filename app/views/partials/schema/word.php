<?php
/**
 * Schema.org - Word (DefinedTerm)
 * Comprehensive word schema with relationships
 * 
 * Required variables:
 * - $word: array with word data
 * - $synonyms: array of synonym words (optional)
 * - $antonyms: array of antonym words (optional)
 * - $examples: array of examples (optional)
 */

if (!isset($word) || empty($word)) {
    return;
}

// Helper function to safely encode JSON strings
function jsonEncode($str) {
    return htmlspecialchars(json_encode($str, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
}
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "DefinedTerm",
  "@id": "<?= BASE_URL ?>/word/<?= urlencode($word['word_lat']) ?>-<?= $word['id'] ?>#term",
  "name": <?= jsonEncode($word['word_tfng']) ?>,
  "alternateName": <?= jsonEncode($word['word_lat']) ?>,
  "mainEntityOfPage": "<?= absolute_url($_SERVER['REQUEST_URI']) ?>",
  "identifier": "anamek-<?= $word['id'] ?>",
  "url": "<?= BASE_URL ?>/word/<?= urlencode($word['word_lat']) ?>-<?= $word['id'] ?>",
  "inLanguage": {
    "@type": "Language",
    "name": "Amazigh",
    "alternateName": "Tamazight",
    "identifier": "zgh"
  },
  "description": <?= jsonEncode($word['translation_fr'] ?? '') ?>,
  "disambiguatingDescription": "Terme linguistique Amazigh (Tamazight)",
  
  <?php if (!empty($word['part_of_speech'])): ?>
  "termCode": <?= jsonEncode($word['part_of_speech']) ?>,
  <?php endif; ?>
  
  "inDefinedTermSet": {
    "@type": "DefinedTermSet",
    "name": "Anamek Lexicographical Database",
    "@id": "<?= BASE_URL ?>/#dataset"
  },
  
  <?php if (!empty($variants) && count($variants) > 1): ?>
  "sameAs": [
    <?php 
    $sameAsLinks = [];
    foreach ($variants as $v) {
        if ($v['id'] != $word['id']) {
            $sameAsLinks[] = BASE_URL . '/word/' . urlencode($v['word_lat']) . '-' . $v['id'];
        }
    }
    echo '"' . implode('","', $sameAsLinks) . '"';
    ?>
  ],
  <?php endif; ?>

  <?php if (!empty($word['examples'])): ?>
  "citation": [
    <?php foreach ($word['examples'] as $index => $ex): ?>
    {
      "@type": "CreativeWork",
      "text": <?= jsonEncode($ex['example_tfng'] ?? '') ?>,
      "inLanguage": "zgh"
    }<?= $index < count($word['examples']) - 1 ? ',' : '' ?>
    <?php endforeach; ?>
  ],
  <?php endif; ?>

  "publisher": {
    "@id": "<?= BASE_URL ?>/#organization"
  }
}
</script>
