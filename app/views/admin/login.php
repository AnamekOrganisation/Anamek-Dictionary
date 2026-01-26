<?php
// View: Admin Login
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<main class="main-content">
    <div class="login-container">
        <h1>Administration</h1>

        <form method="post" class="login-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text"
                       id="username"
                       name="username"
                       required
                       autocomplete="username"
                       value="<?= htmlspecialchars($username ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       autocomplete="current-password">
            </div>

            <button type="submit" class="login-button">Se connecter</button>

            <?php if ($error): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</main>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
