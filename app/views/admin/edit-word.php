<?php
// View: Redesigned Edit Word with High Contrast
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php 
    $current_page = 'words';
    include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
    ?>

    <main class="admin-main p-4 p-lg-5">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold mb-1 header-title">
                    <i class="fas fa-edit text-primary me-2"></i>Modifier le Mot
                </h2>
                <p class="text-secondary mb-0">Mettez à jour les informations détaillées de l'entrée sélectionnée.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/words" class="btn btn-outline-dark rounded-pill px-4 fw-600">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $result ? 'alert-success' : 'alert-danger' ?> rounded-4 border-0 shadow-sm mb-4 p-3 d-flex align-items-center">
                <i class="fas <?= $result ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-3 fa-lg"></i>
                <div><?= htmlspecialchars($message) ?></div>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="premium-card rounded-4 shadow-sm overflow-hidden mb-5">
            <form method="POST" class="p-0">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $word['id'] ?>">

                <!-- Section 1: Core Information -->
                <div class="form-section p-4 p-lg-5">
                    <div class="section-header mb-4">
                        <span class="section-number">01</span>
                        <h5 class="fw-bold mb-0">Informations Principales</h5>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Mot (Tifinagh) <span class="text-danger">*</span></label>
                            <input type="text" name="word_tfng" class="form-control-custom font-tifinagh fs-4" value="<?= htmlspecialchars($word['word_tfng']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Mot (Latin) <span class="text-danger">*</span></label>
                            <input type="text" name="word_lat" class="form-control-custom fw-bold" value="<?= htmlspecialchars($word['word_lat']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Traduction Française <span class="text-danger">*</span></label>
                            <input type="text" name="translation_fr" class="form-control-custom" value="<?= htmlspecialchars($word['translation_fr']) ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Grammar -->
                <div class="form-section bg-light-subtle p-4 p-lg-5 border-top border-bottom">
                    <div class="section-header mb-4">
                        <span class="section-number">02</span>
                        <h5 class="fw-bold mb-0">Grammaire & Genre</h5>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Catégorie grammaticale</label>
                            <select name="part_of_speech" class="form-select form-control-custom">
                                <option value="" <?= empty($word['part_of_speech']) ? 'selected' : '' ?>>Choisir...</option>
                                <option value="noun" <?= $word['part_of_speech'] == 'noun' ? 'selected' : '' ?>>Nom</option>
                                <option value="verb" <?= $word['part_of_speech'] == 'verb' ? 'selected' : '' ?>>Verbe</option>
                                <option value="adjective" <?= $word['part_of_speech'] == 'adjective' ? 'selected' : '' ?>>Adjectif</option>
                                <option value="adverb" <?= $word['part_of_speech'] == 'adverb' ? 'selected' : '' ?>>Adverbe</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Genre</label>
                            <select name="gender" class="form-select form-control-custom">
                                <option value="" <?= empty($word['gender']) ? 'selected' : '' ?>>Non défini</option>
                                <option value="masculine" <?= $word['gender'] == 'masculine' ? 'selected' : '' ?>>Masculin</option>
                                <option value="feminine" <?= $word['gender'] == 'feminine' ? 'selected' : '' ?>>Féminin</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Roots & Variants -->
                 <div class="form-section p-4 p-lg-5">
                    <div class="section-header mb-4">
                        <span class="section-number">03</span>
                        <h5 class="fw-bold mb-0">Racines & Flexions</h5>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Racine (Tifinagh)</label>
                            <input value="<?= htmlspecialchars($word['root_tfng'] ?? '') ?>" type="text" name="root_tfng" class="form-control-custom font-tifinagh" placeholder="ⵣ">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Racine (Latin)</label>
                            <input type="text" name="root_lat" class="form-control-custom" value="<?= htmlspecialchars($word['root_lat'] ?? '') ?>" placeholder="Z">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Pluriel (Tifinagh)</label>
                            <input type="text" name="plural_tfng" class="form-control-custom font-tifinagh" value="<?= htmlspecialchars($word['plural_tfng'] ?? '') ?>" placeholder="Pluriel">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Pluriel (Latin)</label>
                            <input type="text" name="plural_lat" class="form-control-custom" value="<?= htmlspecialchars($word['plural_lat'] ?? '') ?>" placeholder="Pluriel">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Féminin (Tifinagh)</label>
                            <input type="text" name="feminine_tfng" class="form-control-custom font-tifinagh" value="<?= htmlspecialchars($word['feminine_tfng'] ?? '') ?>" placeholder="Féminin">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Féminin (Latin)</label>
                            <input type="text" name="feminine_lat" class="form-control-custom" value="<?= htmlspecialchars($word['feminine_lat'] ?? '') ?>" placeholder="Féminin">
                        </div>
                    </div>
                </div>

        <!-- Section 4: Synonyms and Antonyms -->
         <div class="form-section bg-light-subtle p-4 p-lg-5 border-top">
                    <div class="section-header mb-4">
                        <span class="section-number">04</span>
                        <h5 class="fw-bold mb-0">Synonymes et Antonymes</h5>
                    </div>
                    
                    <!-- Synonyms Dynamic Section -->
                    <div class="mb-4">
                        <label class="form-label-custom d-flex justify-content-between align-items-center">
                            <span>Synonymes</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRelation('synonyms')">
                                <i class="fas fa-plus me-1"></i> Ajouter un synonyme
                            </button>
                        </label>
                        <div id="synonyms-container">
                            <?php 
                            $syns = $synonyms ?? [];
                            if (empty($syns)) $syns = [['synonym_tfng' => '', 'synonym_lat' => '']];
                            foreach ($syns as $i => $syn): ?>
                                <div class="relation-item row g-2 mb-2">
                                    <div class="col-md-5">
                                        <input type="text" name="synonyms_tfng[]" class="form-control font-tifinagh" value="<?= htmlspecialchars($syn['synonym_tfng'] ?? '') ?>" placeholder="Tifinagh">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="synonyms_lat[]" class="form-control" value="<?= htmlspecialchars($syn['synonym_lat'] ?? '') ?>" placeholder="Latin">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Antonyms Dynamic Section -->
                    <div class="mb-0">
                        <label class="form-label-custom d-flex justify-content-between align-items-center">
                            <span>Antonymes</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRelation('antonyms')">
                                <i class="fas fa-plus me-1"></i> Ajouter un antonyme
                            </button>
                        </label>
                        <div id="antonyms-container">
                            <?php 
                            $ants = $antonyms ?? [];
                            if (empty($ants)) $ants = [['antonym_tfng' => '', 'antonym_lat' => '']];
                            foreach ($ants as $i => $ant): ?>
                                <div class="relation-item row g-2 mb-2">
                                    <div class="col-md-5">
                                        <input type="text" name="antonyms_tfng[]" class="form-control font-tifinagh" value="<?= htmlspecialchars($ant['antonym_tfng'] ?? '') ?>" placeholder="Tifinagh">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="antonyms_lat[]" class="form-control" value="<?= htmlspecialchars($ant['antonym_lat'] ?? '') ?>" placeholder="Latin">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <!-- Section 5: Definitions -->
                <div class="form-section bg-light-subtle p-4 p-lg-5 border-top">
                    <div class="section-header mb-4">
                        <span class="section-number">05</span>
                        <h5 class="fw-bold mb-0">Définitions Détaillées</h5>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Définition (Tifinagh)</label>
                            <textarea name="definition_tfng" class="form-control-custom font-tifinagh" rows="4"><?= htmlspecialchars($word['definition_tfng']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Définition (Latin)</label>
                            <textarea name="definition_lat" class="form-control-custom" rows="4"><?= htmlspecialchars($word['definition_lat']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Examples -->
                <div class="form-section bg-light-subtle p-4 p-lg-5 border-top">
                    <div class="section-header mb-4">
                        <span class="section-number">06</span>
                        <h5 class="fw-bold mb-0">Exemples</h5>
                    </div>
                    
                    <!-- Main Example (from words table) -->
                    <div class="row g-4 mb-4 p-3 border-bottom">
                        <div class="col-md-6">
                            <label class="form-label-custom">Exemple principal (Tifinagh)</label>
                            <textarea name="example_tfng" class="form-control-custom font-tifinagh" rows="2"><?= htmlspecialchars($word['example_tfng']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Exemple principal (Latin)</label>
                            <textarea name="example_lat" class="form-control-custom" rows="2"><?= htmlspecialchars($word['example_lat']) ?></textarea>
                        </div>
                    </div>

                    <!-- Additional Examples (from examples table) -->
                    <div class="mb-3">
                        <label class="form-label-custom d-flex justify-content-between align-items-center">
                            <span>Exemples supplémentaires</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addExample()">
                                <i class="fas fa-plus me-1"></i> Ajouter un exemple
                            </button>
                        </label>
                        <div id="examples-container">
                            <?php 
                            $exs = $examples ?? [];
                            if (empty($exs)) $exs = [['id' => '', 'example_tfng' => '', 'example_lat' => '', 'example_fr' => '']];
                            foreach ($exs as $i => $ex): ?>
                                <div class="example-item row g-2 mb-3 p-3 border rounded">
                                    <input type="hidden" name="example_ids[]" value="<?= $ex['id'] ?? '' ?>">
                                    <div class="col-md-4">
                                        <label class="small text-muted">Tifinagh</label>
                                        <textarea name="examples_tfng[]" class="form-control font-tifinagh" rows="2"><?= htmlspecialchars($ex['example_tfng'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted">Latin</label>
                                        <textarea name="examples_lat[]" class="form-control" rows="2"><?= htmlspecialchars($ex['example_lat'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted">Traduction FR</label>
                                        <textarea name="examples_fr[]" class="form-control" rows="2"><?= htmlspecialchars($ex['example_fr'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.example-item').remove()" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <script>
                function addExample() {
                    const container = document.getElementById('examples-container');
                    const div = document.createElement('div');
                    div.className = 'example-item row g-2 mb-3 p-3 border rounded';
                    div.innerHTML = `
                        <input type="hidden" name="example_ids[]" value="">
                        <div class="col-md-4">
                            <label class="small text-muted">Tifinagh</label>
                            <textarea name="examples_tfng[]" class="form-control font-tifinagh" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">Latin</label>
                            <textarea name="examples_lat[]" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Traduction FR</label>
                            <textarea name="examples_fr[]" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.example-item').remove()" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(div);
                }

                function addRelation(type) {
                    const container = document.getElementById(type + '-container');
                    const div = document.createElement('div');
                    div.className = 'relation-item row g-2 mb-2';
                    
                    const fieldName = type === 'synonyms' ? 'synonyms' : 'antonyms';
                    div.innerHTML = `
                        <div class="col-md-5">
                            <input type="text" name="${fieldName}_tfng[]" class="form-control font-tifinagh" placeholder="Tifinagh">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="${fieldName}_lat[]" class="form-control" placeholder="Latin">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(div);
                }
                </script>

                <!-- Submit Area -->
                <div class="p-4 p-lg-5 text-end bg-white">
                    <button type="submit" name="update_word" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-up">
                        <i class="fas fa-check-circle me-2"></i>Mettre à jour le mot
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.section-header { display: flex; align-items: center; gap: 15px; }
.section-number { 
    background: #181d4b; 
    color: white; 
    width: 28px; 
    height: 28px; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 0.75rem; 
    font-weight: 800; 
}
.hover-up { transition: transform 0.2s; }
.hover-up:hover { transform: translateY(-2px); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
