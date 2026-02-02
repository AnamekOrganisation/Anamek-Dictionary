<?php
// View: Admin Settings
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
<?php 
$current_page = 'settings';
include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Paramètres du Système</h2>
                <p class="text-muted">Configurez les options générales و التقنية للموقع.</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            
            <div class="row g-4">
                <?php foreach ($groupedSettings as $group => $items): ?>
                    <div class="col-12">
                        <div class="glass-card rounded-4 p-4">
                            <h5 class="fw-bold mb-4 text-primary text-uppercase small" style="letter-spacing: 1px;">
                                <i class="fas fa-folder-open me-2"></i><?= htmlspecialchars($group) ?>
                            </h5>
                            <div class="row g-3">
                                <?php foreach ($items as $s): ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small fw-bold mb-1 d-block">
                                                <?= htmlspecialchars($s['description']) ?>
                                                <code class="text-muted ms-2" style="font-size: 10px;">(<?= $s['setting_key'] ?>)</code>
                                            </label>
                                            <?php if ($s['setting_key'] === 'maintenance_mode' || $s['setting_key'] === 'allow_contributions' || $s['setting_key'] === 'google_ads_enabled'): ?>
                                                <select name="settings[<?= $s['setting_key'] ?>]" class="form-select border-light bg-light rounded-3">
                                                    <option value="1" <?= $s['setting_value'] == '1' ? 'selected' : '' ?>>Activé / Oui</option>
                                                    <option value="0" <?= $s['setting_value'] == '0' ? 'selected' : '' ?>>Désactivé / Non</option>
                                                </select>
                                            <?php else: ?>
                                                <input type="text" name="settings[<?= $s['setting_key'] ?>]" class="form-control border-light bg-light rounded-3" value="<?= htmlspecialchars($s['setting_value']) ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="col-12 text-end mt-4">
                    <button type="submit" name="update_settings" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                        <i class="fas fa-save me-2"></i>Enregistrer tout
                    </button>
                </div>
            </div>
        </form>
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
.sidebar-nav .nav-link { color: rgba(255,255,255,0.7); padding: 12px 16px; display: block; text-decoration: none; transition: all 0.2s; font-weight: 500; }
.sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
.sidebar-nav .nav-link.active { background: var(--admin-accent); }
.glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
