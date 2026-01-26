<?php
// View: Redesigned Add Proverb
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php 
    $current_page = 'proverbs';
    include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
    ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Nouveau Proverbe</h2>
                <p class="text-muted">Partagez la sagesse des anciens.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/proverbs" class="btn btn-outline-dark rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $result ? 'alert-success' : 'alert-danger' ?> rounded-4 border-0 shadow-sm mb-4">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="glass-card rounded-4 p-4 shadow-sm">
            <form method="POST" class="row g-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="col-12">
                    <label class="small fw-bold text-muted mb-2">Proverbe (Tifinagh) <span class="text-danger">*</span></label>
                    <input type="text" name="proverb_tfng" class="form-control border-light bg-light rounded-3 font-tifinagh fs-5" required>
                </div>

                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-2">Proverbe (Latin) <span class="text-danger">*</span></label>
                    <input type="text" name="proverb_lat" class="form-control border-light bg-light rounded-3 fw-bold" required>
                </div>

                <div class="col-md-6">
                    <label class="small fw-bold text-muted mb-2">Traduction Française <span class="text-danger">*</span></label>
                    <input type="text" name="translation_fr" class="form-control border-light bg-light rounded-3" required>
                </div>

                <div class="col-12">
                    <label class="small fw-bold text-muted mb-2">Explication / Contexte</label>
                    <textarea name="explanation" class="form-control border-light bg-light rounded-3" rows="4"></textarea>
                </div>

                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold py-2 shadow-sm">
                        <i class="fas fa-plus me-2"></i>Ajouter le proverbe
                    </button>
                </div>
            </form>
        </div>
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