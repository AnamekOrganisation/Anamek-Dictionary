<?php
// Partial: Admin Sidebar
// Expected variables: $current_page, $pendingCount (optional)
$pendingCount = $pendingCount ?? 0;
// Fetch unread messages count
$db = Database::getInstance()->getConnection();
$unreadMsgCount = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
?>
<aside class="admin-sidebar shadow">
    <div class="sidebar-header p-4">
        <h5 class="fw-bold mb-0 text-white"><i class="fas fa-shield-alt me-2"></i>Anamek Admin</h5>
    </div>
    <nav class="sidebar-nav px-3">
        <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link <?= $current_page == 'dashboard' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-th-large me-3"></i>Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/words" class="nav-link <?= $current_page == 'words' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-book me-3"></i>Gestion des Mots
        </a>
        <a href="<?= BASE_URL ?>/admin/proverbs" class="nav-link <?= $current_page == 'proverbs' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-quote-left me-3"></i>Gestion Proverbes
        </a>
        <a href="<?= BASE_URL ?>/admin/quizzes" class="nav-link <?= $current_page == 'quizzes' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-question-circle me-3"></i>Gestion des Quizz
        </a>
        <a href="<?= BASE_URL ?>/admin/reviews" class="nav-link <?= $current_page == 'reviews' ? 'active' : '' ?> rounded-3 mb-2 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-hand-holding-heart me-3"></i>Contributions</span>
            <?php if ($pendingCount > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/admin/messages" class="nav-link <?= $current_page == 'messages' ? 'active' : '' ?> rounded-3 mb-2 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-envelope me-3"></i>Messages</span>
            <?php if ($unreadMsgCount > 0): ?>
                <span class="badge bg-primary rounded-pill"><?= $unreadMsgCount ?></span>
            <?php endif; ?>
        </a>
        <div class="nav-divider my-4 opacity-25"></div>
        <a href="<?= BASE_URL ?>/admin/analytics" class="nav-link <?= $current_page == 'analytics' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-chart-line me-3"></i>Statistiques
        </a>
        <a href="<?= BASE_URL ?>/admin/users" class="nav-link <?= $current_page == 'users' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-users me-3"></i>Utilisateurs
        </a>
        <a href="<?= BASE_URL ?>/admin/settings" class="nav-link <?= $current_page == 'settings' ? 'active' : '' ?> rounded-3 mb-2">
            <i class="fas fa-cog me-3"></i>Configuration
        </a>
        <div class="mt-auto pb-4">
            <a href="<?= BASE_URL ?>/logout" class="nav-link text-danger rounded-3">
                <i class="fas fa-sign-out-alt me-3"></i>Déconnexion
            </a>
        </div>
    </nav>
</aside>
