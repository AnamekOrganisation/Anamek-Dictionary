<?php 
$isEdit = isset($word) && !empty($word['id']);
$page_title = $isEdit ? __('Suggérer une modification') : __('Contribuer un mot');
include ROOT_PATH . '/app/views/partials/header.php'; 
?>

<div class="main-content bg-light py-5" style="border-radius: 30px 30px 0 0; margin-top: -30px; position: relative; z-index: 10;">
    <div class="container" style="max-width: 900px;">
        <div class="contribution-card bg-white p-4 p-md-5 shadow-sm border-0" style="border-radius: 20px;">
            <div class="row mb-5 align-items-center">
                <div class="col-auto">
                    <div class="icon-box bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus' ?> fa-lg"></i>
                    </div>
                </div>
                <div class="col">
                    <h1 class="h2 fw-bold mb-1" style="color: var(--lex-primary);"><?= $page_title ?></h1>
                    <p class="text-muted mb-0"><?= __('Enrichissez le savoir amazigh en partageant vos connaissances.') ?></p>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/contribute/word" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="target_id" value="<?= $word['id'] ?>">
                <?php endif; ?>

                <!-- Section 1: Identité -->
                <div class="form-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge bg-primary-soft text-primary me-2 px-3 py-2" style="background: rgba(var(--bs-primary-rgb), 0.1);">1</span>
                        <h3 class="h5 fw-bold mb-0"><?= __('Identité du mot') ?></h3>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><?= __('Tifinagh') ?> *</label>
                            <input type="text" name="word_tfng" class="form-control tifinagh-input form-control-lg" required value="<?= htmlspecialchars($word['word_tfng'] ?? '') ?>" placeholder="Ex: ⴰⵎⴰⵡⴰⵍ">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><?= __('Latin') ?> *</label>
                            <input type="text" name="word_lat" class="form-control form-control-lg" required value="<?= htmlspecialchars($word['word_lat'] ?? '') ?>" placeholder="Ex: Amawal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><?= __('Traduction Fr') ?> *</label>
                            <input type="text" name="translation_fr" class="form-control form-control-lg" required value="<?= htmlspecialchars($word['translation_fr'] ?? '') ?>" placeholder="Ex: Dictionnaire">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Grammaire -->
                <div class="form-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge bg-primary-soft text-primary me-2 px-3 py-2" style="background: rgba(var(--bs-primary-rgb), 0.1);">2</span>
                        <h3 class="h5 fw-bold mb-0"><?= __('Grammaire & Formes') ?></h3>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('Nature grammaticale') ?></label>
                            <select name="word_type" class="form-select form-control-lg">
                                <option value=""><?= __('Sélectionner...') ?></option>
                                <?php 
                                $types = ['nom' => 'Nom', 'verbe' => 'Verbe', 'adjectif' => 'Adjectif', 'adverbe' => 'Adverbe', 'pronom' => 'Pronom', 'conjonction' => 'Conjonction'];
                                foreach ($types as $val => $label): 
                                    $selected = ($word['part_of_speech'] ?? '') == $val ? 'selected' : '';
                                ?>
                                    <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><?= __('Pluriel (Tfng)') ?></label>
                            <input type="text" name="plural_tfng" class="form-control tifinagh-input" value="<?= htmlspecialchars($word['plural_tfng'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><?= __('Pluriel (Lat)') ?></label>
                            <input type="text" name="plural_lat" class="form-control" value="<?= htmlspecialchars($word['plural_lat'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><?= __('Féminin (Tfng)') ?></label>
                            <input type="text" name="feminine_tfng" class="form-control tifinagh-input" value="<?= htmlspecialchars($word['feminine_tfng'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><?= __('Féminin (Lat)') ?></label>
                            <input type="text" name="feminine_lat" class="form-control" value="<?= htmlspecialchars($word['feminine_lat'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><?= __('État d\'annexion (Tfng)') ?></label>
                            <input type="text" name="annexed_tfng" class="form-control tifinagh-input" value="<?= htmlspecialchars($word['annexed_tfng'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><?= __('État d\'annexion (Lat)') ?></label>
                            <input type="text" name="annexed_lat" class="form-control" value="<?= htmlspecialchars($word['annexed_lat'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Relations -->
                <div class="form-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge bg-primary-soft text-primary me-2 px-3 py-2" style="background: rgba(var(--bs-primary-rgb), 0.1);">3</span>
                        <h3 class="h5 fw-bold mb-0"><?= __('Relations Sémantiques') ?></h3>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('Racine (Tfng)') ?></label>
                            <input type="text" name="root_tfng" class="form-control tifinagh-input" value="<?= htmlspecialchars($word['root_tfng'] ?? '') ?>" placeholder="Ex: ⵎⵡⵍ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('Racine (Lat)') ?></label>
                            <input type="text" name="root_lat" class="form-control" value="<?= htmlspecialchars($word['root_lat'] ?? '') ?>" placeholder="Ex: MWL">
                        </div>
                    </div>

                    <!-- Synonyms Dynamic Section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <?= __('Synonymes') ?>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="addRelation('synonyms')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </label>
                        <div id="synonyms-container" class="relation-grid">
                            <?php 
                            $syns = $word['synonyms'] ?? [];
                            if (empty($syns)) $syns = [['synonym_tfng' => '', 'synonym_lat' => '']];
                            foreach ($syns as $i => $syn): ?>
                                <div class="relation-item row g-2 mb-2">
                                    <div class="col-5">
                                        <input type="text" name="synonyms_tfng[]" class="form-control tifinagh-input" value="<?= htmlspecialchars($syn['synonym_tfng'] ?? '') ?>" placeholder="Tfng">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="synonyms_lat[]" class="form-control" value="<?= htmlspecialchars($syn['synonym_lat'] ?? '') ?>" placeholder="Lat">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Antonyms Dynamic Section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <?= __('Antonymes') ?>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="addRelation('antonyms')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </label>
                        <div id="antonyms-container" class="relation-grid">
                            <?php 
                            $ants = $word['antonyms'] ?? [];
                            if (empty($ants)) $ants = [['antonym_tfng' => '', 'antonym_lat' => '']];
                            foreach ($ants as $i => $ant): ?>
                                <div class="relation-item row g-2 mb-2">
                                    <div class="col-5">
                                        <input type="text" name="antonyms_tfng[]" class="form-control tifinagh-input" value="<?= htmlspecialchars($ant['antonym_tfng'] ?? '') ?>" placeholder="Tfng">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="antonyms_lat[]" class="form-control" value="<?= htmlspecialchars($ant['antonym_lat'] ?? '') ?>" placeholder="Lat">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Définitions -->
                <div class="form-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge bg-primary-soft text-primary me-2 px-3 py-2" style="background: rgba(var(--bs-primary-rgb), 0.1);">4</span>
                        <h3 class="h5 fw-bold mb-0"><?= __('Explications & Définitions') ?></h3>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold"><?= __('Définition (Tifinagh)') ?></label>
                        <textarea name="definition_tfng" class="form-control tifinagh-input" rows="3"><?= htmlspecialchars($word['definition_tfng'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold"><?= __('Définition (Tamazight Latin)') ?></label>
                        <textarea name="definition_lat" class="form-control" rows="3"><?= htmlspecialchars($word['definition_lat'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Section 5: Exemples -->
                <div class="form-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge bg-primary-soft text-primary me-2 px-3 py-2" style="background: rgba(var(--bs-primary-rgb), 0.1);">5</span>
                        <h3 class="h5 fw-bold mb-0"><?= __('Exemples d\'utilisation') ?></h3>
                    </div>
                    
                    <!-- Dynamic Examples Section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <?= __('Exemples') ?>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="addRelation('examples')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </label>
                        <div id="examples-container" class="relation-grid">
                            <?php 
                            $exs = $word['examples'] ?? [];
                            if (empty($exs)) $exs = [['example_tfng' => $word['example_tfng'] ?? '', 'example_lat' => $word['example_lat'] ?? '', 'example_fr' => '']];
                            foreach ($exs as $i => $ex): ?>
                                <div class="relation-item row g-2 mb-2 p-2 border rounded">
                                    <div class="col-md-4">
                                        <input type="text" name="examples_tfng[]" class="form-control tifinagh-input" value="<?= htmlspecialchars($ex['example_tfng'] ?? '') ?>" placeholder="Ex: ⴰⵣⵓⵍ ⴼⵍⵍⴰⵡⵏ">
                                        <small class="text-muted">Tifinagh</small>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="examples_lat[]" class="form-control" value="<?= htmlspecialchars($ex['example_lat'] ?? '') ?>" placeholder="Ex: Azul fellawen">
                                        <small class="text-muted">Latin</small>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="examples_fr[]" class="form-control" value="<?= htmlspecialchars($ex['example_fr'] ?? '') ?>" placeholder="Bonjour à tous">
                                        <small class="text-muted">Traduction FR</small>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-sm fw-bold">
                        <i class="fas fa-paper-plane me-2"></i> <?= $isEdit ? __('Soumettre la modification') : __('Soumettre pour révision') ?>
                    </button>
                    <p class="text-muted mt-3 small">
                        <i class="fas fa-info-circle me-1"></i> <?= __('Votre contribution sera vérifiée par un modérateur avant publication.') ?>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addRelation(type) {
    const container = document.getElementById(type + '-container');
    const div = document.createElement('div');
    div.className = 'relation-item row g-2 mb-2';
    
    if (type === 'examples') {
        div.className += ' p-2 border rounded';
        div.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="examples_tfng[]" class="form-control tifinagh-input" placeholder="Ex: ⴰⵣⵓⵍ ⴼⵍⵍⴰⵡⵏ">
                <small class="text-muted">Tifinagh</small>
            </div>
            <div class="col-md-4">
                <input type="text" name="examples_lat[]" class="form-control" placeholder="Ex: Azul fellawen">
                <small class="text-muted">Latin</small>
            </div>
            <div class="col-md-3">
                <input type="text" name="examples_fr[]" class="form-control" placeholder="Bonjour à tous">
                <small class="text-muted">Traduction FR</small>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="col-5">
                <input type="text" name="${type}_tfng[]" class="form-control tifinagh-input" placeholder="Tfng">
            </div>
            <div class="col-5">
                <input type="text" name="${type}_lat[]" class="form-control" placeholder="Lat">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.relation-item').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }
    container.appendChild(div);
}
</script>

<style>
.form-section {
    background: #fff;
    padding: 2.5rem;
    border-radius: 20px;
    border: 2px solid var(--lex-border);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.form-label {
    color: var(--lex-text);
    font-size: 0.95rem;
}
.form-control, .form-select {
    border: 1.5px solid var(--lex-border);
    transition: all 0.2s ease;
}
.form-control:focus, .form-select:focus {
    border-color: var(--lex-accent);
    box-shadow: 0 0 0 4px rgba(var(--lex-accent-rgb, 179, 134, 0), 0.15);
}
.form-control::placeholder {
    color: #94a3b8;
}
.tifinagh-input {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--lex-primary);
}
.btn-primary {
    background: var(--lex-primary);
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-primary:hover {
    background: #000;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
.bg-primary-soft {
    background: rgba(26, 42, 58, 0.1) !important;
}
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
