<?php
/**
 * Global Header Partial
 * This file delegates to sub-partials for better maintainability.
 */

// 1. Head Section: Metadata, SEO, Assets
include ROOT_PATH . '/app/views/partials/head.php';
?>

<body>

<?php 
// 2. Admin Mini-Header (Static for now, could be separate)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
    <div class="mini-header">
        <span><?= isset($_SESSION['admin_name']) ? e($_SESSION['admin_name']) : 'Admin' ?></span>
        <a href="<?= BASE_URL ?>/dashboard">Tableau de bord</a>
        <a href="<?= BASE_URL ?>/logout">Déconnexion</a>
    </div>
<?php endif; ?>

<?php
// 3. Main Navbar: Logo, Menus, Account
include ROOT_PATH . '/app/views/partials/navbar.php';

// 4. Hero Section: Context-aware Search & Headers
include ROOT_PATH . '/app/views/partials/hero-section.php';
?>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const ABSOLUTE_URL = '<?= absolute_url() ?>';
</script>