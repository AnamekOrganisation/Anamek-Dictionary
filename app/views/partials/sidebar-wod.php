<?php
/**
 * Partial: sidebar-wod.php
 * Compact version of Word of the Day for sidebars.
 */
if (!isset($wordOfTheDay)) return;
?>
<div class="sidebar-wod-card border rounded-4 overflow-hidden shadow-sm bg-white">
    <div class="px-4 py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <h3 class="h6 mb-0 fw-bold text-uppercase letter-spacing-1 text-muted">
            <i class="fas fa-star text-warning me-2"></i><?= __('Mot du jour') ?>
        </h3>
        <a href="<?= BASE_URL ?>/word/<?= urlencode($wordOfTheDay['word_lat']) ?>-<?= $wordOfTheDay['id'] ?>" class="text-primary small">
            <i class="fas fa-external-link-alt"></i>
        </a>
    </div>
    <div class="p-4">
        <div class="tifinagh h4 mb-1 text-dark"><?= e($wordOfTheDay['word_tfng']) ?></div>
        <div class="latin text-muted small italic mb-3"><?= e($wordOfTheDay['word_lat']) ?></div>
        <p class="mb-0 text-dark small leading-relaxed">
            <?= e(mb_strimwidth(strip_tags($wordOfTheDay['translation_fr']), 0, 80, "...")) ?>
        </p>
    </div>
</div>
