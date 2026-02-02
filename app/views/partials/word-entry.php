<?php
/**
 * Partial: word-entry.php
 * Renders a single word entry using the ELITE PREMIUM design.
 */

$isActive = isset($word) && $v['id'] == ($word['id'] ?? 0);
$variantCount = count($variants ?? []);

// Helper to generate a link for a word slug
if (!function_exists('getWordLink')) {
    function getWordLink($slug) {
        if (empty($slug) || $slug === '—') return '#';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        return $baseUrl . '/word/' . urlencode($slug);
    }
}
?>

<article id="entry-<?= $v['id'] ?>" class="word-entry elite-entry <?= $isActive ? 'active-entry' : '' ?>">
    
    <!-- Word Hero Header -->
    <header class="elite-hero">
        <span class="tifinagh word-title"><?= e($v['word_tfng']) ?></span>
        <span class="latin phonetic"><?= e($v['word_lat']) ?></span>
        
        <?php if (!empty($v['part_of_speech'])): ?>
        <div class="category-pill shadow-sm">
            <?= __(e($v['part_of_speech'])) ?>
        </div>
        <?php endif; ?>
    </header>

    <!-- Quick Info Grid -->
    <div class="elite-info-grid">
        <div class="info-card">
            <i class="fas fa-tags"></i>
            <div>
                <span class="label"><?= __('Catégorie') ?></span>
                <span class="value"><?= !empty($v['part_of_speech']) ? __(e($v['part_of_speech'])) : '—' ?></span>
            </div>
        </div>
        <div class="info-card">
            <i class="fas fa-venus-mars"></i>
            <div>
                <span class="label"><?= __('Genre') ?></span>
                <span class="value"><?= !empty($v['gender']) ? __(e($v['gender'])) : '—' ?></span>
            </div>
        </div>
        <div class="info-card">
            <i class="fas fa-map-marker-alt"></i>
            <div>
                <span class="label"><?= __('Dialecte') ?></span>
                <span class="value"><?= !empty($v['dialect']) ? e($v['dialect']) : 'Général' ?></span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="elite-content">
        
        <!-- Definitions Section -->
        <section class="elite-section">
            <h2><i class="fas fa-book-open text-primary"></i> <?= __('Définitions') ?></h2>
            
            <?php if (!empty($v['translation_fr'])): ?>
            <div class="definition-box primary">
                <span class="type-label"><?= __('Traduction') ?></span>
                <div class="text fw-bold definition-text"><?= e($v['translation_fr']) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($v['definition_tfng'])): ?>
            <div class="definition-box">
                <span class="type-label"><?= __('Définition académique') ?></span>
                <div class="text"><?= e($v['definition_tfng']) ?></div>
                <?php if(!empty($v['definition_lat'])): ?>
                    <p class="text-muted small mt-2 mb-0 italic">"<?= e($v['definition_lat']) ?>"</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Examples Section -->
        <?php if (!empty($v['examples']) || !empty($v['example_tfng'])): ?>
        <section class="elite-section">
            <h2><i class="fas fa-pen-nib text-success"></i> <?= __('Exemples') ?></h2>
            
            <?php if (!empty($v['example_tfng'])): ?>
            <div class="elite-example shadow-sm">
                <div class="berber fw-bold"><?= e($v['example_tfng']) ?></div>
                <?php if (!empty($v['example_fr'])): ?>
                <div class="translation"><?= e($v['example_fr']) ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($v['examples'])): ?>
                <?php foreach ($v['examples'] as $ex): ?>
                <div class="elite-example shadow-sm">
                    <div class="berber fw-bold"><?= e($ex['example_tfng'] ?? $ex['example_lat']) ?></div>
                    <?php if (!empty($ex['example_fr'])): ?>
                    <div class="translation"><?= e($ex['example_fr']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <!-- Morphology Section -->
        <?php 
        $hasMorph = !empty($v['root_tfng']) || !empty($v['plural_tfng']) || !empty($v['type_tfng']) || !empty($v['formation_tfng']) || !empty($v['feminine_tfng']);
        if ($hasMorph): 
        ?>
        <section class="elite-section">
            <h2><i class="fas fa-dna text-danger"></i> <?= __('Morphologie') ?></h2>
            <div class="morph-grid">
                <?php if (!empty($v['root_tfng'])): ?>
                <div class="morph-pill">
                    <span class="title"><?= __('Racine') ?></span>
                    <a href="<?= getWordLink($v['root_lat'] ?? '') ?>" class="data text-decoration-none text-primary"><?= e($v['root_tfng']) ?></a>
                </div>
                <?php endif; ?>

                <?php if (!empty($v['plural_tfng'])): ?>
                <div class="morph-pill">
                    <span class="title"><?= __('Pluriel') ?></span>
                    <a href="<?= getWordLink($v['plural_lat'] ?? '') ?>" class="data text-decoration-none text-primary"><?= e($v['plural_tfng']) ?></a>
                </div>
                <?php endif; ?>

                <?php if (!empty($v['feminine_tfng'])): ?>
                <div class="morph-pill">
                    <span class="title"><?= __('Féminin') ?></span>
                    <a href="<?= getWordLink($v['feminine_lat'] ?? '') ?>" class="data text-decoration-none text-primary"><?= e($v['feminine_tfng']) ?></a>
                </div>
                <?php endif; ?>

                <?php if (!empty($v['type_tfng'])): ?>
                <div class="morph-pill">
                    <span class="title"><?= __('Type') ?></span>
                    <span class="data"><?= e($v['type_tfng']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Semantic Relations Section -->
        <?php if (!empty($v['synonyms']) || !empty($v['antonyms'])): ?>
        <section class="elite-section">
            <h2><i class="fas fa-link text-info"></i> <?= __('Relations sémantiques') ?></h2>
            
            <?php if (!empty($v['synonyms'])): ?>
            <div class="mb-4">
                <span class="text-muted small fw-bold text-uppercase d-block mb-3"><?= __('Synonymes') ?></span>
                <div class="relations-group">
                    <?php foreach ($v['synonyms'] as $syn): ?>
                        <a href="<?= getWordLink($syn['synonym_lat'] ?? '') ?>" class="rel-tag">
                            <i class="fas fa-equals text-success small"></i>
                            <?= e($syn['synonym_tfng'] ?? $syn['synonym_lat']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($v['antonyms'])): ?>
            <div>
                <span class="text-muted small fw-bold text-uppercase d-block mb-3"><?= __('Antonymes') ?></span>
                <div class="relations-group">
                    <?php foreach ($v['antonyms'] as $ant): ?>
                        <a href="<?= getWordLink($ant['antonym_lat'] ?? '') ?>" class="rel-tag antonym">
                            <i class="fas fa-not-equal text-danger small"></i>
                            <?= e($ant['antonym_tfng'] ?? $ant['antonym_lat']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

    </div>

    <!-- Actions & Toolbar -->
    <footer class="elite-actions">
        <div class="d-flex gap-3">
            <button class="action-btn" onclick="copyWord(<?= (int)$v['id'] ?>)">
                <i class="far fa-copy text-primary"></i> <?= __('Copier') ?>
            </button>
            <button class="action-btn" onclick="shareWord(<?= (int)$v['id'] ?>, <?= htmlspecialchars(json_encode($v['word_tfng'])) ?>)">
                <i class="fas fa-share-alt text-success"></i> <?= __('Partager') ?>
            </button>
        </div>
        
        <div class="d-flex gap-3 mt-md-0 mt-3">
            <?php 
                $slug = urlencode($v['word_lat']); 
                $permalink = BASE_URL . "/word/{$slug}-{$v['id']}"; 
            ?>
            <a href="<?= $permalink ?>" class="action-btn permalink-badge">
                <i class="fas fa-link text-muted"></i> <?= __('Lien') ?>
            </a>
            <a href="<?= BASE_URL ?>/contribute/word?edit_id=<?= $v['id'] ?>" class="action-btn primary shadow-sm">
                <i class="fas fa-magic"></i> <?= __('Modifier') ?>
            </a>
        </div>
    </footer>

</article>
