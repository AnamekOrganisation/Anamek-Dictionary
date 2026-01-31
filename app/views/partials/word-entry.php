<?php
/**
 * Partial: word-entry.php
 * Renders a single academic word entry.
 * Expects:
 * - $v: The word variant data array
 * - $index: The index of the variant (0-based)
 * - $variants: The full array of variants (for count check)
 * - $word: The main word being searched (for active state check)
 */

$isActive = isset($word) && $v['id'] == ($word['id'] ?? 0);
$variantCount = count($variants ?? []);

// Helper to generate a link for a word slug
if (!function_exists('getWordLink')) {
    function getWordLink($slug) {
        if (empty($slug) || $slug === '—') return '#';
        return BASE_URL . '/word/' . urlencode($slug);
    }
}
?>

<article id="entry-<?= $v['id'] ?>" class="word-entry <?= $isActive ? 'active-entry' : '' ?>">
    <header class="word-header">
        <!-- begin of word title -->
        <div class="word-title-group">
            <?php if ($variantCount > 1): ?>
            <span class="entry-index"><?= $index + 1 ?></span>
            <?php endif; ?>
            <h1 class="word-title" data-tfng="<?= e($v['word_tfng']) ?>" data-lat="<?= e($v['word_lat']) ?>"><?= e($v['word_tfng']) ?></h1>
        </div>
        <!-- end of word title -->

        <div class="word-meta">
            <?php if (!empty($v['part_of_speech'])): ?>
            <span class="part-of-speech"><?= __(e($v['part_of_speech'])) ?></span>
            <?php endif; ?>

            <?php 
                $slug = urlencode($v['word_lat']);
                $permalink = BASE_URL . "/word/{$slug}-{$v['id']}";
            ?>
            <a href="<?= $permalink ?>" class="permalink-badge" title="<?= __('Lien direct') ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span><?= __('Link') ?></span>
            </a>
        </div>
    </header>
    
    <section class="definition-list">
        <ol class="definitions">
            <li class="definition-item">
                <p class="french-text"><?= e($v['translation_fr']) ?></p>
                <?php if (!empty($v['definition_tfng'])): ?>
                <p class="definition-text" data-lat="<?= e($v['definition_lat']) ?>" data-tfng="<?= e($v['definition_tfng']) ?>"><?= e($v['definition_tfng']) ?></p>
                <?php endif; ?>
            </li>
        </ol>
    </section>


      <!-- Definitions -->
       <?php if (!empty($v['definition_tfng']) || !empty($v['definition_lat'])): ?>
  <section>
    <h2>📖 Définitions</h2>
    
    <div class="definition" data-lat="<?= e($v['definition_lat']) ?>" data-tfng="<?= e($v['definition_tfng']) ?>">
      <?= e($v['definition_tfng']) ?>
    </div>
  </section>
  <?php endif; ?>
