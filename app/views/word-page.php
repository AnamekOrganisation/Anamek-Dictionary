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
    <div class="main-grid-row triple-grid mt-4">
        <!-- Main Column: Word Entry -->
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
                    <?php if (!$isSingleView): ?>
                        <!-- Summary List Mode -->
                        <div class="search-results-list">
                            <h2 class="mb-4 h4 fw-bold"><?= count($variants) ?> <?= __('résultats trouvés') ?></h2>
                            <?php foreach ($variants as $v): ?>
                                <a href="<?= BASE_URL ?>/word/<?= urlencode($v['word_lat']) ?>-<?= $v['id'] ?>" class="text-decoration-none">
                                    <div class="word-summary-card mb-3 p-4 bg-white border rounded-4 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h3 class="tifinagh h3 mb-1 text-primary"><?= e($v['word_tfng']) ?></h3>
                                                <div class="latin text-muted h5 mb-0"><?= e($v['word_lat']) ?></div>
                                            </div>
                                            <div class="text-end">
                                                <div class="badge bg-light text-dark border mb-2"><?= __(e($v['part_of_speech'])) ?></div>
                                                <div class="translation text-dark fw-bold"><?= e($v['translation_fr']) ?></div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-primary small fw-bold">
                                            <?= __('Voir la fiche complète') ?> <i class="fas fa-arrow-right ms-1"></i>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Full Entry Mode -->
                        <?php foreach ($variants as $index => $v): ?>
                            <?php include ROOT_PATH . '/app/views/partials/word-entry.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
<script src="<?= BASE_URL ?>/public/js/modules/script-toggle-float.js?v=<?= time() ?>"></script>
</body>
</html>
