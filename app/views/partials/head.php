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
