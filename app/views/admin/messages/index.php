<?php include ROOT_PATH . '/app/views/partials/dashboard-head.php'; ?>

<div class="admin-layout">
    <?php include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1 header-title">
                    <i class="fas fa-envelope-open-text text-primary me-2"></i>Messages Reçus
                </h2>
                <p class="text-secondary mb-0">Consultez et gérez les prises de contact des utilisateurs.</p>
            </div>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm" role="alert">
                <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="premium-card rounded-4 shadow-sm overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase fs-7 fw-bold">Statut</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold">Expéditeur</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold">Sujet</th>
                            <th class="py-3 text-secondary text-uppercase fs-7 fw-bold">Date</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase fs-7 fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Aucun message pour le moment.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="<?= !$msg['is_read'] ? 'fw-bold bg-light-soft' : '' ?>">
                                    <td class="ps-4">
                                        <?php if (!$msg['is_read']): ?>
                                            <span class="badge bg-primary rounded-pill px-3">Nouveau</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary rounded-pill px-3 border">Lu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-dark"><?= htmlspecialchars($msg['name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($msg['email']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;">
                                            <?= htmlspecialchars($msg['subject'] ?: '(Sans objet)') ?>
                                        </div>
                                    </td>
                                    <td class="small text-secondary">
                                        <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= BASE_URL ?>/admin/message/view/<?= $msg['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle action-btn" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="<?= BASE_URL ?>/admin/message/delete" class="d-inline" onsubmit="return confirm('Supprimer ce message ?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { border: 1px solid rgba(0,0,0,0.05); }
.fs-7 { font-size: 0.75rem; }
.action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
.bg-light-soft { background-color: rgba(24, 29, 75, 0.02); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
