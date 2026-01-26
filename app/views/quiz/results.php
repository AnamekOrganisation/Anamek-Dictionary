<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center quiz-animate-up">
        <div class="col-lg-10">
            <!-- Results Header: Academic Scorecard -->
            <div class="quiz-question-container mb-5 text-center">
                <div class="score-circle mb-4">
                    <svg viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" class="circle-bg"/>
                        <circle cx="50" cy="50" r="45" class="circle-progress" 
                                id="result-stroke"
                                stroke-dasharray="0 282"/>
                    </svg>
                    <div class="score-text">
                        <span class="percentage" id="score-percentage">0%</span>
                        <span class="text-muted small fw-bold">SCORE</span>
                    </div>
                </div>
                
                <h1 class="fw-bold mb-2" style="color: var(--quiz-primary);">Évaluation Terminée</h1>
                <p class="lead text-muted mb-4"><?= htmlspecialchars($result['quiz_title']) ?></p>
                
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <span class="text-muted small d-block mb-1">CORRECT</span>
                            <span class="h4 fw-bold text-success mb-0"><?= $result['score'] ?>/<?= $result['total_questions'] ?></span>
                        </div>
                    </div>
                    <div class="col-6 col_md-3">
                        <div class="stat-item">
                            <span class="text-muted small d-block mb-1">DURÉE</span>
                            <span class="h4 fw-bold text-primary mb-0"><?= $result['time_taken_seconds'] ?>s</span>
                        </div>
                    </div>
                    <div class="col-6 col_md-3">
                        <div class="stat-item">
                            <span class="text-muted small d-block mb-1">POINTS</span>
                            <span class="h4 fw-bold text-warning mb-0">+<?= $result['score'] * 10 ?></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="<?= BASE_URL ?>/quiz/play/<?= $result['quiz_id'] ?>" class="btn btn-primary px-4 py-2 fw-bold">
                        <i class="fas fa-redo me-2"></i> Recommencer
                    </a>
                    <a href="<?= BASE_URL ?>/quizzes" class="btn btn-outline-secondary px-4 py-2">
                        Retour aux quiz
                    </a>
                </div>
            </div>

            <!-- Detailed Review -->
            <div class="px-2">
                <h3 class="fw-bold mb-4" style="color: var(--quiz-primary);">Révision de l'évaluation</h3>
                <div class="answers-review">
                    <?php foreach ($questions as $index => $question): ?>
                        <?php 
                            $qId = $question['id'];
                            $userAnswer = null;
                            $answerDetail = null;
                            
                            foreach ($result['answers'] as $detail) {
                                if ($detail['question_id'] == $qId) {
                                    $userAnswer = $detail['user_answer'];
                                    $answerDetail = $detail;
                                    break;
                                }
                            }
                            $isCorrect = $answerDetail['is_correct'] ?? false;
                        ?>
                        <div class="feedback-card <?= $isCorrect ? 'feedback-success' : 'feedback-danger' ?>">
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="h6 fw-bold mb-0">QUESTION <?= $index + 1 ?></h5>
                                    <span class="small fw-bold <?= $isCorrect ? 'text-success' : 'text-danger' ?>">
                                        <?= $isCorrect ? 'CORRECT' : 'INCORRECT' ?>
                                    </span>
                                </div>
                                <p class="fs-5 mb-4"><?= htmlspecialchars($question['question_text_fr']) ?></p>
                                
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <span class="text-muted small d-block mb-1">VOTRE RÉPONSE</span>
                                            <span class="fw-bold <?= $isCorrect ? 'text-success' : 'text-danger' ?>">
                                                <?= htmlspecialchars($userAnswer ?? 'N/A') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if (!$isCorrect): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-white rounded-3 border border-success">
                                            <span class="text-muted small d-block mb-1">RÉPONSE CORRECTE</span>
                                            <span class="fw-bold text-success">
                                                <?= htmlspecialchars($question['correct_answer']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetPercentage = <?= round($result['percentage']) ?>;
    const stroke = document.getElementById('result-stroke');
    const text = document.getElementById('score-percentage');
    
    let current = 0;
    const duration = 1500; // 1.5s for smoother feel
    const startTime = Date.now();
    
    function animate() {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function (easeOutQuart)
        const easeProgress = 1 - Math.pow(1 - progress, 4);
        const percentage = Math.round(targetPercentage * easeProgress);
        
        text.textContent = percentage + '%';
        stroke.setAttribute('stroke-dasharray', `${(percentage * 2.82)} 282`);
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }
    
    setTimeout(animate, 500);

    // Track Results View
    if (window.trackEvent) {
        trackEvent('quiz_result_view', {
            'quiz_id': '<?= $result['quiz_id'] ?>',
            'score_percent': <?= round($result['percentage']) ?>,
            'passed': <?= $result['percentage'] >= ($quiz['passing_score'] ?? 70) ? 'true' : 'false' ?>
        });
    }
});
</script>

<style>
.extra-light { background-color: #f0fff4; }
.stat-item { transition: transform 0.3s ease; }
.stat-item:hover { transform: translateY(-5px); background: #fff !important; }
.pulse-badge { animation: quizPulse 2s infinite ease-in-out; }
.x-small { font-size: 0.65rem; }
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
