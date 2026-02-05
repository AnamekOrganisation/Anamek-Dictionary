<?php
/**
 * Schema.org - Website
 * Defines the website structure and search functionality
 */
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "@id": "<?= BASE_URL ?>/#website",
  "url": "<?= BASE_URL ?>",
  "name": "Anamek - Dictionnaire Amazigh",
  "alternateName": "ⴰⵏⴰⵎⴽ - ⴰⵎⴰⵡⴰⵍ ⴰⵎⴰⵣⵉⵖ",
  "description": "Dictionnaire amazigh en ligne avec plus de 25,000 mots en tifinagh, latin et français",
  "inLanguage": ["zgh", "fr"],
  "publisher": {
    "@id": "<?= BASE_URL ?>/#organization"
  },
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "<?= BASE_URL ?>/search?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  },
  "about": {
    "@type": "Thing",
    "name": "Amazigh Language",
    "alternateName": ["Tamazight", "Berber", "ⵜⴰⵎⴰⵣⵉⵖⵜ"],
    "sameAs": [
      "https://www.wikidata.org/wiki/Q25448",
      "https://en.wikipedia.org/wiki/Berber_languages"
    ]
  }
}
</script>
