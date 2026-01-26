<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center quiz-animate-up">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-5">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/quizzes" class="text-decoration-none">Quiz</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($quiz['title_fr']) ?></li>
                </ol>
            </nav>

            <div class="quiz-question-container mb-5">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="text-uppercase fw-bold small mb-2 d-block" style="color: var(--quiz-primary); letter-spacing: 1px;">
                            <?= htmlspecialchars($quiz['category_name'] ?? 'ÉVALUATION LINGUISTIQUE') ?>
                        </span>
                        <h1 class="fw-bold mb-3 display-6" style="color: #263238;"><?= htmlspecialchars($quiz['title_fr']) ?></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/quizzes" class="text-decoration-none text-muted">Quiz</a></li>
                                <li class="breadcrumb-item active"><?= htmlspecialchars($quiz['title_fr']) ?></li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-auto d-none d-md-block op-1">
                        <i class="fas fa-graduation-cap fa-4x" style="color: var(--quiz-primary);"></i>
                    </div>
                </div>
            </div>

                    <p class="lead text-muted mb-5 border-start border-4 border-warning ps-4 py-2">
                        <?= nl2br(htmlspecialchars($quiz['description_fr'] ?? 'Accrochez-vous, ce quiz va tester vos connaissances !')) ?>
                    </p>

                    <div class="row g-4 mb-5">
                        <div class="col-6 col-md-3">
                            <div class="p-4 bg-light rounded-4 text-center h-100 transition-hover border border-white">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">Difficulté</h6>
                                <p class="h5 mb-0 fw-bold">
                                    <?php
                                    $badgeClass = 'text-success';
                                    $diffLabel = 'Débutant';
                                    switch($quiz['difficulty_level']) {
                                        case 'intermediate': $badgeClass = 'text-info'; $diffLabel = 'Intermédiaire'; break;
                                        case 'advanced': $badgeClass = 'text-warning'; $diffLabel = 'Avancé'; break;
                                        case 'expert': $badgeClass = 'text-danger'; $diffLabel = 'Expert'; break;
                                    }
                                    echo "<span class='$badgeClass'>$diffLabel</span>";
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-4 bg-light rounded-4 text-center h-100 transition-hover border border-white">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">Questions</h6>
                                <p class="h5 mb-0 fw-bold"><?= $questionsCount ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-4 bg-light rounded-4 text-center h-100 transition-hover border border-white">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">Durée Est.</h6>
                                <p class="h5 mb-0 fw-bold text-primary"><?= $quiz['estimated_time'] ?? '5' ?> min</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-4 bg-light rounded-4 text-center h-100 transition-hover border border-white">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">À Obtenir</h6>
                                <p class="h5 mb-0 fw-bold text-success"><?= $quiz['passing_score'] ?? '70' ?>%</p>
                            </div>
                        </div>
                    </div>

                    <?php if ($bestResult): ?>
                        <div class="alert alert-success border-0 rounded-4 p-4 mb-5 d-flex align-items-center shadow-sm">
                            <div class="me-4 position-relative">
                                <div class="score-circle" style="width: 80px; height: 80px;">
                                    <svg viewBox="0 0 100 100" style="width: 80px; height: 80px;">
                                        <circle cx="50" cy="50" r="45" class="circle-bg"/>
                                        <circle cx="50" cy="50" r="45" class="circle-progress" 
                                                stroke-dasharray="<?= ($bestResult['percentage'] * 2.82) ?> 282"/>
                                    </svg>
                                    <div class="score-text">
                                        <span class="percentage" style="font-size: 1rem;"><?= round($bestResult['percentage']) ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Votre meilleur score</h5>
                                <p class="mb-0 text-muted small">
                                    Obtenu le <?= date('d/m/Y', strtotime($bestResult['completed_at'])) ?>
                                    en <?= $bestResult['time_taken_seconds'] ?> secondes.
                                </p>
                            </div>
                            <div class="ms-auto">
                                <a href="<?= BASE_URL ?>/leaderboard/<?= $quiz['id'] ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                    Classement <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mt-5">
                        <a href="<?= BASE_URL ?>/quiz/play/<?= $quiz['id'] ?>" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg quiz-pulse">
                            Commencer le Défi
                        </a>
                        <a href="<?= BASE_URL ?>/quizzes" class="btn btn-light btn-lg rounded-pill px-5 py-3 text-muted">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quiz-pulse {
    animation: quizPulse 2s infinite ease-in-out;
}
.transition-hover {
    transition: all 0.3s ease;
}
.transition-hover:hover {
    transform: translateY(-5px);
    background: #fff !important;
    border-color: var(--quiz-primary) !important;
}
</style>

            <!-- Leaderboard Preview -->
            <div class="text-center">
                <a href="<?= BASE_URL ?>/leaderboard/<?= $quiz['id'] ?>" class="text-muted text-decoration-none">
                    <i class="fas fa-trophy me-2 text-warning"></i>Voir le classement pour ce quiz
                </a>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
