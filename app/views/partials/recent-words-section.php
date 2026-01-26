<?php
/**
 * View Partial: Recent Words
 * Displays the most recently added words to the dictionary.
 */
?>
<div class="section recent-words-section">

        <div class="sous-titre-trend"><i class="fas fa-plus-circle text-primary me-2"></i><span><?= __('new words') ?></span></div>

    <div class="recent-words-list">
        <?php if (!empty($recentWords)): ?>
            <ul class="list-unstyled mb-0">
                <?php foreach ($recentWords as $word): ?>
                    <li class="mb-3 border-bottom-light last-no-border">
                        <a href="<?= BASE_URL ?>/word/<?= urlencode($word['word_lat']) ?>-<?= $word['id'] ?>" class="text-decoration-none">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="word-display fs-5 fw-600 color-navy" data-tfng="<?= htmlspecialchars($word['word_tfng']) ?>" data-lat="<?= htmlspecialchars($word['word_lat']) ?>">
                                    <?= htmlspecialchars($word['word_lat']) ?>
                                </span>
                                <span class="badge bg-light text-muted fw-normal"><?= date('d/m', strtotime($word['created_at'])) ?></span>
                            </div>
                            <!-- <div class="text-muted small mt-1"><?php //htmlspecialchars($word['translation_fr']) ?></div> -->
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted text-center py-4"><?= __('Aucun mot récent.') ?></p>
        <?php endif; ?>
    </div>
</div>

<style>
.border-bottom-light { border-bottom: 1px solid #f1f5f9; }
.last-no-border:last-child { border-bottom: none; }
.color-navy { color: #181d4b; }
.fw-600 { font-weight: 600; }
</style>
