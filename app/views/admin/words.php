<?php
// View: Admin Word List
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php 
    $current_page = 'words';
    include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
    ?>

    <main class="admin-main p-4 p-lg-5">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold mb-1 header-title">
                    <i class="fas fa-book text-primary me-2"></i>Liste des Mots
                </h2>
                <p class="text-secondary mb-0">Gérez le dictionnaire : ajoutez, modifiez ou recherchez des mots.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/add-word" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hover-up">
                <i class="fas fa-plus me-2"></i>Ajouter un mot
            </a>
        </div>

            <!-- Search & Filter Card -->
        <div class="premium-card rounded-4 shadow-sm p-4 mb-4">
            <?php if (!empty($message)): ?>
                <div class="mb-4"><?= $message ?></div>
            <?php endif; ?>

            <form method="GET" action="<?= BASE_URL ?>/admin/words" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="q" class="form-control form-control-lg border-start-0 bg-light" 
                               placeholder="Rechercher un mot (Tifinagh, Latin, Français)..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 h-100 rounded-3 fw-bold">
                        Rechercher
                    </button>
                </div>
            </form>
        </div>

        <!-- Words Table -->
        <div class="premium-card rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase fs-7 fw-bold" style="width: 5%;">ID</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold" style="width: 20%;">Tifinagh</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold" style="width: 20%;">Latin</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold" style="width: 25%;">Français</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold" style="width: 15%;">Type</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase fs-7 fw-bold" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($words)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Aucun mot trouvé.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($words as $word): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?= $word['id'] ?></td>
                                    <td class="font-tifinagh fs-5 text-dark"><?= htmlspecialchars($word['word_tfng']) ?></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($word['word_lat']) ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($word['translation_fr']) ?></td>
                                    <td>
                                        <?php if (!empty($word['part_of_speech'])): ?>
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($word['part_of_speech']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= BASE_URL ?>/admin/edit-word?id=<?= $word['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary rounded-circle action-btn" 
                                               title="Modifier">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <form method="POST" action="<?= BASE_URL ?>/admin/delete-word" class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce mot ? Cette action est irréversible.');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $word['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger rounded-circle action-btn" 
                                                        title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>

                                            <a href="<?= BASE_URL ?>/word/<?= $word['id'] ?>" 
                                               target="_blank"
                                               class="btn btn-sm btn-outline-secondary rounded-circle action-btn" 
                                               title="Voir">
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
            
            <!-- Pagination (Simple) -->
            <?php if (isset($total_pages) && $total_pages > 1): ?>
            <div class="px-4 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small">Page <?= $current_page_num ?> sur <?= $total_pages ?></span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($current_page_num > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 bg-transparent text-dark" href="?page=<?= $current_page_num - 1 ?>&q=<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($current_page_num < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link border-0 bg-transparent text-dark" href="?page=<?= $current_page_num + 1 ?>&q=<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { background: white; border: 1px solid rgba(0,0,0,0.05); }
.fs-7 { font-size: 0.75rem; }
.action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
.hover-up { transition: transform 0.2s; }
.hover-up:hover { transform: translateY(-2px); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
