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
</style>

<div class="auth-container">
    <div class="auth-header">
        <h1>Créer un compte</h1>
        <p>Rejoignez la communauté Anamek</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/register">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label for="username">Nom d'utilisateur *</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="<?= htmlspecialchars($old_input['username'] ?? '') ?>"
                   required 
                   minlength="3"
                   pattern="[a-zA-Z0-9_]+"
                   placeholder="Choisissez un nom d'utilisateur">
            <small style="color: #7f8c8d; font-size: 12px;">Lettres, chiffres et underscore uniquement</small>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="<?= htmlspecialchars($old_input['email'] ?? '') ?>"
                   required
                   placeholder="votre@email.com">
        </div>

        <div class="form-group">
            <label for="full_name">Nom complet</label>
            <input type="text" 
                   id="full_name" 
                   name="full_name" 
                   value="<?= htmlspecialchars($old_input['full_name'] ?? '') ?>"
                   placeholder="Votre nom (optionnel)">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe *</label>
            <div class="input-wrapper">
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       minlength="8"
                       placeholder="Au moins 8 caractères">
                <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe *</label>
            <div class="input-wrapper">
                <input type="password" 
                       id="password_confirm" 
                       name="password_confirm" 
                       required
                       minlength="8"
                       placeholder="Retapez votre mot de passe">
                <i class="fas fa-eye password-toggle" onclick="togglePassword('password_confirm', this)"></i>
            </div>
        </div>

        <button type="submit" class="btn-primary">S'inscrire</button>
    </form>

    <div class="auth-footer">
        <p>Vous avez déjà un compte? <a href="<?= BASE_URL ?>/login">Se connecter</a></p>
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

    // Client-side password match validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas');
        }
    });
</script>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
