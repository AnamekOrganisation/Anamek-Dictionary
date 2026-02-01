<?php
// View: Admin Quiz Question Add/Edit Form
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
$isEdit = isset($question);
$quizId = $isEdit ? $question['quiz_id'] : $quizId;
$action = $isEdit ? '/admin/quiz/question/edit/' . $question['id'] : '/admin/quiz/question/add/' . $quizId;
?>

<div class="admin-layout">
    <?php include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="mb-5">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/quizzes" class="text-decoration-none">Quizz</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/quiz/questions/<?= $quizId ?>" class="text-decoration-none">Questions</a></li>
                    <li class="breadcrumb-item active"><?= $isEdit ? 'Modifier' : 'Ajouter' ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold header-title">
                <i class="fas <?= $isEdit ? 'fa-pen' : 'fa-plus-circle' ?> text-primary me-2"></i>
                <?= $isEdit ? 'Modifier la Question' : 'Nouvelle Question' ?>
            </h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="premium-card rounded-4 shadow-sm p-4 p-lg-5">
                    <form method="POST" action="<?= BASE_URL . $action ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="mb-4">
                            <label class="form-label-custom">Texte de la Question (Français)</label>
                            <input type="text" name="question_text_fr" class="form-control-custom" 
                                   value="<?= htmlspecialchars($question['question_text_fr'] ?? '') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Texte de la Question (Tifinagh - Optionnel)</label>
                            <input type="text" name="question_text_tfng" class="form-control-custom font-tifinagh fs-4" 
                                   value="<?= htmlspecialchars($question['question_text_tfng'] ?? '') ?>">
                        </div>

                        <hr class="my-4 opacity-10">

                        <h5 class="fw-bold mb-4"><i class="fas fa-tasks text-primary me-2"></i>Options et Réponses</h5>
                        <div class="p-4 bg-light rounded-4 mb-4">
                            <p class="small text-muted mb-4">Saisissez les 4 options possibles. Cochez le bouton radio à côté de l'option qui est la réponse correcte.</p>
                            
                            <?php 
                            $options = $question['options'] ?? ['', '', '', ''];
                            for ($i = 0; $i < 4; $i++): 
                                $optValue = $options[$i] ?? '';
                                $isCorrect = ($isEdit && $optValue === $question['correct_answer']);
                            ?>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white border-end-0">
                                            <input class="form-check-input mt-0" type="radio" name="correct_answer_index" value="<?= $i ?>" <?= $isCorrect ? 'checked' : '' ?> required title="Marquer comme réponse correcte">
                                        </div>
                                        <input type="text" name="options[]" class="form-control-custom border-start-0" 
                                               placeholder="Option <?= chr(65 + $i) ?>" value="<?= htmlspecialchars($optValue) ?>" required>
                                    </div>
                                </div>
                            <?php endfor; ?>
                            
                            <div class="small text-danger mt-2">
                                <i class="fas fa-info-circle me-1"></i> La "Réponse Correcte" sera automatiquement extraite de l'option cochée lors de l'enregistrement.
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label-custom">Points accordés</label>
                                <input type="number" name="points" class="form-control-custom" value="<?= $question['points'] ?? '10' ?>" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Ordre d'affichage</label>
                                <input type="number" name="display_order" class="form-control-custom" value="<?= $question['display_order'] ?? '0' ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-5">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> <?= $isEdit ? 'Mettre à jour' : 'Ajouter la Question' ?>
                            </button>
                            <a href="<?= BASE_URL ?>/admin/quiz/questions/<?= $quizId ?>" class="btn btn-light rounded-pill px-4 text-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Handle radio selection to pass correct text value (handled in controller instead for simplicity, 
// but we'll add a hidden field or adjust controller logic as planned)
// Actually, I'll adjust the controller to handle correct_answer based on correct_answer_index.
</script>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { background: white; border: 1px solid rgba(0,0,0,0.05); }
.form-label-custom { display: block; font-size: 0.875rem; font-weight: 700; color: #344767; margin-bottom: 0.5rem; }
.form-control-custom { width: 100%; padding: 0.75rem 1rem; font-size: 0.95rem; color: #495057; background-color: #fff; border: 1px solid #d2d6da; border-radius: 0.5rem; transition: all 0.2s ease-in-out; }
.form-control-custom:focus { border-color: #181d4b; outline: 0; box-shadow: 0 0 0 3px rgba(24, 29, 75, 0.1); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
