<?php 
require_once ROOT_PATH . '/app/controllers/AuthController.php';
$csrf_token = AuthController::generateCsrfToken();
include ROOT_PATH . '/app/views/partials/dashboard-head.php'; 
?>

<div class="main-content bg-light py-5" style="border-radius: 30px 30px 0 0; margin-top: -30px; position: relative; z-index: 10;">
    <div class="container" style="max-width: 1000px;">
        <div class="row g-4">
            <!-- Left Column: User Card & Navigation -->
            <div class="col-lg-4">
                <div class="profile-sidebar bg-white p-4 shadow-sm h-100" style="border-radius: 20px; border: 2px solid var(--lex-border);">
                    <div class="text-center mb-4">
                        <div class="avatar-container mb-3 position-relative d-inline-block">
                            <div class="avatar-circle rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 700;">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                            <span class="status-indicator bg-success border border-white border-3 position-absolute bottom-0 end-0 rounded-circle" style="width: 20px; height: 20px;"></span>
                        </div>
                        <h2 class="h4 fw-bold mb-1"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h2>
                        <p class="text-muted small">@<?= htmlspecialchars($user['username']) ?></p>
                        <div class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill mb-2" style="background: rgba(var(--bs-primary-rgb), 0.1);">
                            <i class="fas fa-crown me-1 small"></i> <?= ucfirst($user['user_type']) ?>
                        </div>
                    </div>

                    <div class="nav flex-column nav-pills mt-4" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start mb-2 py-3 px-4 rounded-4" id="v-pills-info-tab" data-bs-toggle="pill" data-bs-target="#v-pills-info" type="button" role="tab" aria-selected="true">
                            <i class="fas fa-user-circle me-2"></i> <?= __('Informations personnelles') ?>
                        </button>
                        <button class="nav-link text-start mb-2 py-3 px-4 rounded-4" id="v-pills-activity-tab" data-bs-toggle="pill" data-bs-target="#v-pills-activity" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-chart-line me-2"></i> <?= __('Activités & Badges') ?>
                        </button>
                        <button class="nav-link text-start mb-2 py-3 px-4 rounded-4 text-danger" onclick="location.href='<?= BASE_URL ?>/logout'">
                            <i class="fas fa-sign-out-alt me-2"></i> <?= __('Déconnexion') ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tab Content -->
            <div class="col-lg-8">
                <div class="tab-content border-0" id="v-pills-tabContent">
                    <!-- Info Tab -->
                    <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel">
                        <div class="profile-card bg-white p-4 p-md-5 shadow-sm" style="border-radius: 20px; border: 2px solid var(--lex-border);">
                            <h3 class="h4 fw-bold mb-4 d-flex align-items-center">
                                <?= __('Modifier le profil') ?>
                            </h3>

                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                    <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
                                    <?php unset($_SESSION['success']); ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= BASE_URL ?>/user/profile" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><?= __('Nom complet') ?></label>
                                        <input type="text" name="full_name" class="form-control form-control-lg border-2" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Ex: Karim Amazigh">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><?= __('Nom d\'utilisateur') ?></label>
                                        <input type="text" class="form-control form-control-lg border-2 bg-light" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                                        <div class="form-text small"><?= __('Le nom d\'utilisateur ne peut pas être modifié.') ?></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold"><?= __('E-mail') ?></label>
                                        <div class="input-group">
                                            <input type="email" class="form-control form-control-lg border-2 bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                            <span class="input-group-text border-2 bg-light">
                                                <i class="fas <?= $user['email_verified'] ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning' ?>"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold"><?= __('Bio / À propos') ?></label>
                                        <textarea name="bio" class="form-control form-control-lg border-2" rows="4" placeholder="<?= __('Dites-nous en un peu plus sur vous...') ?>"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-top">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-sm fw-bold">
                                        <?= __('Sauvegarder les modifications') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Activity Tab -->
                    <div class="tab-pane fade" id="v-pills-activity" role="tabpanel">
                        <div class="activity-card bg-white p-4 p-md-5 shadow-sm" style="border-radius: 20px; border: 2px solid var(--lex-border);">
                            <h3 class="h4 fw-bold mb-4"><?= __('Éstatistiques d\'activité') ?></h3>
                            
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <div class="stat-box p-4 rounded-4 text-center h-100" style="background: #f8faff; border: 1.5px solid #e1e7f5;">
                                        <div class="h1 fw-bold text-primary mb-1"><?= $stats['contribution_points'] ?></div>
                                        <div class="text-muted small fw-bold text-uppercase"><?= __('Points de Savoir') ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-box p-4 rounded-4 text-center h-100" style="background: #fff9f2; border: 1.5px solid #f9f0e1;">
                                        <div class="h1 fw-bold text-warning mb-1"><?= $stats['approved_contributions'] ?></div>
                                        <div class="text-muted small fw-bold text-uppercase"><?= __('Contributions validées') ?></div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="h6 fw-bold text-muted text-uppercase mb-4"><?= __('Succès & Badges') ?></h4>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="badge-item p-3 text-center rounded-4 border-2" style="width: 100px; background: #fff; border: 2px solid var(--lex-border);">
                                    <div class="h2 mb-1">🌱</div>
                                    <div class="small fw-bold">Novice</div>
                                </div>
                                <?php if ($stats['approved_contributions'] >= 10): ?>
                                <div class="badge-item p-3 text-center rounded-4 border-2" style="width: 100px; background: #fff; border: 2px solid var(--lex-accent);">
                                    <div class="h2 mb-1">📚</div>
                                    <div class="small fw-bold">Érudit</div>
                                </div>
                                <?php endif; ?>
                                <div class="badge-item p-3 text-center rounded-4 border-2 opacity-50 bg-light" style="width: 100px; border: 2px dashed #ccc;">
                                    <div class="h2 mb-1">🛡️</div>
                                    <div class="small fw-bold">Modérateur?</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-pills .nav-link {
    color: var(--lex-text);
    font-weight: 500;
    border: 1.5px solid transparent;
}
.nav-pills .nav-link:hover {
    background: #f1f5f9;
}
.nav-pills .nav-link.active {
    background: var(--lex-primary) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.form-control:focus {
    border-color: var(--lex-accent);
    box-shadow: 0 0 0 4px rgba(var(--lex-accent-rgb, 179, 134, 0), 0.1);
}
.profile-sidebar {
    position: sticky;
    top: 20px;
}
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
