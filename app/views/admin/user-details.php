<?php
// View: Admin User Details & Contributions
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
<?php 
$current_page = 'users';
include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
?>

    <main class="admin-main p-4 p-lg-5">
        <div class="mb-5">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/users" class="text-decoration-none text-muted">Utilisateurs</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($user['username']) ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1">Détails de l'utilisateur</h2>
            <p class="text-muted">Historique des contributions et informations de profil.</p>
        </div>

        <div class="row g-4">
            <!-- User Profile Card -->
            <div class="col-lg-4">
                <div class="glass-card rounded-4 p-4 shadow-sm sticky-top" style="top: 2rem;">
                    <div class="text-center mb-4">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['username']) ?></h4>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3"><?= ucfirst($user['user_type']) ?></span>
                    </div>

                    <div class="space-y-3">
                        <div class="d-flex justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted">Email :</span>
                            <span class="fw-medium"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted">Points :</span>
                            <span class="fw-bold text-warning"><?= $user['contribution_points'] ?> pts</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted">Inscrit le :</span>
                            <span><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted">Statut :</span>
                            <span class="badge <?= $user['is_active'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-pill">
                                <?= $user['is_active'] ? 'Actif' : 'Suspendu' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contributions Timeline/Table -->
            <div class="col-lg-8">
                <div class="glass-card rounded-4 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Historique des Modifications</h5>
                        <span class="badge bg-secondary rounded-pill"><?= count($contributions) ?> entrées</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Action</th>
                                    <th>Type</th>
                                    <th>Élément</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($contributions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                            <p>Aucune contribution trouvée pour cet utilisateur.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($contributions as $c): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php 
                                                $actionClass = 'bg-info-subtle text-info';
                                                $actionIcon = 'fa-plus';
                                                if ($c['action_type'] === 'update') {
                                                    $actionClass = 'bg-warning-subtle text-warning';
                                                    $actionIcon = 'fa-pen';
                                                }
                                                ?>
                                                <span class="badge <?= $actionClass ?> rounded-pill px-2">
                                                    <i class="fas <?= $actionIcon ?> me-1 small"></i> <?= ucfirst($c['action_type']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small fw-bold text-uppercase"><?= $c['contribution_type'] ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $content = $c['content_after'];
                                                if ($c['contribution_type'] === 'word') {
                                                    echo '<div class="fw-bold text-primary">' . htmlspecialchars($content['word_lat'] ?? '') . '</div>';
                                                    echo '<div class="small font-tifinagh text-dark">' . htmlspecialchars($content['word_tfng'] ?? '') . '</div>';
                                                } elseif ($c['contribution_type'] === 'proverb') {
                                                    echo '<div class="text-muted small text-truncate" style="max-width: 200px;">' . htmlspecialchars($content['proverb_fr'] ?? '') . '</div>';
                                                } else {
                                                    echo '<span class="text-muted">ID: ' . ($c['target_id'] ?: 'Nouveau') . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-muted small">
                                                <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $statusClass = 'bg-secondary-subtle text-secondary';
                                                if ($c['status'] === 'approved') $statusClass = 'bg-success-subtle text-success';
                                                if ($c['status'] === 'rejected') $statusClass = 'bg-danger-subtle text-danger';
                                                ?>
                                                <span class="badge <?= $statusClass ?> rounded-pill px-3">
                                                    <?= ucfirst($c['status'] ?? 'pending') ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContrib-<?= $c['id'] ?>">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="collapseContrib-<?= $c['id'] ?>">
                                            <td colspan="6" class="p-4 bg-light-subtle">
                                                <div class="row">
                                                    <div class="col-md-6 border-end">
                                                        <h6 class="fw-bold small text-muted text-uppercase mb-3">Données Soumises</h6>
                                                        <pre class="bg-dark text-light p-3 rounded-3 mb-0 small" style="max-height: 200px; overflow-y: auto;"><?= json_encode($c['content_after'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                                    </div>
                                                    <div class="col-md-6 ps-4">
                                                        <h6 class="fw-bold small text-muted text-uppercase mb-3">Notes de Révision</h6>
                                                        <div class="p-3 bg-white border rounded-3 mb-3">
                                                            <?= $c['review_notes'] ? htmlspecialchars($c['review_notes']) : '<span class="text-muted italic">Aucune note</span>' ?>
                                                        </div>
                                                        <div class="d-flex justify-content-between small">
                                                            <span class="text-muted">Points accordés : <span class="fw-bold text-dark"><?= $c['points_awarded'] ?? 0 ?></span></span>
                                                            <span class="text-muted">Par : <span class="fw-bold text-dark"><?= $c['reviewed_by'] ?: '-' ?></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
