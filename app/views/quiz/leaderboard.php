<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center quiz-animate-up">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="quiz-question-container text-center mb-5">
                <div class="rank-pill rank-gold mb-4" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-trophy"></i>
                </div>
                <h1 class="fw-bold mb-2" style="color: var(--quiz-primary);">Classement des Experts</h1>
                <?php if ($quiz): ?>
                    <p class="lead text-muted"><?= htmlspecialchars($quiz['title_fr']) ?></p>
                <?php else: ?>
                    <p class="lead text-muted">Contributeurs et apprenants les plus actifs</p>
                <?php endif; ?>
            </div>

            <!-- Leaderboard Table -->
            <div class="leaderboard-container">
                <div class="table-responsive">
                    <table class="table table-hover leaderboard-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">RANG</th>
                                <th>UTILISATEUR</th>
                                <?php if (!$quiz): ?>
                                    <th>ÉVALUATION</th>
                                <?php endif; ?>
                                <th class="text-center">PRÉCISION</th>
                                <th class="text-center pe-4">POINTS GAGNÉS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leaderboard)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted italic">
                                        Aucun résultat pour le moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leaderboard as $index => $row): ?>
                                    <?php 
                                        $rank = $index + 1;
                                        $isMe = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']);
                                        $rankClass = '';
                                        if ($rank == 1) $rankClass = 'rank-gold';
                                        elseif ($rank == 2) $rankClass = 'rank-silver';
                                        elseif ($rank == 3) $rankClass = 'rank-bronze';
                                    ?>
                                    <tr class="<?= $isMe ? 'leaderboard-row-me' : '' ?>">
                                        <td class="ps-4">
                                            <div class="rank-pill <?= $rankClass ?>"><?= $rank ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="fw-bold fs-6">
                                                    <?= htmlspecialchars($row['username']) ?>
                                                    <?php if ($isMe): ?>
                                                        <span class="badge bg-primary ms-1" style="font-size: 0.6rem;">VOUS</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <?php if (!$quiz): ?>
                                            <td class="small text-muted"><?= htmlspecialchars($row['quiz_title']) ?></td>
                                        <?php endif; ?>
                                        <td class="text-center">
                                            <span class="fw-bold text-success"><?= round($row['percentage']) ?>%</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <span class="fw-bold" style="color: var(--quiz-primary);">+<?= number_format($row['score'] * 10) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="<?= BASE_URL ?>/quizzes" class="btn btn-outline-primary px-4 py-2">
                    <i class="fas fa-play me-2"></i> Nouveau défi
                </a>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
