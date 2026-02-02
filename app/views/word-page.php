<!-- Header -->
<?php include_once __DIR__ . '/partials/header.php'; ?>

<?php if (!empty($word)): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "DefinedTerm",
  "name": "<?= e($word['word_tfng']) ?>",
  "identifier": "<?= e($word['word_lat']) ?>",
  "description": "<?= e($word['translation_fr']) ?>",
  "inDefinedTermSet": {
    "@type": "DefinedTermSet",
    "name": "Dictionnaire Anamek",
    "url": "<?= BASE_URL ?>"
  }
}
</script>
<?php endif; ?>

<!-- Main Content -->
<main class="main" id="main">
    <!-- Search Header Section -->
    <!-- Search Header Section Removed as per request (redundant with header) -->

    <div class="main-grid-row triple-grid mt-4">
        <!-- Main Column: Word Entry (Takes 2/3 space if we use triple-grid or custom grid) -->
        <div class="main-column-entry">
            <div class="definition-section">
                <?php if (empty($variants)): ?>
                    <div class="not-found-container">
                        <div class="not-found-icon">🔍</div>
                        <h2 class="not-found-title"><?= __('Word not found') ?></h2>
                        <p class="not-found-text"><?= __('Désolé, nous n\'avons trouvé aucun résultat pour votre recherche.') ?></p>
                        <a href="<?= BASE_URL ?>" class="view-btn"><?= __('Retour à l\'accueil') ?></a>
                    </div>
                <?php else: ?>
                    <?php if (count($variants) > 1): ?>
                    <div class="sense-selector mb-4">
                        <h3 class="sense-selector-title"><?= __('Plusieurs sens trouvés :') ?></h3>
                        <div class="sense-grid">
                            <?php foreach ($variants as $idx => $v): ?>
                            <a href="#entry-<?= $v['id'] ?>" class="sense-item <?= $v['id'] == ($word['id'] ?? 0) ? 'current-sense' : '' ?>">
                                <span class="sense-num"><?= $idx + 1 ?></span>
                                <span class="sense-summary"><?= htmlspecialchars($v['translation_fr']) ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php foreach ($variants as $index => $v): ?>
                        <?php include ROOT_PATH . '/app/views/partials/word-entry.php'; ?>
                    <?php endforeach; ?>


                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="sidebar-column">
             <!-- Dictionary Identification Card -->
             <?php include_once __DIR__ . '/partials/word-details-section.php'; ?>

             <!-- Word of the Day Section -->
             <div class="mt-4">
                <?php include_once __DIR__ . '/partials/word-of-the-day-section.php'; ?>
             </div>

             <!-- Ad Section (Top) -->
             <div class="mt-4">
                <?php $slot = $adSlotHome; include __DIR__ . '/partials/ads-section.php'; ?>
             </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include_once __DIR__ . '/partials/footer.php'; ?>

<!-- Scripts -->
<script src="<?= BASE_URL ?>/public/js/home.js?v=<?= time() ?>"></script>
</body>
</html>
