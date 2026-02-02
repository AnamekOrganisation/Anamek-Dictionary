<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<style>
    :root {
        --contact-primary: #6366f1;
        --contact-secondary: #a855f7;
        --contact-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    }

    .contact-page {
        background: #f8faff;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        padding: 100px 0;
        font-family: 'Outfit', 'Inter', sans-serif;
    }

    /* Floating Blobs */
    .blob {
        position: absolute;
        width: 500px;
        height: 500px;
        background: var(--contact-gradient);
        filter: blur(100px);
        opacity: 0.08;
        border-radius: 50%;
        z-index: 0;
        animation: float 25s infinite alternate;
    }
    .blob-1 { top: -150px; right: -100px; }
    .blob-2 { bottom: -150px; left: -100px; animation-delay: -7s; }

    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(60px, 40px) scale(1.1); }
    }

    .container { position: relative; z-index: 10; }

    .contact-header { text-align: center; margin-bottom: 60px; }
    .contact-header h2 { font-size: 42px; font-weight: 800; color: #1e293b; margin-bottom: 15px; letter-spacing: -1px; }
    .contact-header p { color: #64748b; font-size: 18px; max-width: 600px; margin: 0 auto; line-height: 1.6; }

    .premium-contact-form-card {
        background: white;
        border-radius: 35px;
        box-shadow: 0 40px 80px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        padding: 60px;
        position: relative;
    }
    /* Subtle decorative element */
    .premium-contact-form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: var(--contact-gradient);
        border-radius: 0 0 10px 10px;
    }

    .form-group-custom { margin-bottom: 30px; }
    .form-label-custom {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }
    .form-control-custom {
        width: 100%;
        padding: 18px 22px;
        background: #f8fafc;
        border: 2px solid transparent;
        border-radius: 20px;
        font-size: 16px;
        color: #1e293b;
        transition: all 0.3s ease;
    }
    .form-control-custom:focus {
        background: white;
        border-color: var(--contact-primary);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.08);
        outline: none;
    }

    .btn-submit-full {
        background: var(--contact-gradient);
        color: white;
        border: none;
        padding: 20px 40px;
        border-radius: 22px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.25);
        width: 100%;
        margin-top: 20px;
    }
    .btn-submit-full:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 30px 50px rgba(99, 102, 241, 0.35); 
        opacity: 0.95; 
    }
    .btn-submit-full i { transition: transform 0.3s ease; }
    .btn-submit-full:hover i { transform: translateX(8px) rotate(-15deg); }

    .alert-custom {
        border-radius: 24px;
        border: none;
        padding: 25px;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.04);
        animation: slideDown 0.5s ease-out;
    }
    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .alert-custom-success { background: #ecfdf5; color: #047857; border-left: 6px solid #10b981; }
    .alert-custom-error { background: #fef2f2; color: #b91c1c; border-left: 6px solid #ef4444; }

    @media (max-width: 768px) {
        .contact-page { padding: 60px 0; }
        .premium-contact-form-card { padding: 40px 25px; border-radius: 25px; }
        .contact-header h2 { font-size: 32px; }
    }
</style>

<main class="main contact-page">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="contact-header">
                    <h2><?= __('Contactez-nous') ?></h2>
                    <p><?= __('Une question ou une suggestion ? Notre équipe est à votre écoute.') ?></p>
                </div>

                <div class="premium-contact-form-card">
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert-custom alert-custom-success">
                            <i class="fas fa-check-circle fs-3"></i>
                            <div class="fw-bold fs-5"><?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['flash_error'])): ?>
                        <div class="alert-custom alert-custom-error">
                            <i class="fas fa-exclamation-triangle fs-3"></i>
                            <div class="fw-bold fs-5"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/contact" method="POST" novalidate>
                        <?= csrf_field() ?>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label for="name" class="form-label-custom"><?= __('Nom complet') ?> *</label>
                                    <input type="text" class="form-control-custom" id="name" name="name" placeholder="Ex: Karim Amazigh" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label for="email" class="form-label-custom"><?= __('Adresse Email') ?> *</label>
                                    <input type="email" class="form-control-custom" id="email" name="email" placeholder="k.amazigh@example.com" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-custom">
                                    <label for="subject" class="form-label-custom"><?= __('Sujet') ?></label>
                                    <input type="text" class="form-control-custom" id="subject" name="subject" placeholder="<?= __('De quoi souhaitez-vous discuter ?') ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-custom">
                                    <label for="message" class="form-label-custom"><?= __('Message') ?> *</label>
                                    <textarea class="form-control-custom" id="message" name="message" rows="6" placeholder="<?= __('Décrivez votre demande en détail...') ?>" required></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-submit-full">
                                    <span><?= __('Envoyer le message') ?></span>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
