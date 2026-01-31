<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Setup Locale
$currentLang = $_SESSION['lang'] ?? DEFAULT_LOCALE;
$currentScript = $_SESSION['script'] ?? 'tfng';

setupLocale($currentLang);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" data-script="<?= $currentScript ?>">
<head>
  <!-- Basic -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Title -->
  <title><?= e($page_title ?? 'Anamek – Dictionnaire Amazigh Français') ?></title>

  <!-- Description -->
  <meta name="description" content="<?= e(
    $page_description ?? 'Anamek est un dictionnaire collaboratif amazigh–français dédié à la langue et à la culture amazighes.'
  ) ?>">

  <!-- Keywords (secondary SEO) -->
  <meta name="keywords" content="<?= e(
    $page_keywords ?? 'قاموس أمازيغي فرنسي, معجم أمازيغي, ترجمة أمازيغية, Tamazight, Amazigh Dictionary, Dictionnaire Amazigh Français, Berber language'
  ) ?>">

  <!-- Author -->
  <meta name="author" content="<?= e($author ?? 'Anamek') ?>">

  <!-- Canonical -->
  <link rel="canonical" href="<?= $canonical_url ?? (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
  ) ?>">

  <!-- Favicons -->
  <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/public/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/public/favicon/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/public/favicon/apple-touch-icon.png">

  <!-- Open Graph -->
  <meta property="og:title" content="<?= e($og_title ?? $page_title ?? 'Anamek') ?>">
  <meta property="og:description" content="<?= e($og_description ?? $page_description) ?>">
  <meta property="og:image" content="<?= $og_image ?? BASE_URL . '/public/img/og-default.jpg' ?>">
  <meta property="og:url" content="<?= $og_url ?? (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
  ) ?>">
  <meta property="og:type" content="website">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($twitter_title ?? $page_title) ?>">
  <meta name="twitter:description" content="<?= e($twitter_description ?? $page_description) ?>">
  <meta name="twitter:image" content="<?= $twitter_image ?? $og_image ?>">

  <!-- Styles -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/language-selector.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/quiz.css?v=<?= time() ?>">

  <!-- Performance / LCP Optimizations -->
  <link rel="preload" as="image" href="<?= BASE_URL ?>/public/img/bg.webp" fetchpriority="high">
  <link rel="preload" as="image" href="<?= BASE_URL ?>/public/img/wod.webp" fetchpriority="high">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Source+Serif+Pro:wght@400;600;700&family=Noto+Sans+Tifinagh&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Source+Serif+Pro:wght@400;600;700&family=Noto+Sans+Tifinagh&display=swap" rel="stylesheet"></noscript>

  <!-- Admin styles -->
  <?php
    $adminPages = ['login', 'dashboard', 'admin', 'edit-word', 'add-word', 'words', 'users'];
    $currentAction = $_GET['action'] ?? '';
    if (in_array($currentAction, $adminPages)) {
        echo '<link rel="stylesheet" href="' . BASE_URL . '/public/css/admin.css">';
    }
  ?>

  <!-- Google Analytics -->
  <?php if (!empty($_ENV['GA_ENABLED']) && $_ENV['GA_ENABLED'] === 'true' && !empty($_ENV['GA_TRACKING_ID'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $_ENV['GA_TRACKING_ID'] ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= $_ENV['GA_TRACKING_ID'] ?>', {
        user_id: '<?= $_SESSION['user_id'] ?? '' ?>',
        language: '<?= $currentLang ?? 'fr' ?>'
      });
    </script>
  <?php endif; ?>
</head>


<body>

<?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
    <div class="mini-header">
        <span><?= isset($_SESSION['admin_name']) ? e($_SESSION['admin_name']) : 'Admin' ?></span>
        <a href="<?= BASE_URL ?>/dashboard">Tableau de bord</a>
        <a href="<?= BASE_URL ?>/logout">Déconnexion</a>
    </div>
<?php endif; ?>

<header class="main-header">
    <div class="top-bar">
        <div class="container flex-row">
            <div class="logo">
                <a href="<?= BASE_URL ?>/">
                    <img src="<?= BASE_URL ?>/public/img/logo.png" alt="anamek - dictionnaire amazigh">
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
                                <strong><?= e($_SESSION['username'] ?? 'Admin') ?></strong>
                            </div>
                            <hr>
                            <a href="<?= BASE_URL ?>/dashboard"><?= __('Tableau de bord') ?></a>
                            <a href="<?= BASE_URL ?>/admin/settings"><?= __('Configuration') ?></a>
                            <a href="<?= BASE_URL ?>/admin/reviews"><?= __('Révisions') ?></a>
                            <hr>
                            <a href="<?= BASE_URL ?>/logout" class="text-danger"><?= __('Déconnexion') ?></a>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown-header">
                                <strong><?= e($_SESSION['username'] ?? 'User') ?></strong>
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
<?php
// Page Context Detection
$currentUri = $_SERVER['REQUEST_URI'];
$isProverbs = strpos($currentUri, '/proverbs') !== false || strpos($currentUri, '/proverb/') !== false;
$is404 = isset($page_title) && $page_title === '404 Not Found'; 
if (isset($is_404) && $is_404) {
    return; 
}

// Info pages detection
$isInfoPage = strpos($currentUri, '/about') !== false || strpos($currentUri, '/contact') !== false || strpos($currentUri, '/privacy') !== false;

$commonData = DictionaryController::getSharedData();
$totalWords = $commonData['wordCount'];
$totalProverbs = $commonData['proverbCount'];

$headerTitle = $isProverbs 
    ? __('Proverbs Dictionary') . ' <span id="proverb-count">' . number_format($totalProverbs) . '</span>' 
    : __('Le dictionnaire contient actuellement') . ' <span id="word-count">' . number_format($totalWords) . '</span> ' . __('mots') . ' ' . __('et') . ' <span id="proverb-count">' . number_format($totalProverbs) . '</span> ' . __('proverbes.');

// Search Placeholder
$searchPlaceholder = $isProverbs ? __('Search a proverb...') : __('Search a word or phrase');
?>
    <div class="hero-search" <?php if ($isInfoPage) echo 'style="display:none;"'; ?>>
        <div class="container">
            <?php if ($isProverbs): ?>
                <!-- Proverbs Specific Header -->
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold mb-2" style="font-size: 2.5rem; color: #fff;">
                        <span class="tifinagh d-block mb-2" style="font-family: 'Noto Sans Tifinagh', sans-serif;">ⵉⵏⵣⵉⵜⵏ</span>
                        <?= __('Amazigh Proverbs') ?>
                    </h1>
                    <p class="lead" style="color: rgba(255,255,255,0.8); font-size: 1.1rem;"><?= __('Discover ancestral wisdom through our proverbs.') ?></p>
                </div>

                <!-- Proverbs Search Bar -->
                <div class="row justify-content-center mb-5" style="margin-bottom: 0 !important;">
                    <div class="col-md-8 col-lg-8">
                        <form action="<?= BASE_URL ?>/proverbs" method="GET" class="search-form" style="width: 100%; display: flex; justify-content: center;">
                            <div class="search-bar-container search-box">
                                <input type="text" 
                                       name="q" 
                                       class="search-bar" 
                                       placeholder="<?= __('Search a proverb...') ?>" 
                                       value="<?= e($_GET['q'] ?? '') ?>"
                                       autocomplete="off"
                                       style="padding-left: 20px;">
                                
                                <?php if (!empty($_GET['q'])): ?>
                                    <a href="<?= BASE_URL ?>/proverbs" class="btn text-muted p-2" style="display: flex; align-items: center; color: #666; text-decoration: none;">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <button class="search-btn" type="submit">
                                    <svg aria-hidden="true" fill="none" height="19" version="1.1" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.04004 8.79391C2.04004 5.18401 5.02763 2.23297 8.74367 2.23297C12.4597 2.23297 15.4473 5.18401 15.4473 8.79391C15.4473 12.4038 12.4597 15.3549 8.74367 15.3549C5.02763 15.3549 2.04004 12.4038 2.04004 8.79391ZM8.74367 0.732971C4.22666 0.732971 0.540039 4.32838 0.540039 8.79391C0.540039 13.2595 4.22666 16.8549 8.74367 16.8549C10.4144 16.8549 11.9716 16.363 13.2706 15.5171C13.6981 15.2387 14.2697 15.2585 14.6339 15.6158L17.4752 18.4027C17.7668 18.6887 18.2338 18.6887 18.5254 18.4027V18.4027C18.8251 18.1087 18.8251 17.626 18.5254 17.332L15.725 14.5853C15.3514 14.2188 15.3296 13.6296 15.6192 13.1936C16.4587 11.9301 16.9473 10.4197 16.9473 8.79391C16.9473 4.32838 13.2607 0.732971 8.74367 0.732971Z" fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- Default Dictionary Header -->
                <h6><?= $headerTitle ?></h6>
                <form id="searchForm" style="width: 100%; display: flex; justify-content: center;">
                    <div class="search-bar-container search-box">
                        <div class="lang-dropdown">
                            <input type="hidden" name="lang" id="search-lang" value="ber">
                            <button type="button" class="lang-btn">
                                <span><span class="lang-text">ⵜⴰⵎⴰⵣⵉⵖⵜ</span></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="margin-left:6px;" viewBox="0 0 24 24">
                                    <polygon points="12 17.414 3.293 8.707 4.707 7.293 12 14.586 19.293 7.293 20.707 8.707 12 17.414"></polygon>
                                </svg>
                            </button>
                            <div class="lang-menu" style="display:none;">
                                <div class="lang-item" data-value="ber">
                                    <span>ⵜⴰⵎⴰⵣⵉⵖⵜ</span>
                                </div>
                                <div class="lang-item" data-value="fr">
                                    <span>Français</span>
                                </div>
                            </div>
                        </div>
                        <input type="text" id="search-input" class="search-bar" placeholder="<?= $searchPlaceholder ?>" aria-label="Search" autocomplete="off">
                        <button type="submit" class="search-btn" id="searchBtn">
                            <svg aria-hidden="true" fill="none" height="19" version="1.1" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.04004 8.79391C2.04004 5.18401 5.02763 2.23297 8.74367 2.23297C12.4597 2.23297 15.4473 5.18401 15.4473 8.79391C15.4473 12.4038 12.4597 15.3549 8.74367 15.3549C5.02763 15.3549 2.04004 12.4038 2.04004 8.79391ZM8.74367 0.732971C4.22666 0.732971 0.540039 4.32838 0.540039 8.79391C0.540039 13.2595 4.22666 16.8549 8.74367 16.8549C10.4144 16.8549 11.9716 16.363 13.2706 15.5171C13.6981 15.2387 14.2697 15.2585 14.6339 15.6158L17.4752 18.4027C17.7668 18.6887 18.2338 18.6887 18.5254 18.4027V18.4027C18.8251 18.1087 18.8251 17.626 18.5254 17.332L15.725 14.5853C15.3514 14.2188 15.3296 13.6296 15.6192 13.1936C16.4587 11.9301 16.9473 10.4197 16.9473 8.79391C16.9473 4.32838 13.2607 0.732971 8.74367 0.732971Z" fill="currentColor"></path>
                            </svg>
                        </button>
                        <div id="autocomplete-results" class="autocomplete-results suggestions" style="display:none;"></div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>
<script>
    const BASE_URL = '<?= BASE_URL ?>';
</script>