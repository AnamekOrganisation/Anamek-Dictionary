<?php include ROOT_PATH . '/app/views/partials/dashboard-head.php'; ?>

<div class="admin-layout">
    <?php include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="mb-5">
            <a href="<?= BASE_URL ?>/admin/messages" class="text-decoration-none text-primary mb-3 d-inline-block">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
            <h2 class="fw-bold mb-1 header-title"><?= htmlspecialchars($message['subject'] ?: 'Sans objet') ?></h2>
            <p class="text-secondary">Message de <?= htmlspecialchars($message['name']) ?> (<?= htmlspecialchars($message['email']) ?>)</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="premium-card rounded-4 shadow-sm p-4 p-lg-5 bg-white mb-4">
                    <div class="message-meta d-flex justify-content-between border-bottom pb-4 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user-circle fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?= htmlspecialchars($message['name']) ?></h5>
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="small text-decoration-none"><?= htmlspecialchars($message['email']) ?></a>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-secondary">Reçu le</div>
                            <div class="fw-medium"><?= date('d F Y', strtotime($message['created_at'])) ?></div>
                            <div class="small text-muted"><?= date('H:i', strtotime($message['created_at'])) ?></div>
                        </div>
                    </div>

                    <div class="message-body" style="white-space: pre-wrap; line-height: 1.8; color: #444; font-size: 1.05rem;">
                        <?= htmlspecialchars($message['message']) ?>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= htmlspecialchars($message['subject']) ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fas fa-reply me-2"></i>Répondre par email
                        </a>
                        <form method="POST" action="<?= BASE_URL ?>/admin/message/delete" class="d-inline ms-2" onsubmit="return confirm('Supprimer ce message ?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="id" value="<?= $message['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                                <i class="fas fa-trash-alt me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.header-title { color: #181d4b; letter-spacing: -0.5px; }
.premium-card { border: 1px solid rgba(0,0,0,0.05); }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
