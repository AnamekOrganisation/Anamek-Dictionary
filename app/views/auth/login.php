<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<style>
    .auth-container {
        max-width: 450px;
        margin: 80px auto;
        padding: 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .auth-header h1 {
        color: #2c3e50;
        font-size: 28px;
        margin-bottom: 10px;
    }
    .auth-header p {
        color: #7f8c8d;
        font-size: 14px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #34495e;
        font-weight: 500;
        font-size: 14px;
    }
    .input-wrapper {
        position: relative;
    }
    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 15px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }
    .form-group input:focus {
        outline: none;
        border-color: #3498db;
    }
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
        cursor: pointer;
        z-index: 10;
    }
    .password-toggle:hover {
        color: #3498db;
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
    .checkbox-group input[type="checkbox"] {
        width: auto;
    }
    .checkbox-group label {
        margin: 0;
        font-weight: normal;
    }
    .error-message {
        background: #fee;
        border-left: 4px solid #e74c3c;
        color: #c0392b;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }
    .btn-primary {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
    }
    .auth-footer {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #ecf0f1;
    }
    .auth-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    .auth-footer a:hover {
        text-decoration: underline;
    }
    .forgot-password {
        text-align: right;
        margin-top: -10px;
        margin-bottom: 20px;
    }
    .forgot-password a {
        color: #7f8c8d;
        font-size: 13px;
        text-decoration: none;
    }
    .forgot-password a:hover {
        color: #667eea;
    }
    
    /* Google Button Styles */
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 20px 0;
        color: #7f8c8d;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #ecf0f1;
    }
    .divider span {
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .btn-google {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px;
        background: white;
        color: #34495e;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .btn-google:hover {
        background: #f8f9fa;
        border-color: #dcdde1;
        color: #2c3e50;
    }
</style>

<div class="auth-container">
    <div class="auth-header">
        <h1>Connexion</h1>
        <p>Accédez à votre compte Anamek</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: #d4edda; border-left: 4px solid #28a745; color: #155724; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/login">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label for="email_or_username">Email ou nom d'utilisateur</label>
            <input type="text" 
                   id="email_or_username" 
                   name="email_or_username" 
                   required
                   autofocus
                   placeholder="Entrez votre email ou nom d'utilisateur">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-wrapper">
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       placeholder="Entrez votre mot de passe">
                <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
            </div>
        </div>

        <div class="forgot-password">
            <a href="<?= BASE_URL ?>/forgot-password">Mot de passe oublié?</a>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" id="remember_me" name="remember_me" value="1">
            <label for="remember_me">Se souvenir de moi</label>
        </div>

        <button type="submit" class="btn-primary">Se connecter</button>

        <div class="divider">
            <span>OU</span>
        </div>

        <a href="<?= BASE_URL ?>/auth/google" class="btn-google">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="18">
            Se connecter avec Google
        </a>
    </form>

    <div class="auth-footer">
        <p>Vous n'avez pas de compte? <a href="<?= BASE_URL ?>/register">S'inscrire</a></p>
        <p><a href="<?= BASE_URL ?>/">← Retour à l'accueil</a></p>
    </div>
</div>

<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
