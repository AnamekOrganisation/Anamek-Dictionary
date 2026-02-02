<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<main class="main contact-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="contact-card premium-card rounded-4 shadow-sm overflow-hidden bg-white border-0">
                    <div class="row g-0">
                        <!-- Contact Info Sidebar -->
                        <div class="col-md-4 contact-sidebar p-4 p-lg-5 text-white d-flex flex-column h-100" style="background: linear-gradient(135deg, #181d4b 0%, #2a3b8f 100%);">
                            <div class="mb-5">
                                <h2 class="fw-bold mb-3"><?= __('Contactez-nous') ?></h2>
                                <p class="opacity-75"><?= __('Une question ? Un signalement ? Nous sommes à votre écoute.') ?></p>
                            </div>

                            <div class="contact-info-list mt-auto">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="info-icon me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <div class="small opacity-50"><?= __('Email') ?></div>
                                        <div class="fw-medium">contact@anamek.com</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <div class="info-icon me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <div class="small opacity-50"><?= __('Localisation') ?></div>
                                        <div class="fw-medium">Tamazight, Maroc</div>
                                    </div>
                                </div>

                                <div class="social-links mt-5 d-flex gap-3">
                                    <a href="#" class="text-white opacity-75 hover-opacity-100 fs-5"><i class="fab fa-facebook"></i></a>
                                    <a href="#" class="text-white opacity-75 hover-opacity-100 fs-5"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="text-white opacity-75 hover-opacity-100 fs-5"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="col-md-8 p-4 p-lg-5">
                            <?php if (isset($_SESSION['flash_message'])): ?>
                                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['flash_error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <h3 class="fw-bold text-dark mb-4"><?= __('Envoyez-nous un message') ?></h3>
                            
                            <form action="<?= BASE_URL ?>/contact" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label small fw-bold text-secondary"><?= __('Nom complet') ?> *</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0 px-3" id="name" name="name" placeholder="John Doe" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-bold text-secondary"><?= __('Email') ?> *</label>
                                        <input type="email" class="form-control form-control-lg bg-light border-0 px-3" id="email" name="email" placeholder="john@example.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label small fw-bold text-secondary"><?= __('Sujet') ?></label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0 px-3" id="subject" name="subject" placeholder="Question sur une traduction...">
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label small fw-bold text-secondary"><?= __('Message') ?> *</label>
                                        <textarea class="form-control bg-light border-0 px-3" id="message" name="message" rows="5" placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                                    </div>
                                    <div class="col-12 pt-3">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm hover-up w-100 w-md-auto">
                                            <?= __('Envoyer le message') ?>
                                            <i class="fas fa-paper-plane ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.contact-page { background: #f8faff; min-height: 80vh; }
.hover-up { transition: transform 0.2s; }
.hover-up:hover { transform: translateY(-3px); }
.form-control:focus { background: #fff !important; box-shadow: 0 0 0 0.25rem rgba(24, 29, 75, 0.1); border-color: rgba(24, 29, 75, 0.2); }
.hover-opacity-100:hover { opacity: 1 !important; }
@media (max-width: 768px) {
    .contact-sidebar { border-radius: 1rem 1rem 0 0; }
}
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
