<?php
// View: Admin Quiz Questions Management
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="mb-5">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/quizzes" class="text-decoration-none">Quizz</a></li>
                    <li class="breadcrumb-item active">Questions</li>
                </ol>
            </nav>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="fw-bold mb-1 header-title">
                        <i class="fas fa-list-ul text-primary me-2"></i>Questions : <?= htmlspecialchars($quiz['title_fr']) ?>
                    </h2>
                    <p class="text-secondary mb-0">Gérez les questions, les options et les réponses correctes pour ce quiz.</p>
                </div>
                <a href="<?= BASE_URL ?>/admin/quiz/question/add/<?= $quiz['id'] ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hover-up">
                    <i class="fas fa-plus me-2"></i>Ajouter une Question
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm" role="alert">
                <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($questions)): ?>
            <div class="premium-card rounded-4 shadow-sm p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-question-circle fa-4x opacity-10 text-primary"></i>
                </div>
                <h4 class="fw-bold">Aucune question pour le moment</h4>
                <p class="text-secondary mb-4">Ce quiz ne sera pas visible par les utilisateurs tant qu'il n'aura pas au moins une question.</p>
                <a href="<?= BASE_URL ?>/admin/quiz/question/add/<?= $quiz['id'] ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
                    Créer la première question
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="col-12">
                        <div class="premium-card rounded-4 shadow-sm p-4 h-100 border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-light text-primary border rounded-pill px-3">Question <?= $index + 1 ?></span>
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>/admin/quiz/question/edit/<?= $q['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle action-btn shadow-none">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/quiz/question/delete" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $q['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn shadow-none">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold mb-3 text-dark"><?= htmlspecialchars($q['question_text_fr']) ?></h5>
                            <?php if ($q['question_text_tfng']): ?>
                                <div class="mb-4 p-3 bg-light rounded-3 font-tifinagh fs-4 text-center">
                                    <?= htmlspecialchars($q['question_text_tfng']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="row g-2">
                                <?php foreach ($q['options'] as $opt): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 border <?= ($opt === $q['correct_answer']) ? 'bg-success-soft border-success text-success fw-bold' : 'bg-white border-light text-secondary' ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><?= htmlspecialchars($opt) ?></span>
                                                <?php if ($opt === $q['correct_answer']): ?>
                                                    <i class="fas fa-check-circle"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="fas fa-star me-1"></i> Points : <?= $q['points'] ?></span>
                                <span><i class="fas fa-sort me-1"></i> Ordre : <?= $q['display_order'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { background: white; border: 1px solid rgba(0,0,0,0.05); }
.action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.05); }
.hover-up { transition: transform 0.2s; }
.hover-up:hover { transform: translateY(-2px); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
