<?php
// View: Redesigned Add Word with High Contrast
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
                    <i class="fas fa-plus-circle text-primary me-2"></i>Nouveau Mot
                </h2>
                <p class="text-secondary mb-0">Ajoutez une nouvelle entrée au dictionnaire avec des détails complets.</p>
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
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <!-- Section 1: Core Information -->
                <div class="form-section p-4 p-lg-5">
                    <div class="section-header mb-4">
                        <span class="section-number">01</span>
                        <h5 class="fw-bold mb-0">Informations Principales</h5>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Mot (Tifinagh) <span class="text-danger">*</span></label>
                            <input type="text" name="word_tfng" class="form-control-custom font-tifinagh fs-4" placeholder="ⵣ" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Mot (Latin) <span class="text-danger">*</span></label>
                            <input type="text" name="word_lat" class="form-control-custom fw-bold" placeholder="Azul" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Traduction Française <span class="text-danger">*</span></label>
                            <input type="text" name="translation_fr" class="form-control-custom" placeholder="Ex: Bonjour" required>
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
                                <option value="">Choisir...</option>
                                <option value="noun">Nom (Amagrad)</option>
                                <option value="verb">Verbe (Amyag)</option>
                                <option value="adjective">Adjectif (Irsm)</option>
                                <option value="adverb">Adverbe (Asfka)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Genre</label>
                            <select name="gender" class="form-select form-control-custom">
                                <option value="">Non défini</option>
                                <option value="masculine">Masculin (Amalay)</option>
                                <option value="feminine">Féminin (Unti)</option>
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
                            <input type="text" name="root_tfng" class="form-control-custom font-tifinagh" placeholder="ⵣ">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Racine (Latin)</label>
                            <input type="text" name="root_lat" class="form-control-custom" placeholder="Z">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Pluriel (Tifinagh)</label>
                            <input type="text" name="plural_tfng" class="form-control-custom font-tifinagh" placeholder="Pluriel">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Pluriel (Latin)</label>
                            <input type="text" name="plural_lat" class="form-control-custom" placeholder="Pluriel">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Féminin (Tifinagh)</label>
                            <input type="text" name="feminine_tfng" class="form-control-custom font-tifinagh" placeholder="Féminin">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label-custom">Féminin (Latin)</label>
                            <input type="text" name="feminine_lat" class="form-control-custom" placeholder="Féminin">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Definitions -->
                <div class="form-section bg-light-subtle p-4 p-lg-5 border-top">
                    <div class="section-header mb-4">
                        <span class="section-number">04</span>
                        <h5 class="fw-bold mb-0">Définitions Détaillées</h5>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Définition (Tifinagh)</label>
                            <textarea name="definition_tfng" class="form-control-custom font-tifinagh" rows="4" placeholder="ⴰⵙⵏⵎⵍ..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Définition (Latin)</label>
                            <textarea name="definition_lat" class="form-control-custom" rows="4" placeholder="Définition en latin..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Area -->
                <div class="p-4 p-lg-5 text-end bg-white">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-up">
                        <i class="fas fa-save me-2"></i>Enregistrer le mot
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<style>
/* Dashboard Styles are now in admin.css, but we keep core functional overrides here if needed */
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