<?php
// View: Admin Quiz Add/Edit Form
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
$isEdit = isset($quiz);
$action = $isEdit ? '/admin/quiz/edit/' . $quiz['id'] : '/admin/quiz/add';
?>

<div class="admin-layout">
    <?php include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="mb-5">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/quizzes" class="text-decoration-none">Quizz</a></li>
                    <li class="breadcrumb-item active"><?= $isEdit ? 'Modifier' : 'Ajouter' ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold header-title">
                <i class="fas <?= $isEdit ? 'fa-pen' : 'fa-plus-circle' ?> text-primary me-2"></i>
                <?= $isEdit ? 'Modifier le Quiz' : 'Créer un Nouveau Quiz' ?>
            </h2>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="premium-card rounded-4 shadow-sm p-4 p-lg-5">
                    <form method="POST" action="<?= BASE_URL . $action ?>">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <label class="form-label-custom">Titre du Quiz (Français)</label>
                            <input type="text" name="title_fr" class="form-control-custom" 
                                   value="<?= htmlspecialchars($quiz['title_fr'] ?? '') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Description</label>
                            <textarea name="description_fr" class="form-control-custom" rows="3"><?= htmlspecialchars($quiz['description_fr'] ?? '') ?></textarea>
                            <div class="small text-muted mt-1">Expliquez aux utilisateurs l'objectif de ce quiz.</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Catégorie</label>
                                <select name="category_id" class="form-control-custom" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (isset($quiz) && $quiz['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Niveau de Difficulté</label>
                                <select name="difficulty_level" class="form-control-custom" required>
                                    <option value="beginner" <?= (isset($quiz) && $quiz['difficulty_level'] == 'beginner') ? 'selected' : '' ?>>Débutant</option>
                                    <option value="intermediate" <?= (isset($quiz) && $quiz['difficulty_level'] == 'intermediate') ? 'selected' : '' ?>>Intermédiaire</option>
                                    <option value="advanced" <?= (isset($quiz) && $quiz['difficulty_level'] == 'advanced') ? 'selected' : '' ?>>Avancé</option>
                                    <option value="expert" <?= (isset($quiz) && $quiz['difficulty_level'] == 'expert') ? 'selected' : '' ?>>Expert</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Temps estimé (minutes)</label>
                                <input type="number" name="estimated_time" class="form-control-custom" 
                                       value="<?= $quiz['estimated_time'] ?? '5' ?>" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Score de passage (%)</label>
                                <input type="number" name="passing_score" class="form-control-custom" 
                                       value="<?= $quiz['passing_score'] ?? '70' ?>" min="0" max="100">
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" 
                                       <?= (!isset($quiz) || $quiz['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="isActive">Rendre le quiz actif</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" 
                                       <?= (isset($quiz) && $quiz['is_featured']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="isFeatured">Mettre en avant sur l'accueil</label>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-5">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le Quiz' ?>
                            </button>
                            <a href="<?= BASE_URL ?>/admin/quizzes" class="btn btn-light rounded-pill px-4 text-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="premium-card rounded-4 shadow-sm p-4 border-start border-4 border-primary">
                    <h5 class="fw-bold mb-3">Conseils de création</h5>
                    <ul class="small text-muted ps-3">
                        <li class="mb-2">Utilisez un titre clair et accrocheur.</li>
                        <li class="mb-2">La difficulté doit correspondre au vocabulaire utilisé.</li>
                        <li class="mb-2">Un score de passage de 70% est standard.</li>
                        <li>Après avoir créé le quiz, vous pourrez ajouter les questions.</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { background: white; border: 1px solid rgba(0,0,0,0.05); }
.form-label-custom { display: block; font-size: 0.875rem; font-weight: 700; color: #344767; margin-bottom: 0.5rem; }
.form-control-custom { width: 100%; padding: 0.75rem 1rem; font-size: 0.95rem; color: #495057; background-color: #fff; border: 1px solid #d2d6da; border-radius: 0.5rem; transition: all 0.2s ease-in-out; }
.form-control-custom:focus { border-color: #181d4b; outline: 0; box-shadow: 0 0 0 3px rgba(24, 29, 75, 0.1); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
