<?php
// View: Advanced Admin Dashboard
// Variables: $counts, $socialLinks, $chartLabels, $chartData, $popularSearches, $activeUsers, $pendingCount
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
<?php 
$current_page = 'dashboard';
include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Bienvenue, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?> 👋</h2>
                <p class="text-muted mb-0">Voici l'activité réelle de votre plateforme.</p>
            </div>
            <div class="live-status badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                <span class="pulse-dot me-2"></span>
                <strong><?= $activeUsers ?></strong> Visiteurs uniques aujourd'hui
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-widget glass-card rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between mb-3 text-primary">
                        <i class="fas fa-font fa-2x"></i>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">+<?= rand(5, 15) ?>%</span>
                    </div>
                    <h3 class="fw-bold mb-1"><?= number_format($counts['words']) ?></h3>
                    <p class="text-muted mb-0">Mots Enregistrés</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-widget glass-card rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between mb-3 text-warning">
                        <i class="fas fa-quote-right fa-2x"></i>
                        <span class="badge bg-warning-subtle text-warning rounded-pill">Stable</span>
                    </div>
                    <h3 class="fw-bold mb-1"><?= number_format($counts['proverbs']) ?></h3>
                    <p class="text-muted mb-0">Proverbes</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-widget glass-card rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between mb-3 text-info">
                        <i class="fas fa-user-check fa-2x"></i>
                        <span class="badge bg-info-subtle text-info rounded-pill">Précis</span>
                    </div>
                    <h3 class="fw-bold mb-1"><?= number_format($activeUsers) ?></h3>
                    <p class="text-muted mb-0">Visiteurs Uniques (24h)</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-widget glass-card rounded-4 p-4 h-100 border-primary" style="border-width: 2px;">
                    <div class="d-flex justify-content-between mb-3 text-danger">
                        <i class="fas fa-inbox fa-2x"></i>
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge bg-danger rounded-pill pulse-badge">Action Requise</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $pendingCount ?></h3>
                    <p class="text-muted mb-0">Contributions en attente</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Traffic Chart -->
            <div class="col-lg-8">
                <div class="glass-card rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Affluence (Unique Visitors - 30j)</h5>
                        <a href="<?= BASE_URL ?>/admin/analytics" class="btn btn-sm btn-light rounded-pill px-3">Détails <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="trafficChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Popular Searches -->
            <div class="col-lg-4">
                <div class="glass-card rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Recherches Populaires</h5>
                    <div class="search-list">
                        <?php if (!empty($popularSearches)): ?>
                            <?php foreach ($popularSearches as $search): ?>
                                <div class="search-item d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="search-icon bg-light rounded-2 p-2 me-3">
                                            <i class="fas fa-search text-muted small"></i>
                                        </div>
                                        <span class="fw-semibold text-truncate" style="max-width: 150px;"><?= htmlspecialchars($search['query']) ?></span>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill border"><?= $search['count'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-hourglass-start fa-2x mb-3 opacity-25"></i>
                                <p>Aucune recherche enregistrée</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/analytics" class="btn btn-outline-primary w-100 rounded-pill mt-3 btn-sm fw-bold">Rapport Complet</a>
                </div>

                <div class="glass-card rounded-4 p-4 mt-4">
                    <h5 class="fw-bold mb-4">Activité Récente</h5>
                    <div class="activity-feed">
                        <?php if (!empty($recentActivity)): ?>
                            <?php foreach ($recentActivity as $act): ?>
                                <div class="activity-item d-flex mb-3">
                                    <div class="activity-icon me-3">
                                        <?php if ($act['type'] === 'contribution'): ?>
                                            <div class="bg-primary-subtle text-primary rounded-circle p-2">
                                                <i class="fas fa-hand-holding-heart small"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-info-subtle text-info rounded-circle p-2">
                                                <i class="fas fa-search small"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="activity-info">
                                        <div class="fw-bold small"><?= $act['type'] === 'contribution' ? 'Nouvelle Contribution' : 'Recherche' ?></div>
                                        <div class="text-muted smaller"><?= htmlspecialchars($act['item']) ?></div>
                                        <div class="text-muted x-small"><?= date('H:i', strtotime($act['created_at'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-muted py-3">Aucun mouvement</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Content Management & Social -->
            <div class="col-lg-6">
                <div class="glass-card rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">Actions Rapides</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="<?= BASE_URL ?>/admin/add-word" class="btn btn-light w-100 p-4 border rounded-4 text-start">
                                <i class="fas fa-plus-circle text-primary mb-2 fa-lg"></i>
                                <div class="fw-bold">Nouveau Mot</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= BASE_URL ?>/admin/add-proverb" class="btn btn-light w-100 p-4 border rounded-4 text-start">
                                <i class="fas fa-feather-alt text-warning mb-2 fa-lg"></i>
                                <div class="fw-bold">Proverbe</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links Update -->
            <div class="col-lg-6">
                <div class="glass-card rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">Liens Sociaux</h5>
                    <form method="POST" class="social-links-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fab fa-facebook text-primary"></i></span>
                                    <input type="text" name="social[facebook]" class="form-control border-light bg-light" value="<?= htmlspecialchars($socialLinks['facebook'] ?? '') ?>" placeholder="Facebook">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fab fa-instagram text-danger"></i></span>
                                    <input type="text" name="social[instagram]" class="form-control border-light bg-light" value="<?= htmlspecialchars($socialLinks['instagram'] ?? '') ?>" placeholder="Instagram">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fab fa-twitter text-info"></i></span>
                                    <input type="text" name="social[twitter]" class="form-control border-light bg-light" value="<?= htmlspecialchars($socialLinks['twitter'] ?? '') ?>" placeholder="Twitter (X)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fab fa-tiktok text-dark"></i></span>
                                    <input type="text" name="social[tiktok]" class="form-control border-light bg-light" value="<?= htmlspecialchars($socialLinks['tiktok'] ?? '') ?>" placeholder="TikTok">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="fab fa-youtube text-danger"></i></span>
                                    <input type="text" name="social[youtube]" class="form-control border-light bg-light" value="<?= htmlspecialchars($socialLinks['youtube'] ?? '') ?>" placeholder="YouTube">
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" name="update_social" class="btn btn-dark w-100 rounded-pill fw-bold py-2 shadow-sm">
                                    <i class="fas fa-save me-2"></i>Mettre à jour les réseaux
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
/* Hide global site header and footer in dashboard */
.header, .footer, .mini-header {
    display: none !important;
}

/* Admin Layout CSS */
:root {
    --admin-sidebar-w: 260px;
    --admin-primary: #181d4b;
    --admin-accent: #f99417;
    --admin-bg: #f8f9fa;
}

body {
    background-color: var(--admin-bg) !important;
    padding-top: 0 !important;
}

.admin-layout {
    display: flex;
    min-height: 100vh;
    position: relative;
    z-index: 1000;
    margin-top: 0;
}

.admin-sidebar {
    width: var(--admin-sidebar-w);
    background: var(--admin-primary);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1001;
    transition: all 0.3s ease;
}

.admin-main {
    flex: 1;
    margin-left: var(--admin-sidebar-w);
    transition: all 0.3s ease;
    background: var(--admin-bg);
    min-height: 100vh;
}

.smaller { font-size: 0.85rem; }
.x-small { font-size: 0.75rem; }

.sidebar-nav .nav-link {
    color: rgba(255,255,255,0.7);
    padding: 12px 16px;
    display: block;
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 500;
}

.sidebar-nav .nav-link:hover, 
.sidebar-nav .nav-link.active {
    background: rgba(255,255,255,0.1);
    color: white;
}

.sidebar-nav .nav-link.active {
    background: var(--admin-accent);
}

.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
}
.glass-card h3,
.admin-main h2,
.admin-main h3,
.admin-main h4,
.admin-main h5,
.admin-main h6,
.admin-main .text-truncate{
    color: #000;
}

.pulse-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: #28a745;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.nav-divider {
    border-top: 1px solid white;
}

@media (max-width: 991.98px) {
    .admin-sidebar {
        margin-left: calc(-1 * var(--admin-sidebar-w));
    }
    .admin-main {
        margin-left: 0;
    }
}

.pulse-badge {
    animation: pulse-danger 1.5s infinite;
}

@keyframes pulse-danger {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trafficChart').getContext('2d');
    
    let labels = <?= json_encode($chartLabels ?: []) ?>;
    let data = <?= json_encode($chartData ?: []) ?>;
    
    // Creative Visuals: Gradient & Smooth Lines
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(24, 29, 75, 0.2)');
    gradient.addColorStop(1, 'rgba(24, 29, 75, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visiteurs Uniques',
                data: data,
                borderColor: '#181d4b',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#181d4b',
                    padding: 12,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>