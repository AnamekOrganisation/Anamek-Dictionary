<?php 

// include ROOT_PATH . '/app/views/partials/dashboard-head.php'; 
include ROOT_PATH . '/app/views/partials/head.php';
include ROOT_PATH . '/app/views/partials/navbar.php';
?>

<div class="main-content bg-light py-5" style="position: relative; z-index: 10;">
    <div class="container" style="max-width: 1000px;">
        <div class="row g-4">
            <!-- Left Column: User Card & Navigation -->
            <div class="col-lg-4">
                <div class="profile-sidebar bg-white p-4 shadow-sm h-100" style="border-radius: 20px; border: 2px solid var(--lex-border);">
                    <div class="text-center mb-4">
                        <div class="avatar-container mb-3 position-relative d-inline-block">
                            <div class="avatar-circle rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 700;">
                                <?= strtoupper(substr($user['full_name'] ?: ($user['username'] ?: 'U'), 0, 1)) ?>
                            </div>
                            <span class="status-indicator bg-success border border-white border-3 position-absolute bottom-0 end-0 rounded-circle" style="width: 20px; height: 20px;"></span>
                        </div>
                        <h2 class="h4 fw-bold mb-1"><?= htmlspecialchars($user['full_name'] ?: __('Utilisateur')) ?></h2>
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
                                <?= csrf_field() ?>
                                
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold"><?= __('Nom complet') ?></label>
                                        <input type="text" name="full_name" class="form-control form-control-lg border-2" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Ex: Karim Amazigh">
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
                                        <div class="text-muted small fw-bold text-uppercase"><?= __('Contribution points') ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-box p-4 rounded-4 text-center h-100" style="background: #fff9f2; border: 1.5px solid #f9f0e1;">
                                        <div class="h1 fw-bold text-warning mb-1"><?= ($stats['approved_contributions'] > 0) ? $stats['approved_contributions'] : 0;?></div>
                                        <div class="text-muted small fw-bold text-uppercase"><?= __('approved contributions') ?></div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="h6 fw-bold text-muted text-uppercase mb-4"><?= __('Succès & Badges') ?></h4>
                            <div class="d-flex flex-wrap gap-4 mt-2">
                                <!-- Novice Badge -->
                                <div class="badge-token badge-novice">
                                    <div class="badge-icon-bg">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 21L12 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M12 14C12 14 15 11 18 11C21 11 21 15 18 16C15 17 12 16 12 16" stroke="currentColor" stroke-width="2" stroke-opacity="0.4"/>
                                            <path d="M12 14C12 14 9 11 6 11C3 11 3 15 6 16C9 17 12 16 12 16" stroke="currentColor" stroke-width="2" stroke-opacity="0.4"/>
                                            <path d="M12 10L14 7M12 10L10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <span class="badge-name">Novice</span>
                                    <div class="badge-tooltip">Bienvenue dans la communauté !</div>
                                </div>

                                <?php if ($stats['approved_contributions'] >= 10): ?>
                                <!-- Scholar Badge -->
                                <div class="badge-token badge-scholar">
                                    <div class="badge-icon-bg">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 6V18M12 6H19V18H12M12 6H5V18H12" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            <path d="M12 18C12 18 9 20 6 20C3 20 3 18 3 18V6" stroke="currentColor" stroke-width="2" stroke-opacity="0.3"/>
                                            <path d="M12 18C12 18 15 20 18 20C21 20 21 18 21 18V6" stroke="currentColor" stroke-width="2" stroke-opacity="0.3"/>
                                        </svg>
                                    </div>
                                    <span class="badge-name">Érudit</span>
                                    <div class="badge-tooltip">Contributeur régulier et passionné.</div>
                                </div>
                                <?php endif; ?>

                                <!-- Locked/Upcoming -->
                                <div class="badge-token locked">
                                    <div class="badge-icon-bg">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2" stroke-opacity="0.2"/>
                                            <path d="M8 11V7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7V11" stroke="currentColor" stroke-width="2" stroke-opacity="0.1"/>
                                        </svg>
                                    </div>
                                    <span class="badge-name">Elite</span>
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
:root {
    --badge-gold: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --badge-emerald: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --badge-locked: #f1f5f9;
}

.nav-pills .nav-link {
    color: #64748b;
    font-weight: 600;
    padding: 16px 24px;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}
.nav-pills .nav-link:hover {
    background: #f8fafc;
    color: #1e293b;
}
.nav-pills .nav-link.active {
    background: #1e293b !important;
    color: white !important;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

/* Premium Badge Tokens */
.badge-token {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    cursor: help;
}
.badge-icon-bg {
    width: 80px;
    height: 80px;
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.badge-name {
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-novice .badge-icon-bg { color: #10b981; }
.badge-scholar .badge-icon-bg { color: #f59e0b; }
.badge-token.locked { opacity: 0.4; color: #94a3b8; }

.badge-token:hover .badge-icon-bg {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.badge-novice:hover .badge-icon-bg { border-color: #10b981; background: #ecfdf5; }
.badge-scholar:hover .badge-icon-bg { border-color: #f59e0b; background: #fffbeb; }

/* Tooltip */
.badge-tooltip {
    position: absolute;
    bottom: 110%;
    left: 50%;
    transform: translateX(-50%) translateY(10px);
    background: #1e293b;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 100;
}
.badge-token:hover .badge-tooltip {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}

.profile-sidebar {
    position: sticky;
    top: 20px;
}
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
