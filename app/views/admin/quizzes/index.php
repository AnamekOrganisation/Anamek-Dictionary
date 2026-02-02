<?php
// View: Admin Quiz List
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold mb-1 header-title">
                    <i class="fas fa-question-circle text-primary me-2"></i>Gestion des Quizz
                </h2>
                <p class="text-secondary mb-0">Créez et gérez les défis linguistiques pour les utilisateurs.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/quiz/add" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hover-up">
                <i class="fas fa-plus me-2"></i>Nouveau Quiz
            </a>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm" role="alert">
                <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm" role="alert">
                <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="premium-card rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase fs-7 fw-bold">Statut</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold">Titre (FR)</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold">Catégorie</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold">Difficulté</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold text-center">Questions</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase fs-7 fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-plus-circle fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Commencez par créer votre premier quiz !</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quizzes as $q): ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($q['is_active']): ?>
                                            <span class="badge bg-success-soft text-success rounded-pill px-3">Actif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-soft text-secondary rounded-pill px-3">Brouillon</span>
                                        <?php endif; ?>
                                        <?php if ($q['is_featured']): ?>
                                            <i class="fas fa-star text-warning ms-1" title="Mis en avant"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($q['title_fr']) ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($q['description_fr']) ?></div>
                                    </td>
                                    <td class="text-secondary"><?= htmlspecialchars($q['category_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="small fw-bold text-uppercase"><?= $q['difficulty_level'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $qStmt = $this->pdo->prepare("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = ?");
                                            $qStmt->execute([$q['id']]);
                                            $count = $qStmt->fetchColumn();
                                        ?>
                                        <span class="badge bg-light text-dark border"><?= $count ?></span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= BASE_URL ?>/admin/quiz/questions/<?= $q['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3 action-btn-text" title="Gérer les questions">
                                                <i class="fas fa-list-ul me-1"></i> Questions
                                            </a>
                                            <a href="<?= BASE_URL ?>/admin/quiz/edit/<?= $q['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle action-btn" title="Modifier">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form method="POST" action="<?= BASE_URL ?>/admin/quiz/delete" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $q['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            <a href="<?= BASE_URL ?>/quiz/<?= $q['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle action-btn" title="Voir">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { background: white; border: 1px solid rgba(0,0,0,0.05); }
.fs-7 { font-size: 0.75rem; }
.action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
.action-btn-text { height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600; }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }
.hover-up { transition: transform 0.2s; }
.hover-up:hover { transform: translateY(-2px); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
