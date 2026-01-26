<?php
/**
 * View Partial: Quiz Teaser
 * Invites users to test their knowledge.
 */
?>
<div class="section quiz-teaser-section text-center p-4" style="background: linear-gradient(135deg, #1F2A44, #2E3A5F); color: white; border: none;">
    <div class="quiz-icon mb-3">
        <div class="icon-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;">
            <i class="fas fa-gamepad fa-2x text-warning"></i>
        </div>
    </div>
    
    <h2 class="h5 fw-bold mb-2 text-white"><?= __('Testez vos connaissances !') ?></h2>
    <p class="small text-white-50 mb-4"><?= __('Découvrez notre quiz interactif pour apprendre le Tamazight en vous amusant.') ?></p>
    
    <a href="<?= BASE_URL ?>/quiz" class="btn rounded-pill px-4 py-2 fw-bold w-100 mb-2" style="background-color: #f09914; color: #FFF;">
        <?= __('Commencer le Quiz') ?>
    </a>
    <a href="<?= BASE_URL ?>/quiz/leaderboard" class="btn btn-link text-white-50 text-decoration-none small">
        <i class="fas fa-trophy me-1"></i><?= __('Classement') ?>
    </a>
</div>
