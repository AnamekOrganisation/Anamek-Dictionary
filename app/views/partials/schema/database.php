<?php
/**
 * Schema.org - Dataset
 * Defines Anamek as a comprehensive linguistic database
 */
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Dataset",
  "@id": "<?= BASE_URL ?>/#dataset",
  "name": "Anamek Amazigh Dictionary Database",
  "alternateName": "ⴰⵎⴰⵡⴰⵍ ⴰⵎⴰⵣⵉⵖ ⴰⵏⴰⵎⴽ",
  "description": "The world's most extensive cross-linguistic Amazigh dictionary database, providing scholarly definitions for 25,000+ entries in Tifinagh and Latin scripts with translations in French, supported by contextual usage examples and idiomatic proverbs",
  "url": "<?= BASE_URL ?>",
  "keywords": [
    "Amazigh",
    "Berber",
    "Tamazight",
    "Dictionary",
    "Language",
    "Tifinagh",
    "ⵜⴰⵎⴰⵣⵉⵖⵜ",
    "Linguistic Database",
    "North African Languages"
  ],
  "inLanguage": ["zgh", "fr"],
  "license": "https://creativecommons.org/licenses/by-sa/4.0/",
  "creator": {
    "@id": "<?= BASE_URL ?>/#organization"
  },
  "publisher": {
    "@id": "<?= BASE_URL ?>/#organization"
  },
  "datePublished": "2024-01-01",
  "dateModified": "<?= date('Y-m-d') ?>",
  "temporalCoverage": "2024/..",
  "spatialCoverage": {
    "@type": "Place",
    "name": "North Africa",
    "geo": {
      "@type": "GeoShape",
      "box": "37.5 -17.0 15.0 25.0"
    }
  },
  "distribution": [
    {
      "@type": "DataDownload",
      "encodingFormat": "application/json",
      "contentUrl": "<?= BASE_URL ?>/api/words"
    },
    {
      "@type": "DataDownload",
      "encodingFormat": "text/html",
      "contentUrl": "<?= BASE_URL ?>"
    }
  ],
  "variableMeasured": [
    "Word definitions",
    "Translations",
    "Phonetic transcriptions",
    "Usage examples",
    "Synonyms",
    "Antonyms",
    "Proverbs"
  ]
}
</script>
