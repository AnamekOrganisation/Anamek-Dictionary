<?php
/**
 * Partial: word-entry.php
 * Unified Cultural Variant Design
 * Implements the new "App-like" design while maintaining desktop compatibility.
 */

// Determine context for mobile layout elements
$isFirst = !isset($index) || $index === 0;
$isLast = !isset($index) || !isset($variants) || $index === count($variants) - 1;

if (!function_exists('getWordLink')) {
    function getWordLink($slug) {
        if (empty($slug) || $slug === '—') return '#';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        return $baseUrl . '/word/' . urlencode($slug);
    }
}
?>

<!-- Main Entry Content -->
<div class="cultural-entry-container mb-4">
    <!-- Hero Card -->
    <div class="cultural-hero-card">
        <div class="cultural-hero-header">
            <h1 class="cultural-hero-title tifinagh"><?= e($v['word_tfng']) ?></h1>
            <button class="cultural-hero-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
            </button>
        </div>
        <p class="cultural-hero-latin"><?= e($v['word_lat']) ?></p>
        <?php if (!empty($v['part_of_speech']) || !empty($v['gender'])): ?>
        <div class="cultural-hero-badges">
            <?php if (!empty($v['part_of_speech'])): ?>
            <span class="cultural-hero-badge"><?= __(e($v['part_of_speech'])) ?></span>
            <?php endif; ?>
            <?php if (!empty($v['gender'])): ?>
            <span class="cultural-hero-badge"><?= __(e($v['gender'])) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Definitions Section -->
    <?php if (!empty($v['definition_tfng']) || !empty($v['translation_fr'])): ?>
    <section class="cultural-section">
        <div class="cultural-section-header">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor" style="color: var(--cultural-primary);"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/></svg>
            <h2 class="cultural-section-title"><?= __('Définitions') ?></h2>
        </div>
        
        <?php if (!empty($v['definition_tfng'])): ?>
        <div>
            <p class="cultural-definition-label"><?= __('Définition Académique') ?></p>
            <p class="cultural-definition-tfng tifinagh"><?= e($v['definition_tfng']) ?></p>
            <?php if (!empty($v['definition_lat'])): ?>
            <p class="cultural-definition-latin">"<?= e($v['definition_lat']) ?>"</p>
            <?php endif; ?>
        </div>
        <?php elseif (!empty($v['translation_fr'])): ?>
        <p class="cultural-definition-tfng"><?= e($v['translation_fr']) ?></p>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- Examples Section -->
    <?php if (!empty($v['examples']) || !empty($v['example_tfng'])): ?>
    <section class="cultural-example-section">
        <div class="cultural-example-header">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor" style="color: #10b981;"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            <h2 class="cultural-section-title"><?= __('Exemples') ?></h2>
        </div>
        
        <?php if (!empty($v['example_tfng'])): ?>
        <div class="cultural-example-card">
            <p class="cultural-example-tfng tifinagh"><?= e($v['example_tfng']) ?></p>
            <?php if (!empty($v['example_fr'])): ?>
            <p class="cultural-example-fr"><?= e($v['example_fr']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($v['examples'])): ?>
            <?php foreach ($v['examples'] as $ex): ?>
            <div class="cultural-example-card">
                <p class="cultural-example-tfng tifinagh"><?= e($ex['example_tfng'] ?? $ex['example_lat']) ?></p>
                <?php if (!empty($ex['example_fr'])): ?>
                <p class="cultural-example-fr"><?= e($ex['example_fr']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- Morphology Section -->
    <?php 
    $hasMorph = !empty($v['root_tfng']) || !empty($v['plural_tfng']) || !empty($v['type_tfng']) || !empty($v['feminine_tfng']);
    if ($hasMorph): 
    ?>
    <section class="cultural-section">
        <div class="cultural-section-header">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor" style="color: #f43f5e;"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 13.5h8v8H3z"/></svg>
            <h2 class="cultural-section-title"><?= __('Morphologie') ?></h2>
        </div>
        <div class="cultural-morph-grid">
            <?php if (!empty($v['root_tfng'])): ?>
            <div class="cultural-morph-pill">
                <span class="cultural-morph-label"><?= __('Racine') ?></span>
                <a href="<?= getWordLink($v['root_lat'] ?? '') ?>" class="cultural-morph-value tifinagh" style="text-decoration: none;">
                    <?= e($v['root_tfng']) ?>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($v['plural_tfng'])): ?>
            <div class="cultural-morph-pill">
                <span class="cultural-morph-label"><?= __('Pluriel') ?></span>
                <a href="<?= getWordLink($v['plural_lat'] ?? '') ?>" class="cultural-morph-value tifinagh" style="text-decoration: none;">
                    <?= e($v['plural_tfng']) ?>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($v['feminine_tfng'])): ?>
            <div class="cultural-morph-pill">
                <span class="cultural-morph-label"><?= __('Féminin') ?></span>
                <a href="<?= getWordLink($v['feminine_lat'] ?? '') ?>" class="cultural-morph-value tifinagh" style="text-decoration: none;">
                    <?= e($v['feminine_tfng']) ?>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($v['type_tfng'])): ?>
            <div class="cultural-morph-pill">
                <span class="cultural-morph-label"><?= __('Type') ?></span>
                <span class="cultural-morph-value tifinagh"><?= e($v['type_tfng']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Semantic Relations Section -->
    <?php if (!empty($v['synonyms']) || !empty($v['antonyms'])): ?>
    <section class="cultural-example-section">
        <div class="cultural-example-header">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor" style="color: #3b82f6;"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
            <h2 class="cultural-section-title"><?= __('Relations sémantiques') ?></h2>
        </div>
        
        <div class="cultural-section">
            <?php if (!empty($v['synonyms'])): ?>
            <div style="margin-bottom: 1rem;">
                <span class="cultural-rel-label"><?= __('Synonymes') ?></span>
                <div class="cultural-rel-tags">
                    <?php foreach ($v['synonyms'] as $syn): ?>
                    <a href="<?= getWordLink($syn['synonym_lat'] ?? '') ?>" class="cultural-rel-tag">
                        <span style="color: var(--cultural-slate-400); font-size: 0.75rem;">=</span>
                        <span class="tifinagh"><?= e($syn['synonym_tfng'] ?? $syn['synonym_lat']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($v['antonyms'])): ?>
            <div>
                <span class="cultural-rel-label"><?= __('Antonymes') ?></span>
                <div class="cultural-rel-tags">
                    <?php foreach ($v['antonyms'] as $ant): ?>
                    <a href="<?= getWordLink($ant['antonym_lat'] ?? '') ?>" class="cultural-rel-tag antonym">
                        <span style="font-size: 0.75rem;">≠</span>
                        <span class="tifinagh"><?= e($ant['antonym_tfng'] ?? $ant['antonym_lat']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="cultural-actions">
        <button class="cultural-action-btn" onclick="copyWord('<?= htmlspecialchars(addslashes($v['word_tfng'] ?? ''), ENT_QUOTES) ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 0 24 24" width="18" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
            <?= __('Copier') ?>
        </button>
        <button class="cultural-action-btn" onclick="shareWord('<?= htmlspecialchars(addslashes($v['word_tfng'] ?? ''), ENT_QUOTES) ?>', '<?= getWordLink($v['slug'] ?? '') ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 0 24 24" width="18" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.66 0 1.61 1.31 2.91 2.92 2.91 1.61 0 2.92-1.3 2.92-2.91A2.92 2.92 0 0 0 18 16.08z"/></svg>
            <?= __('Partager') ?>
        </button>
        <a href="<?= BASE_URL ?>/contribute/word?edit_id=<?= $v['id'] ?>" class="cultural-action-btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        </a>
    </div>
</div>

<!-- Mobile Layout Elements (Nav) - Only once at the end -->
<?php if ($isLast): ?>
<div class="d-md-none">
    <nav class="cultural-bottom-nav">
        <div class="cultural-bottom-nav-items">
            <a href="<?= BASE_URL ?>/" class="cultural-nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                <span><?= __('Accueil') ?></span>
            </a>
            <a href="<?= BASE_URL ?>/word/search" class="cultural-nav-item active">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <span><?= __('Dictionnaire') ?></span>
            </a>
            <a href="<?= BASE_URL ?>/quizzes" class="cultural-nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12zM10 9h8v2h-8zm0 3h4v2h-4zm0-6h8v2h-8z"/></svg>
                <span><?= __('Quiz') ?></span>
            </a>
            <a href="<?= BASE_URL ?>/user/dashboard" class="cultural-nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3 3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                <span><?= __('Profil') ?></span>
            </a>
        </div>
    </nav>
    <div style="height: 70px;"></div>

    <!-- Script Toggle Button -->
    <button class="cultural-script-toggle">
        <span class="tifinagh">ⴰ</span>
        <span>Latin</span>
    </button>
</div>
<?php endif; ?>
