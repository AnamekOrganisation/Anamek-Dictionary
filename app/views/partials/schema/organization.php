<?php
/**
 * Schema.org - Organization
 * Defines Anamek as an authoritative educational organization
 */
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": ["Organization", "EducationalOrganization"],
  "@id": "<?= BASE_URL ?>/#organization",
  "name": "Anamek",
  "alternateName": ["ⴰⵏⴰⵎⴽ", "Dictionnaire Anamek"],
  "url": "<?= BASE_URL ?>",
  "logo": {
    "@type": "ImageObject",
    "url": "<?= BASE_URL ?>/public/img/logo_site.png",
    "width": 512,
    "height": 512
  },
  "description": "L'autorité numérique de référence pour la langue Amazighe. Premier dictionnaire global avec plus de 25 000 entrées.",
  "foundingDate": "2024",
  "knowsAbout": ["Linguistic", "Amazigh Culture", "Tamazight Language"],
  "knowsLanguage": ["zgh", "fr", "en"],
  "sameAs": [
    <?php 
    global $pdo;
    $stmt = $pdo->query("SELECT url FROM social_links WHERE status = 1");
    $links = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $links[] = "https://www.wikidata.org/wiki/Q12345678"; // Keep Wikidata ID
    echo '"' . implode('", "', array_unique($links)) . '"';
    ?>
  ]
}
</script>
