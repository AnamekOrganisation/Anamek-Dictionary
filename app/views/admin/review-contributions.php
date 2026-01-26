<?php
// View: Redesigned Review Contributions
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
<?php 
$current_page = 'reviews';
include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Centre de Révision</h2>
                <p class="text-muted">Analysez et approuvez les contributions de la communauté.</p>
            </div>
            <div class="stats badge bg-danger rounded-pill px-3 py-2">
                <?= count($pending) ?> Révisions en attente
            </div>
        </div>

        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert alert-info alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                <i class="fas fa-info-circle me-2"></i><?= $_SESSION['admin_message'] ?>
                <?php unset($_SESSION['admin_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($pending)): ?>
            <div class="text-center py-5 glass-card rounded-5 mt-5">
                <div class="display-1 mb-4">🎉</div>
                <h3 class="fw-bold text-dark">Tout est à jour !</h3>
                <p class="text-muted">Aucune contribution n'attend votre validation pour le moment.</p>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary rounded-pill px-4 mt-3">Retour au tableau de bord</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pending as $c): ?>
                    <div class="col-12">
                        <div class="glass-card rounded-4 p-4 border-0 shadow-sm">
                            <div class="row">
                                <div class="col-lg-8 border-end border-light">
                                    <div class="d-flex align-items-center mb-4">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 me-3"><?= strtoupper($c['contribution_type']) ?></span>
                                        <div class="text-muted small">
                                            Soumis par <strong class="text-dark"><?= htmlspecialchars($c['username']) ?></strong> 
                                            le <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <?php if ($c['contribution_type'] === 'word'): ?>

                                            <!-- Basic Info -->
                                            <div class="col-md-4">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Tifinagh</div>
                                                <div class="p-2 bg-light rounded-3 font-tifinagh fs-5"><?= htmlspecialchars($c['content_after']['word_tfng']) ?></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Latin</div>
                                                <div class="p-2 bg-light rounded-3 fw-bold"><?= htmlspecialchars($c['content_after']['word_lat']) ?></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Français</div>
                                                <div class="p-2 bg-light rounded-3"><?= htmlspecialchars($c['content_after']['translation_fr']) ?></div>
                                            </div>

                                            <div class="col-12"><hr class="my-2 border-light"></div>

                                            <!-- Grammar -->
                                            <div class="col-md-3">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Nature</div>
                                                <div class="p-1 px-2 border rounded-pill small bg-white d-inline-block">
                                                    <?= htmlspecialchars($c['content_after']['part_of_speech'] ?? 'N/A') ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Pluriel</div>
                                                <div class="small"><?= htmlspecialchars($c['content_after']['plural_tfng'] ?? '-') ?> / <?= htmlspecialchars($c['content_after']['plural_lat'] ?? '-') ?></div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Féminin</div>
                                                <div class="small"><?= htmlspecialchars($c['content_after']['feminine_tfng'] ?? '-') ?> / <?= htmlspecialchars($c['content_after']['feminine_lat'] ?? '-') ?></div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">État Annexé</div>
                                                <div class="small"><?= htmlspecialchars($c['content_after']['annexed_tfng'] ?? '-') ?> / <?= htmlspecialchars($c['content_after']['annexed_lat'] ?? '-') ?></div>
                                            </div>

                                            <div class="col-12"><hr class="my-2 border-light"></div>

                                            <!-- Definitions -->
                                            <?php if (!empty($c['content_after']['definition_tfng'])): ?>
                                                <div class="col-12 mb-2">
                                                    <div class="small fw-bold text-muted text-uppercase mb-1">Définition (Tifinagh)</div>
                                                    <div class="p-2 bg-light rounded-3 font-tifinagh"><?= htmlspecialchars($c['content_after']['definition_tfng']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($c['content_after']['definition_lat'])): ?>
                                                <div class="col-12">
                                                    <div class="small fw-bold text-muted text-uppercase mb-1">Définition (Latin/Fr)</div>
                                                    <div class="p-2 bg-light rounded-3"><?= htmlspecialchars($c['content_after']['definition_lat']) ?></div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Relations -->
                                            <?php if (!empty($c['content_after']['synonyms']) || !empty($c['content_after']['antonyms'])): ?>
                                                <div class="col-12 mt-3">
                                                     <div class="row g-3">
                                                        <?php if (!empty($c['content_after']['synonyms'])): ?>
                                                            <div class="col-md-6">
                                                                <div class="small fw-bold text-muted text-uppercase mb-1">Synonymes</div>
                                                                <?php foreach($c['content_after']['synonyms'] as $syn): ?>
                                                                    <span class="badge bg-success-subtle text-success me-1">
                                                                        <?= $syn['tfng'] ?> (<?= $syn['lat'] ?>)
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($c['content_after']['antonyms'])): ?>
                                                            <div class="col-md-6">
                                                                <div class="small fw-bold text-muted text-uppercase mb-1">Antonymes</div>
                                                                <?php foreach($c['content_after']['antonyms'] as $ant): ?>
                                                                    <span class="badge bg-danger-subtle text-danger me-1">
                                                                        <?= $ant['tfng'] ?> (<?= $ant['lat'] ?>)
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                     </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Examples -->
                                            <?php if (!empty($c['content_after']['example_tfng']) || !empty($c['content_after']['example_lat'])): ?>
                                                <div class="col-12 mt-3">
                                                    <div class="p-3 bg-light rounded-3 border-start border-4 border-warning">
                                                        <div class="small fw-bold text-warning text-uppercase mb-2">Exemple d'utilisation</div>
                                                        <?php if (!empty($c['content_after']['example_tfng'])): ?>
                                                            <div class="font-tifinagh fs-6 mb-1"><?= htmlspecialchars($c['content_after']['example_tfng']) ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($c['content_after']['example_lat'])): ?>
                                                            <div class="fst-italic text-muted"><?= htmlspecialchars($c['content_after']['example_lat']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($c['contribution_type'] === 'example'): ?>
                                            <div class="col-12">
                                                <div class="small fw-bold text-muted text-uppercase mb-1">Exemple Content</div>
                                                <div class="p-3 bg-light rounded-3">
                                                    <div class="font-tifinagh mb-2"><?= htmlspecialchars($c['content_after']['example_tfng']) ?></div>
                                                    <div class="text-muted italic mb-2"><?= htmlspecialchars($c['content_after']['example_lat']) ?></div>
                                                    <div class="fw-bold"><?= htmlspecialchars($c['content_after']['translation_fr']) ?></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 ps-lg-4">
                                    <form action="<?= BASE_URL ?>/admin/reviews" method="POST" class="h-100 d-flex flex-column">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        
                                        <div class="form-group mb-3">
                                            <label class="small fw-bold mb-2">Notes administratives</label>
                                            <textarea name="notes" class="form-control border-light bg-light rounded-3" rows="3" placeholder="Pourquoi validez-vous ou refusez-vous ?"></textarea>
                                        </div>
                                        
                                        <div class="mt-auto d-flex gap-2">
                                            <button type="submit" name="action" value="approve" class="btn btn-success flex-grow-1 rounded-pill fw-bold">
                                                <i class="fas fa-check me-2"></i>Approuver
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-outline-danger rounded-pill">
                                                <i class="fas fa-times me-2"></i>Rejeter
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
/* Sidebar and common styles (Keep consistent) */
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
.font-tifinagh { font-family: 'Noto Sans Tifinagh', sans-serif; }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
