<?php
$page_title = __('404 - Page Non Trouvée');
$page_description = __('Désolé, la page que vous recherchez n\'existe pas.');
include ROOT_PATH . '/app/views/partials/header.php';
?>

<div class="main-content d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="container text-center">
        <div class="error-container py-5">
            <h1 class="display-1 fw-bold text-primary mb-0" style="font-size: 8rem; opacity: 0.1; position: absolute; left: 50%; transform: translateX(-50%); z-index: -1;">404</h1>
            <div class="error-content relative">
                <div class="mb-4">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="var(--accent-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search-x">
                        <circle cx="11" cy="11" r="8"/><path d="m16 16 5.6 5.6"/><path d="m7 15 8-8"/><path d="m15 15-8-8"/>
                    </svg>
                </div>
                <h2 class="h1 mb-4" style="color: var(--lex-primary);"><?= __('Oups! Page introuvable'); ?></h2>
                <p class="lead text-muted mb-5 mx-auto" style="max-width: 600px;">
                    <?= __('Il semble que le mot ou la page que vous cherchez n\'existe pas encore ou a été déplacé.'); ?>
                </p>

                <!-- 404 Search Bar -->
                <div class="row justify-content-center mb-5">
                    <div class="col-md-8 col-lg-6">
                        <form action="<?= BASE_URL ?>/search" method="GET" class="search-form">
                            <div class="search-bar-container shadow-sm border">
                                <input type="text" name="q" class="search-bar" placeholder="<?= __('Rechercher un mot...') ?>" autofocus>
                                <button class="search-btn" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= BASE_URL ?>/" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm">
                        <i class="fas fa-home me-2"></i> <?= __('Retour à l\'accueil'); ?>
                    </a>
                    <a href="<?= BASE_URL ?>/proverbs" class="btn btn-outline-secondary btn-lg px-4 rounded-pill shadow-sm">
                        <i class="fas fa-quote-left me-2"></i> <?= __('Voir les proverbes'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-container {
    position: relative;
    overflow: hidden;
}
.error-content {
    animation: fadeIn 0.8s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
