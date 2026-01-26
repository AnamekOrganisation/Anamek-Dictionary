<?php
// View: Admin Users Management
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
<?php 
$current_page = 'users';
include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Gestion des Utilisateurs</h2>
                <p class="text-muted">Gérez les rôles et les accès des membres de la communauté.</p>
            </div>
            <div class="stats badge bg-primary rounded-pill px-3 py-2">
                <?= count($users) ?> Utilisateurs au total
            </div>
        </div>

        <?php if (isset($message) && $message): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="glass-card rounded-4 overflow-hidden border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Rôle Actuel</th>
                            <th>Points</th>
                            <th>Date d'inscription</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($u['username']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="role" class="form-select form-select-sm border-0 bg-light rounded-pill" onchange="this.form.submit()">
                                            <option value="regular" <?= $u['user_type'] == 'regular' ? 'selected' : '' ?>>Régulier</option>
                                            <option value="contributor" <?= $u['user_type'] == 'contributor' ? 'selected' : '' ?>>Contributeur</option>
                                            <option value="expert" <?= $u['user_type'] == 'expert' ? 'selected' : '' ?>>Expert</option>
                                            <option value="moderator" <?= $u['user_type'] == 'moderator' ? 'selected' : '' ?>>Modérateur</option>
                                            <option value="admin" <?= $u['user_type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <input type="hidden" name="change_role" value="1">
                                    </form>
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3">
                                        <?= $u['contribution_points'] ?> pts
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($u['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Suspendu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> rounded-pill px-3">
                                            <?= $u['is_active'] ? 'Désactiver' : 'Réactiver' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<style>
/* Same layout styles as dashboard.php */
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
.sidebar-nav .nav-link { color: rgba(255,255,255,0.7); padding: 12px 16px; display: block; text-decoration: none; transition: all 0.2; font-weight: 500; }
.sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
.sidebar-nav .nav-link.active { background: var(--admin-accent); }
.glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
