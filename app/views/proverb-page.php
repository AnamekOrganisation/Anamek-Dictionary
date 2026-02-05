<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>
<?php if (!empty($proverb)): ?>
    <?php 
    $breadcrumbs = [
        ['name' => __('Home'), 'url' => BASE_URL . '/'],
        ['name' => __('Proverbs'), 'url' => BASE_URL . '/proverbs'],
        ['name' => '#' . $proverb['id'], 'url' => BASE_URL . '/proverb/' . $proverb['id']]
    ];
    include_once BASE_PATH . '/app/views/partials/schema/breadcrumb.php';
    include_once BASE_PATH . '/app/views/partials/schema/proverb.php'; 
    ?>
<?php endif; ?>

<main class="proverb-page-content py-5 bg-light" style="min-height: 80vh;">
    <div class="container" style="max-width: 900px;">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-decoration-none text-muted"><?= __('Home') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/proverbs" class="text-decoration-none text-muted"><?= __('Proverbs') ?></a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">#<?= $proverb['id'] ?></li>
            </ol>
        </nav>

        <div class="proverb-card bg-white shadow-sm overflow-hidden" style="border-radius: 24px; border: 2px solid var(--lex-border);">
            <!-- Header Section -->
            <div class="proverb-header p-4 p-md-5 text-center border-bottom bg-white">
                <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill mb-4" style="background: rgba(var(--bs-primary-rgb), 0.05);">
                    <i class="fas fa-quote-left me-2 small"></i> <?= __('Sagesse Amazighe') ?>
                </div>
                
                <h1 class="proverb-tfng display-5 fw-bold mb-4" style="font-family: 'Noto Sans Tifinagh', serif; line-height: 1.6; color: var(--lex-primary);">
                    <?= htmlspecialchars($proverb['proverb_tfng']) ?>
                </h1>
                
                <div class="proverb-lat h3 text-muted fw-normal italic mb-0" style="font-family: var(--lex-font-serif); font-style: italic;">
                    "<?= htmlspecialchars($proverb['proverb_lat']) ?>"
                </div>
            </div>

            <!-- Content Section -->
            <div class="proverb-body p-4 p-md-5">
                <div class="row g-5">
                    <!-- Translation -->
                    <div class="col-md-6 border-end">
                        <h3 class="h6 text-uppercase fw-bold text-muted letter-spacing-1 mb-3">
                            <i class="fas fa-language me-2"></i> <?= __('Traduction') ?>
                        </h3>
                        <p class="h5 leading-relaxed text-dark">
                            <?= htmlspecialchars($proverb['translation_fr']) ?>
                        </p>
                    </div>

                    <!-- Explanation -->
                    <div class="col-md-6">
                        <h3 class="h6 text-uppercase fw-bold text-muted letter-spacing-1 mb-3">
                            <i class="fas fa-info-circle me-2"></i> <?= __('Explication') ?>
                        </h3>
                        <div class="text-secondary leading-relaxed">
                            <?= nl2br(htmlspecialchars($proverb['explanation'] ?: __('Aucune explication disponible pour ce proverbe.'))) ?>
                        </div>
                    </div>
                </div>

                <!-- Footer / Actions -->
                <div class="mt-5 pt-5 border-top d-flex justify-content-between align-items-center">
                    <div class="proverb-meta text-muted small">
                        <span class="me-3"><i class="far fa-calendar-alt me-1"></i> <?= date('F Y') ?></span>
                        <span><i class="far fa-eye me-1"></i> <?= rand(100, 500) ?> <?= __('vues') ?></span>
                    </div>
                    
                    <div class="proverb-actions">
                        <button class="btn btn-outline-primary rounded-pill px-4 me-2" onclick="copyProverb(this)">
                            <i class="far fa-copy me-2"></i> <?= __('Copier') ?>
                        </button>
                        <button class="btn btn-primary rounded-pill px-4" onclick="shareProverb(this)">
                            <i class="fas fa-share-alt me-2"></i> <?= __('Partager') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / More Section (below for mobile/compact feel) -->
        <div class="mt-5">
            <h4 class="h5 fw-bold mb-4 d-flex align-items-center">
                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                    <i class="fas fa-lightbulb"></i>
                </span>
                <?= __('Mot mis en avant') ?>
            </h4>
            <?php if ($featuredWord): ?>
                <a href="<?= BASE_URL ?>/word/<?= urlencode($featuredWord['word_lat']) ?>-<?= $featuredWord['id'] ?>" class="text-decoration-none">
                    <div class="featured-word-card p-4 bg-white shadow-sm border-2" style="border-radius: 16px; border: 2px solid var(--lex-border); transition: all 0.3s ease;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold text-primary mb-1"><?= $featuredWord['word_tfng'] ?></h5>
                                <div class="text-muted small"><?= $featuredWord['word_lat'] ?></div>
                            </div>
                            <span class="badge bg-light text-dark border"><?= $featuredWord['part_of_speech'] ?></span>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.proverb-page-content .breadcrumb-item + .breadcrumb-item::before {
    content: "→";
    font-size: 0.8rem;
    color: #ccc;
}
.leading-relaxed {
    line-height: 1.8;
}
.featured-word-card:hover {
    border-color: var(--lex-accent) !important;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
}
.proverb-tfng {
    text-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
@media (max-width: 768px) {
    .proverb-body .row > div:first-child {
        border-right: none !important;
        border-bottom: 2px solid var(--lex-border);
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }
}
</style>



<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
