<?php
// View: Redesigned Proverbs Management
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php 
    $current_page = 'proverbs';
    include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
    ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h2 class="fw-bold mb-1">Gestion des Proverbes</h2>
                <p class="text-muted">Gérez la sagesse populaire amazighe.</p>
                <a href="<?= BASE_URL ?>/admin/add-proverb" class="btn btn-warning rounded-pill px-4 mt-2">
                    <i class="fas fa-plus me-2"></i>Nouveau Proverbe
                </a>
            </div>
            <div class="search-box glass-card p-2 rounded-pill d-flex align-items-center bg-white border" style="width: 100%; max-width: 400px;">
                <form method="post" class="d-flex w-100">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="text" name="search_query" class="form-control border-0 bg-transparent shadow-none" placeholder="Rechercher un proverbe..." required>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info rounded-4 border-0 shadow-sm mb-4"><?= $message ?></div>
        <?php endif; ?>

        <?php if (!empty($results) && empty($proverb)): ?>
            <div class="glass-card rounded-4 overflow-hidden shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Proverbe (Tifinagh)</th>
                                <th>Traduction</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $p): ?>
                                <tr>
                                    <td class="ps-4 font-tifinagh"><?= htmlspecialchars($p['proverb_tfng']) ?></td>
                                    <td><?= htmlspecialchars($p['translation_fr']) ?></td>
                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>/admin/edit-proverb?id=<?= $p['id'] ?>" class="btn btn-sm btn-light rounded-pill px-3">
                                            <i class="fas fa-edit me-1 text-warning"></i> Modifier
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($proverb)): ?>
            <div class="glass-card rounded-4 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h4 class="fw-bold mb-0">Modifier le Proverbe #<?= $proverb['id'] ?></h4>
                    <a href="<?= BASE_URL ?>/admin/proverbs" class="btn btn-sm btn-outline-dark rounded-pill">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
                
                <form method="post" class="row g-4">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($proverb['id']) ?>">

                    <div class="col-12">
                        <label class="small fw-bold text-muted mb-2">Proverbe (Tifinagh)</label>
                        <input type="text" name="proverb_tfng" class="form-control border-light bg-light rounded-3 font-tifinagh fs-5" value="<?= htmlspecialchars($proverb['proverb_tfng']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-2">Proverbe (Latin)</label>
                        <input type="text" name="proverb_lat" class="form-control border-light bg-light rounded-3 fw-bold" value="<?= htmlspecialchars($proverb['proverb_lat']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-2">Traduction (Français)</label>
                        <input type="text" name="translation_fr" class="form-control border-light bg-light rounded-3" value="<?= htmlspecialchars($proverb['translation_fr']) ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="small fw-bold text-muted mb-2">Explication / Contexte</label>
                        <textarea name="explanation" class="form-control border-light bg-light rounded-3" rows="4"><?= htmlspecialchars($proverb['explanation'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" name="update_proverb" class="btn btn-warning rounded-pill px-5 fw-bold py-2 shadow-sm">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.header, .footer { display: none !important; }
:root {
    --admin-sidebar-w: 260px;
    --admin-primary: #181d4b;
    --admin-accent: #f99417;
    --admin-bg: #f8f9fa;
}
body { background-color: var(--admin-bg) !important; padding-top: 0 !important; }
.admin-layout { display: flex; min-height: 100vh; position: relative; z-index: 1000; }
.admin-sidebar { width: var(--admin-sidebar-w); background: var(--admin-primary); position: fixed; top: 0; left: 0; height: 100vh; z-index: 1001; }
.admin-main { flex: 1; margin-left: var(--admin-sidebar-w); background: var(--admin-bg); min-height: 100vh; }
.glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07); }
.font-tifinagh { font-family: 'Noto Sans Tifinagh', sans-serif; }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>