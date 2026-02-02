<?php include ROOT_PATH . '/app/views/partials/head.php'; ?>
<?php include ROOT_PATH . '/app/views/partials/navbar.php'; ?>

<div class="main-content bg-white py-5" style="position: relative; z-index: 10;">
    <div class="container">
        <!-- Daily Challenge Banner -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #1e3a5f 0%, #405d72 100%); color: white;">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <span class="text-uppercase fw-bold small mb-2 d-block" style="letter-spacing: 2px; color: var(--quiz-accent);">DÉFI QUOTIDIEN</span>
                                <h2 class="display-6 fw-bold mb-3">Testez votre Tamazight aujourd'hui</h2>
                                <p class="lead mb-4" style="opacity: 0.9;">Relevez le défi quotidien et gagnez des points de réputation.</p>
                                <a href="<?= BASE_URL ?>/quiz/daily" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                    Commencer
                                </a>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block text-center" style="opacity: 0.2;">
                                <i class="fas fa-book-reader fa-8x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Header Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h2 fw-bold mb-0" style="color: var(--quiz-primary);">Tous les Quiz</h1>
                <p class="text-muted mb-0">Explorez notre collection de défis linguistiques.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?= BASE_URL ?>/leaderboard" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-trophy me-2"></i>Classement Général
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm border-0 rounded-4 mb-5 bg-light">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/quizzes" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Difficulté</label>
                        <select name="difficulty" class="form-select border-0 shadow-sm rounded-3">
                            <option value="">Toutes les difficultés</option>
                            <option value="beginner" <?= (isset($difficulty) && $difficulty == 'beginner') ? 'selected' : '' ?>>Débutant</option>
                            <option value="intermediate" <?= (isset($difficulty) && $difficulty == 'intermediate') ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="advanced" <?= (isset($difficulty) && $difficulty == 'advanced') ? 'selected' : '' ?>>Avancé</option>
                            <option value="expert" <?= (isset($difficulty) && $difficulty == 'expert') ? 'selected' : '' ?>>Expert</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Catégorie</label>
                        <select name="category_id" class="form-select border-0 shadow-sm rounded-3">
                            <option value="">Toutes les catégories</option>
                            <?php if (isset($categories)): foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($categoryId) && $categoryId == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 shadow-sm">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quiz Grid -->
        <div class="row g-4 mb-5">
            <?php if (empty($quizzes)): ?>
                <div class="col-12 text-center py-5">
                    <div class="mb-4 text-muted op-2">
                        <i class="fas fa-search fa-4x"></i>
                    </div>
                    <h3>Aucun quiz trouvé</h3>
                    <p class="text-muted">Essayez de changer vos filtres ou revenez plus tard.</p>
                    <a href="<?= BASE_URL ?>/quizzes" class="btn btn-link">Voir tous les quiz</a>
                </div>
            <?php else: ?>
                <?php foreach ($quizzes as $index => $quiz): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="quiz-card card h-100 border-0 shadow-sm rounded-4 position-relative">
                            <?php if ($quiz['is_featured']): ?>
                                <div class="quiz-featured-badge">
                                    <i class="fas fa-star me-1"></i>VÉDETTE
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-3">
                                    <span class="badge bg-light text-primary rounded-pill px-3 py-2 fw-medium border">
                                        <?= htmlspecialchars($quiz['category_name'] ?? 'Général') ?>
                                    </span>
                                </div>
                                
                                <h3 class="h4 fw-bold mb-3" style="color: #263238;"><?= htmlspecialchars($quiz['title_fr']) ?></h3>
                                <p class="text-muted small mb-4 flex-grow-1">
                                    <?= htmlspecialchars($quiz['description_fr'] ?? 'Pas de description.') ?>
                                </p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                    <div class="text-muted small">
                                        <i class="far fa-clock me-1"></i><?= $quiz['estimated_time'] ?? '5' ?> min
                                    </div>
                                    <a href="<?= BASE_URL ?>/quiz/<?= $quiz['id'] ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                                        Jouer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
