<header class="main-header">
    <div class="top-bar">
        <div class="container flex-row">
            <div class="logo">
                <a href="<?= BASE_URL ?>/">
                    <img src="<?= BASE_URL ?>/public/img/logo.webp" alt="anamek - dictionnaire amazigh">
                </a>
            </div>
            
            <nav class="main-nav">
                <div class="hamburger-icon mobile" id="icon">
                    <span class="icon-1 a" id="a"></span>
                    <span class="icon-2 c" id="b"></span>
                    <span class="icon-3 b" id="c"></span>
                </div>
                <div class="nav-menu">
                    <a href="<?= BASE_URL ?>/"><?= __('Home') ?></a>
                    <a href="<?= BASE_URL ?>/proverbs"><?= __('Proverb') ?></a>
                    <a href="<?= BASE_URL ?>/quizzes"><?= __('Quiz') ?></a>
                    <a href="<?= BASE_URL ?>/about"><?= __('About') ?></a>
                    <span class="menu_close"></span>
                </div>
            </nav>
            
            <div class="secondary-brand">
                <?php
                switch ($currentLang) {
                    case 'fr_FR': $langLabel = 'FR'; break;
                    case 'zgh_Latn': $langLabel = 'TZM'; break;
                    case 'zgh':
                    default: $langLabel = 'ⵜⵣⵎ'; break;
                }
                ?>
                <div class="language-container">
                    <div class="language"><?= $langLabel ?></div>
                    <div class="language-dropdown" id="languageDropdown">
                    <?php
                    // Robustly determine current URI and parameters
                    $currentUri = strtok($_SERVER['REQUEST_URI'], '?');
                    $currentParams = $_GET;
                    unset($currentParams['lang']);
                    
                    // Filter out the path parameter and legacy 'action' (for clean URLs)
                    $cleanParams = [];
                    foreach ($currentParams as $k => $v) {
                        if ($k !== $currentUri && !empty($k) && $k !== 'action') {
                            $cleanParams[$k] = $v;
                        }
                    }

                    $buildLangUrl = function($lang) use ($currentUri, $cleanParams) {
                        $params = $cleanParams;
                        $params['lang'] = $lang;
                        return $currentUri . '?' . http_build_query($params);
                    };
                    ?>
                    <a href="<?= $buildLangUrl('fr_FR') ?>" class="language-option <?= $currentLang === 'fr_FR' ? 'active' : '' ?>">Français</a>
                    <a href="<?= $buildLangUrl('zgh_Latn') ?>" class="language-option <?= $currentLang === 'zgh_Latn' ? 'active' : '' ?>">Tamaziɣt</a>
                    <a href="<?= $buildLangUrl('ber_MA') ?>" class="language-option <?= $currentLang === 'ber_MA' ? 'active' : '' ?>">ⵜⴰⵎⴰⵣⵉⵖⵜ</a>
                </div>
            </div>
            
                <div class="account-container">
                    <div class="account">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="9" r="3" stroke="currentColor" stroke-width="1.5"></circle>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"></circle>
                            <path d="M17.9691 20C17.81 17.1085 16.9247 15 11.9999 15C7.07521 15 6.18991 17.1085 6.03076 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <div class="account-dropdown" id="accountDropdown">
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                            <div class="dropdown-header">
                                <strong><?= e($user['full_name'] ?? 'Admin') ?></strong>
                            </div>
                            <hr>
                            <a href="<?= BASE_URL ?>/admin/dashboard"><?= __('Tableau de bord') ?></a>
                            <a href="<?= BASE_URL ?>/admin/settings"><?= __('Configuration') ?></a>
                            <a href="<?= BASE_URL ?>/admin/reviews"><?= __('Révisions') ?></a>
                            <hr>
                            <a href="<?= BASE_URL ?>/logout" class="text-danger"><?= __('Déconnexion') ?></a>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown-header">
                                <strong><?= e($user['full_name'] ?? 'User') ?></strong>
                            </div>
                            <hr>
                            <a href="<?= BASE_URL ?>/user/dashboard"><?= __('Tableau de bord') ?></a>
                            <a href="<?= BASE_URL ?>/user/profile"><?= __('Mon Profil') ?></a>
                            <a href="<?= BASE_URL ?>/user/contributions"><?= __('Mes contributions') ?></a>
                            <hr>
                            <a href="<?= BASE_URL ?>/logout" class="text-danger"><?= __('Déconnexion') ?></a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/login"><?= __('Connexion') ?></a>
                            <a href="<?= BASE_URL ?>/register"><?= __('S\'inscrire') ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