<!-- end of definitions -->

  <!-- Examples -->
  <?php if (!empty($v['examples']) || !empty($v['example_tfng']) || !empty($v['example_lat'])): ?>
  <section>
    <h2>✍️ Exemples</h2>
      <?php if (!empty($v['example_tfng']) || !empty($v['example_lat'])): ?>
                <div class="example">
                    "<?= e($v['example_tfng'] ?? $v['example_lat']) ?>"
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($v['examples'])): ?>
                            <?php foreach ($v['examples'] as $example): ?>
                            <div class="example">
                                "<?= e($example['example_tfng'] ?? $example['example_lat']) ?>"
                                <?php if (!empty($example['example_fr'])): ?>
                                <br><span style="font-size: 0.9em; opacity: 0.8;">– <?= e($example['example_fr']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
  </section>
  <?php endif; ?>
<!-- end of examples -->

  <!-- Morphology -->
    <?php 
    // Check if we have any morphology data to show
    $hasMorphology = !empty($v['root_tfng']) || !empty($v['root_lat']) || 
                     !empty($v['plural_tfng']) || !empty($v['feminine_tfng']) || 
                     !empty($v['annexed_tfng']);
    if($hasMorphology){
    ?>
  <section>
    <h2>🧬 Morphologie</h2>
    <table>
        <?php if (!empty($v['root_tfng']) || !empty($v['root_lat'])): ?>
      <tr><td><?= __('Root') ?></td><td><?= e($v['root_tfng'] ?? $v['root_lat']) ?></td></tr>
      <?php endif; ?>
      <?php if (!empty($v['plural_tfng']) || !empty($v['plural_lat'])): ?>
      <tr><td><?= __('Plural') ?></td><td><?= e($v['plural_tfng'] ?? $v['plural_lat']) ?></td></tr>
      <?php endif; ?>
      <?php if (!empty($v['type_tfng']) || !empty($v['type_lat'])): ?>
      <tr><td><?= __('Type') ?></td><td><?= e($v['type_tfng'] ?? $v['type_lat']) ?></td></tr>
      <?php endif; ?>
      <?php if (!empty($v['formation_tfng']) || !empty($v['formation_lat'])): ?>
      <tr><td><?= __('Formation') ?></td><td><?= e($v['formation_tfng'] ?? $v['formation_lat']) ?></td></tr>
      <?php endif; ?>
    </table>
  </section>
  <?php } ?>
<!-- end of morphology -->
    
    <?php if (!empty($v['etymology'])): ?>
    <section class="word-origin">
        <h2 class="origin-title"><?= __('Origine') ?></h2>
        <p class="origin-text">
            <?= e($v['etymology']['notes_fr'] ?? $v['etymology']['origin_language'] ?? 'N/A') ?>
        </p>
    </section>
    <?php endif; ?>

    <?php 
    // Check if we have any morphology data to show
    $hasMorphology = !empty($v['root_tfng']) || !empty($v['root_lat']) || 
                     !empty($v['plural_tfng']) || !empty($v['feminine_tfng']) || 
                     !empty($v['annexed_tfng']);
    ?>

    <?php if ($hasMorphology): ?>
    <section class="word-morphology mt-3">
        <h4 class="section-title text-muted text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;"><?= __('Morphologie') ?></h4>
        <div class="morphology-grid" style="display: grid; grid-template-columns: auto 1fr; gap: 8px 15px; align-items: baseline;">
            
            <?php if (!empty($v['root_tfng']) || !empty($v['root_lat'])): ?>
            <div class="text-secondary small"><?= __('Racine') ?></div> 
            <div>
                <?php if(!empty($v['root_tfng'])): ?>
                    <a href="<?= getWordLink($v['root_lat']) ?>" class="lex-link fw-bold"><?= e($v['root_tfng']) ?></a> 
                <?php endif; ?>
                <?php if(!empty($v['root_lat'])): ?>
                    <span class="text-muted ms-1">(<?= e($v['root_lat']) ?>)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($v['plural_tfng'])): ?>
            <div class="text-secondary small"><?= __('Pluriel') ?></div> 
            <div>
                <a href="<?= getWordLink($v['plural_lat']) ?>" class="lex-link fw-bold"><?= e($v['plural_tfng']) ?></a> 
                <span class="text-muted ms-1">(<?= e($v['plural_lat']) ?>)</span>
            </div>
            <?php endif; ?>

            <?php if (!empty($v['feminine_tfng'])): ?>
            <div class="text-secondary small"><?= __('Féminin') ?></div> 
            <div>
                <a href="<?= getWordLink($v['feminine_lat']) ?>" class="lex-link fw-bold"><?= e($v['feminine_tfng']) ?></a> 
                <span class="text-muted ms-1">(<?= e($v['feminine_lat']) ?>)</span>
            </div>
            <?php endif; ?>

            <?php if (!empty($v['annexed_tfng'])): ?>
            <div class="text-secondary small"><?= __('État d\'annexion') ?></div> 
            <div>
                <a href="<?= getWordLink($v['annexed_lat']) ?>" class="lex-link fw-bold"><?= e($v['annexed_tfng']) ?></a> 
                <span class="text-muted ms-1">(<?= e($v['annexed_lat']) ?>)</span>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($v['synonyms']) || !empty($v['antonyms'])): ?>
    <section class="word-relations mt-3">
        <h4 class="section-title text-muted text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;"><?= __('Relations Sémantiques') ?></h4>
        <div class="relations-grid" style="display: flex; flex-direction: column; gap: 8px;">
            <?php if (!empty($v['synonyms'])): ?>
                <div class="relation-group">
                    <span class="text-success small fw-bold me-2"><i class="fas fa-equals me-1"></i><?= __('Synonymes') ?></span>
                    <?php foreach ($v['synonyms'] as $i => $syn): ?>
                        <a href="<?= getWordLink($syn['synonym_lat'] ?? '') ?>" class="lex-link"><?= e($syn['synonym_tfng'] ?? $syn['synonym_lat'] ?? '') ?></a><?= $i < count($v['synonyms']) - 1 ? ', ' : '' ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($v['antonyms'])): ?>
                <div class="relation-group">
                    <span class="text-danger small fw-bold me-2"><i class="fas fa-not-equal me-1"></i><?= __('Antonymes') ?></span>
                    <?php foreach ($v['antonyms'] as $i => $ant): ?>
                        <a href="<?= getWordLink($ant['antonym_lat'] ?? '') ?>" class="lex-link"><?= e($ant['antonym_tfng'] ?? $ant['antonym_lat'] ?? '') ?></a><?= $i < count($v['antonyms']) - 1 ? ', ' : '' ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Suggest Edit Button -->
    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
        <div class="word-actions">
            <button class="btn btn-sm btn-link text-muted text-decoration-none me-3" onclick="copyWord(<?= $v['id'] ?>)">
                <i class="far fa-copy me-1"></i> <?= __('Copier') ?>
            </button>
            <button class="btn btn-sm btn-link text-muted text-decoration-none" onclick="shareWord(<?= $v['id'] ?>, '<?= addslashes($v['word_tfng']) ?>')">
                <i class="fas fa-share-alt me-1"></i> <?= __('Partager') ?>
            </button>
        </div>
        <a href="<?= BASE_URL ?>/contribute/word?edit_id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="fas fa-edit me-1"></i> <?= __('Proposer une modification') ?>
        </a>
    </div>
</article>


