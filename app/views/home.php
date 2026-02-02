<!-- Header -->
<?php include_once __DIR__ . '/partials/header.php'; ?>

<?php if (isset($preLoadedWord)): ?>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "DefinedTerm",
        "name": "<?= htmlspecialchars($preLoadedWord['word_lat'] . ' / ' . $preLoadedWord['word_tfng']) ?>",
        "inDefinedTermSet": "https://amawal.net",
        "termCode": "<?= htmlspecialchars($preLoadedWord['word_lat']) ?>",
        "description": "<?= htmlspecialchars($preLoadedWord['definition_fr'] ?? $preLoadedWord['translation_fr']) ?>",
        "url": "<?= BASE_URL ?>/word/<?= urlencode($preLoadedWord['word_lat']) ?>-<?= $preLoadedWord['id'] ?>"
    }
</script>
<?php endif; ?>

<!-- Main Content -->
<main class="main-content">
    <!-- Old search removed - now in header's hero-search section -->

    <!-- Top Section: Featured Items -->
    <div class="main-grid-row triple-grid mt-4">
        <!-- Word of the Day Section -->
        <?php include_once __DIR__ . '/partials/word-of-the-day-section.php'; ?>
        
        <!-- Dictionary Identification Card -->
        <?php include_once __DIR__ . '/partials/word-details-section.php'; ?>

        <!-- Ad Section (Top) -->
        <?php $slot = $adSlotHome; include __DIR__ . '/partials/ads-section.php'; ?>
    </div>

    <!-- Middle Section: Analytics & Engagement -->
    <div class="main-grid-row triple-grid mt-4">
        <!-- Trending Words Section (Last Research) -->
        <?php include_once __DIR__ . '/partials/trending-section.php'; ?>

        <!-- Latest Added Words -->
        <?php include_once __DIR__ . '/partials/recent-words-section.php'; ?>

        <!-- Quiz Teaser -->
        <?php include_once __DIR__ . '/partials/quiz-teaser-section.php'; ?>
    </div>

    <!-- Bottom Section: Proverbs & Final Ad -->
    <div class="main-grid-row mt-4">
        <!-- Proverb Section -->
        <?php include_once __DIR__ . '/partials/proverb-of-the-day-section.php'; ?>
    </div>

    <div class="main-grid-row mt-4">
        <!-- Ad Section (Bottom) -->
        <?php $slot = $adSlotHome; include __DIR__ . '/partials/ads-section.php'; ?>
    </div>
</main>




<!-- Footer -->
<?php include_once __DIR__ . '/partials/footer.php'; ?>


<!-- Home Page Logic -->
<script src="<?= BASE_URL ?>/public/js/home.js?v=<?= time() ?>"></script>
<?php if (isset($preLoadedWord) && empty($isHomepage)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        selectWord(<?= json_encode($preLoadedWord['word_lat'] ?? '') ?>);
    });
</script>
<?php endif; ?>