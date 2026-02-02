<?php
// View logic handled by Controller
require_once 'partials/header.php';
?>

<div class="main-content bg-light py-5" style="position: relative; z-index: 10;">
    <div class="container">
       

        <div class="proverbs-grid">
            <?php if (!empty($proverbs)): ?>
                <?php foreach ($proverbs as $proverb): ?>
                    <div class="proverb-card">
                        <div class="proverb-quote-icon mb-3 opacity-25">
                            <i class="fas fa-quote-left fa-2x text-primary"></i>
                        </div>
                        <a href="<?= BASE_URL ?>/proverb/<?= $proverb['id'] ?>" class="text-decoration-none">
                            <div class="proverb-text word-display" 
                                 data-tfng="<?= htmlspecialchars($proverb['proverb_tfng']) ?>"
                                 data-lat="<?= htmlspecialchars($proverb['proverb_lat']) ?>">
                                <?= htmlspecialchars($proverb['proverb_tfng']) ?>
                            </div>
                            <!-- <div class="proverb-translation"-->
                                <?php //htmlspecialchars($proverb['translation_fr']) ?>
                            <!--div> -->
                        </a>
                        
                        <?php //if (!empty($proverb['explanation'])): ?>
                            <!-- <div class="proverb-explanation">
                                <button class="explanation-toggle" onclick="this.nextElementSibling.classList.toggle('hidden');">
                                    <i class="fas fa-info-circle"></i--> <?php //__('Explanation') ?> 
                                <!-- </button>
                                <div class="explanation-text hidden"> -->
                                    <?php //nl2br(htmlspecialchars($proverb['explanation'])) ?>
                                <!-- </div>
                            </div> -->
                        <?php //endif; ?>

                        <div class="proverb-actions-bar mt-auto pt-3 border-top">
                            <button class="share-btn btn-sm" onclick="shareProverb(this)">
                                <i class="fas fa-share-alt"></i>
                                <?= __('Share') ?>
                            </button>
                            <button class="copy-btn btn-sm" onclick="copyProverb(this)">
                                <i class="fas fa-copy"></i>
                                <?= __('Copy') ?>
                            </button>
                        </div>

                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                            <div class="mt-3 text-end pt-2 border-top">
                                <a href="<?= BASE_URL ?>/admin/edit-proverb?id=<?= $proverb['id'] ?>" class="btn btn-link btn-sm p-0 text-warning text-decoration-none">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="not-found-container col-12">
                    <div class="not-found-icon">🔍</div>
                    <h3 class="not-found-title">Aucun proverbe trouvé</h3>
                    <p class="not-found-text">Essayez avec d'autres mots clés.</p>
                    <a href="<?= BASE_URL ?>/proverbs" class="btn btn-link">Voir tous les proverbes</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination pagination-rounded justify-content-center">
                    <?php 
                    $queryParams = [];
                    if (!empty($query)) $queryParams['q'] = $query;
                    if ($perPage != 12) $queryParams['per_page'] = $perPage;
                    ?>
                    
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <?php $queryParams['page'] = $page - 1; ?>
                            <a class="page-link shadow-sm border-0 mx-1 rounded-3" href="<?= BASE_URL ?>/proverbs?<?= http_build_query($queryParams) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <?php $queryParams['page'] = $i; ?>
                            <a class="page-link shadow-sm border-0 mx-1 rounded-3 <?= $i == $page ? 'bg-primary text-white' : 'bg-light' ?>" href="<?= BASE_URL ?>/proverbs?<?= http_build_query($queryParams) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <?php $queryParams['page'] = $page + 1; ?>
                            <a class="page-link shadow-sm border-0 mx-1 rounded-3" href="<?= BASE_URL ?>/proverbs?<?= http_build_query($queryParams) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>