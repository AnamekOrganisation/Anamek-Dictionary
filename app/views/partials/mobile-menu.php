<div class="mobile-nav-panel">
    <div class="mobile-menu-header">
        <div class="logo">
            <a href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/public/img/logo.webp" alt="anamek - dictionnaire amazigh">
            </a>
        </div>
        <div class="menu-close-wrapper">
            <div class="menu_close"></div>
        </div>
    </div>
    
    <div class="nav-links">
        <a href="<?= BASE_URL ?>/">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <?= __('Home') ?>
        </a>
        <a href="<?= BASE_URL ?>/proverbs">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <?= __('Proverb') ?>
        </a>
        <a href="<?= BASE_URL ?>/quizzes">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <?= __('Quiz') ?>
        </a>
        <a href="<?= BASE_URL ?>/about">
            <svg class="menu-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <?= __('About') ?>
        </a>
    </div>

    <div class="mobile-menu-footer">
        <!-- <div class="social-links">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
        </div> -->
        <p class="copyright">&copy; <?= date('Y') ?> Anamek.org <br/> Dictionnaire Amazigh</p>
    </div>
</div>
<div class="menu-overlay"></div>
