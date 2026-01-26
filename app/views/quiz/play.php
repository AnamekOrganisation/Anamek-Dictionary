<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="container py-5">
    <div id="quiz-container" class="row justify-content-center">
        <div class="col-lg-8 quiz-animate-up">
            <!-- Quiz Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1" style="color: var(--quiz-primary);"><?= htmlspecialchars($quiz['title_fr']) ?></h1>
                    <p class="text-muted small mb-0">Testez vos connaissances linguistiques</p>
                </div>
                <div id="quiz-timer" class="text-muted small fw-bold">
                    <i class="far fa-clock me-1"></i> <span id="timer-display">00:00</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="quiz-progress-wrapper">
                <div id="quiz-progress" class="quiz-progress-bar" role="progressbar" style="width: 0%"></div>
            </div>

            <!-- Questions Area -->
            <div class="quiz-question-container mb-5">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="quiz-question" id="q-<?= $question['id'] ?>" data-index="<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                        <span class="question-number">Question <?= $index + 1 ?> sur <?= count($questions) ?></span>
                        <h2 class="question-text"><?= htmlspecialchars($question['question_text_fr']) ?></h2>
                        
                        <?php if ($question['question_text_tfng']): ?>
                            <div class="p-4 bg-light rounded-3 mb-4 text-center">
                                <p class="tifinagh h1 text-dark mb-0"><?= htmlspecialchars($question['question_text_tfng']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="quiz-options">
                            <?php 
                            $options = $question['options'];
                            if ($options && is_array($options)): 
                                foreach ($options as $key => $option):
                            ?>
                                <button type="button" class="quiz-option-btn option-btn" 
                                        data-question="<?= $question['id'] ?>" data-value="<?= htmlspecialchars($option) ?>">
                                    <span class="option-indicator"><?= chr(65 + $key) ?></span>
                                    <span class="option-text"><?= htmlspecialchars($option) ?></span>
                                </button>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Area -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" id="prev-btn" class="btn btn-outline-secondary px-4 py-2" disabled>
                    <i class="fas fa-arrow-left me-2"></i> Précédent
                </button>
                <div class="d-flex gap-2">
                    <button type="button" id="next-btn" class="btn btn-primary px-5 py-2 fw-bold" disabled>
                        Suivant <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <button type="button" id="submit-btn" class="btn btn-success px-5 py-2 fw-bold" style="display: none;">
                        Terminer <i class="fas fa-check ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="quiz-loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem; border-width: .3em;" role="status">
            <span class="visually-hidden">Calcul du score...</span>
        </div>
        <h3 class="fw-bold">Analyse de vos réponses...</h3>
        <p class="text-muted">Nous préparons vos résultats détaillés.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.quiz-question');
    const totalQuestions = <?= count($questions) ?>;
    let currentIndex = 0;
    let userAnswers = {};
    let startTime = Date.now();
    let timerInterval;

    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const submitBtn = document.getElementById('submit-btn');
    const progressBar = document.getElementById('quiz-progress');
    const timerDisplay = document.getElementById('timer-display');

    // Start Timer
    timerInterval = setInterval(updateTimer, 1000);

    // Track Quiz Start
    if (window.trackEvent) {
        trackEvent('quiz_start', {
            'quiz_id': '<?= $quiz['id'] ?>',
            'quiz_title': '<?= addslashes($quiz['title_fr']) ?>'
        });
    }

    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const mins = Math.floor(elapsed / 60).toString().padStart(2, '0');
        const secs = (elapsed % 60).toString().padStart(2, '0');
        timerDisplay.textContent = `${mins}:${secs}`;
    }

    function updateButtons() {
        prevBtn.disabled = (currentIndex === 0);
        
        const currentQId = questions[currentIndex].id.replace('q-', '');
        const hasAnswer = userAnswers[currentQId] !== undefined;

        if (currentIndex === totalQuestions - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
            submitBtn.disabled = !hasAnswer;
        } else {
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
            nextBtn.disabled = !hasAnswer;
        }

        // Update progress
        const progress = ((currentIndex + 1) / totalQuestions) * 100;
        progressBar.style.width = progress + '%';
    }

    // Handle Option Selection
    document.querySelectorAll('.option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const qId = this.dataset.question;
            const value = this.dataset.value;

            // Clear previous selection for this question
            document.querySelectorAll(`.option-btn[data-question="${qId}"]`).forEach(b => {
                b.classList.remove('selected');
            });

            // Set new selection
            this.classList.add('selected');
            
            userAnswers[qId] = value;
            updateButtons();
        });
    });

    nextBtn.addEventListener('click', () => {
        if (currentIndex < totalQuestions - 1) {
            questions[currentIndex].style.display = 'none';
            currentIndex++;
            questions[currentIndex].style.display = 'block';
            updateButtons();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            questions[currentIndex].style.display = 'none';
            currentIndex--;
            questions[currentIndex].style.display = 'block';
            updateButtons();
        }
    });

    submitBtn.addEventListener('click', () => {
        const timeTaken = Math.floor((Date.now() - startTime) / 1000);
        
        // Switch views
        document.getElementById('quiz-container').style.display = 'none';
        document.getElementById('quiz-loading').style.display = 'block';
        window.scrollTo(0, 0);
        clearInterval(timerInterval);

        // Submit via AJAX
        const formData = new FormData();
        formData.append('time_taken', timeTaken);
        for (let qId in userAnswers) {
            formData.append(`answers[${qId}]`, userAnswers[qId]);
        }

        fetch('<?= BASE_URL ?>/quiz/submit/<?= $quiz['id'] ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Track Quiz Completion
                if (window.trackEvent) {
                    trackEvent('quiz_complete', {
                        'quiz_id': '<?= $quiz['id'] ?>',
                        'score': data.score,
                        'percentage': data.percentage,
                        'time_taken': timeTaken
                    });
                }
                window.location.href = data.results_url;
            } else {
                alert('Une erreur est survenue lors de la soumission.');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur réseau.');
        });
    });
});
</script>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
