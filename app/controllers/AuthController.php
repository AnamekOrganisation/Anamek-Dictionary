<?php

/**
 * Authentication Controller
 * Handles user registration, login, logout, and password management
 */
class AuthController extends BaseController {
    private $userModel;
    private $authService;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->userModel = new User($pdo);
        $this->authService = new \App\Services\AuthService($pdo);
    }

    /**
     * Show registration form
     */
    public function showRegister() {
        $page_title = "Inscription - Amawal";
        $errors = $_SESSION['errors'] ?? [];
        $old_input = $_SESSION['old_input'] ?? [];
        
        // Clear session data
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);
        
        include ROOT_PATH . '/app/views/auth/register.php';
    }

    /**
     * Process registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('/register', 'Accès non autorisé.');
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? '')
        ];

        // Process registration via service
        $result = $this->authService->registerUser($data);
        
        if (!$result['success']) {
            $_SESSION['errors'] = $result['errors'];
            $_SESSION['old_input'] = $data;
            $this->redirectWithError('/register', 'Échec de l\'inscription.');
        }

        $user = $result['user'];

        // Send verification email
        if (defined('SMTP_HOST') && SMTP_HOST) {
            $this->sendVerificationEmail($user);
        }

        // Log the user in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = 'regular';
        
        $this->redirectWith('/user/dashboard', 'Inscription réussie ! Bienvenue à Anamek.');
    }

    /**
     * Show login form
     */
    public function showLogin() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectWith('/user/dashboard', 'Vous êtes déjà connecté.');
        }

        $page_title = "Connexion - Amawal";
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        
        include ROOT_PATH . '/app/views/auth/login.php';
    }

    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('/login', 'Accès non autorisé.');
        }

        $emailOrUsername = trim($_POST['email_or_username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        // Attempt login via service
        $result = $this->authService->loginUser($emailOrUsername, $password);
        
        if (!$result['success']) {
            $_SESSION['errors'] = $result['errors'];
            $this->redirectWithError('/login', 'Identifiants invalides.');
        }

        $user = $result['user'];

        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['email_verified'] = $user['email_verified'];

        // Persistent session
        if ($rememberMe) {
            $sessionToken = $this->userModel->createSession(
                $user['id'],
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );
            setcookie('session_token', $sessionToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        }

        $defaultDashboard = ($user['user_type'] === 'admin') ? BASE_URL . '/admin/dashboard' : BASE_URL . '/user/dashboard';
        $redirectUrl = $_SESSION['intended_url'] ?? $defaultDashboard;
        unset($_SESSION['intended_url']);
        
        header('Location: ' . $redirectUrl); // Use header for intended_url as it might have BASE_URL already or be absolute
        exit;
    }

    /**
     * Logout user
     */
    public function logout() {
        if (isset($_COOKIE['session_token'])) {
            $this->userModel->destroySession($_COOKIE['session_token']);
            setcookie('session_token', '', time() - 3600, '/');
        }

        session_unset();
        session_destroy();
        
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    public function showForgotPassword() {
        $page_title = "Mot de passe oublié - Amawal";
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? '';
        
        unset($_SESSION['errors']);
        unset($_SESSION['success']);
        
        include ROOT_PATH . '/app/views/auth/forgot-password.php';
    }

    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('/forgot-password', 'Accès non autorisé.');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('/forgot-password', 'Veuillez entrer une adresse e-mail valide.');
        }

        $resetToken = $this->userModel->requestPasswordReset($email);
        if ($resetToken) {
            $this->sendPasswordResetEmail($email, $resetToken);
        }

        $this->redirectWith('/forgot-password', 'Si un compte existe, un lien a été envoyé.');
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirectWithError('/login', 'Token manquant.');
        }

        $page_title = "Réinitialiser le mot de passe - Amawal";
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        
        include ROOT_PATH . '/app/views/auth/reset-password.php';
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('/login', 'Accès non autorisé.');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errors = [];
        if (empty($password) || strlen($password) < 8) $errors[] = 'Minimum 8 caractères.';
        if ($password !== $passwordConfirm) $errors[] = 'Les mots de passe ne correspondent pas.';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirectWithError('/reset-password?token=' . urlencode($token), 'Erreurs de validation.');
        }

        if ($this->userModel->resetPassword($token, $password)) {
            $this->redirectWith('/login', 'Mot de passe réinitialisé.');
        } else {
            $this->redirectWithError('/forgot-password', 'Lien invalide ou expiré.');
        }
    }

    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) $this->redirectWith('/', '');

        if ($this->userModel->verifyEmail($token)) {
            if (isset($_SESSION['user_id'])) $_SESSION['email_verified'] = true;
            $this->redirectWith('/login', 'Email vérifié !');
        } else {
            $this->redirectWithError('/login', 'Lien invalide.');
        }
    }

    private function sendVerificationEmail($user) {
        $verificationUrl = BASE_URL . '/verify-email?token=' . $user['verification_token'];
        $subject = 'Vérifiez votre email - Anamek';
        $message = "<h2>Bienvenue!</h2><p><a href='$verificationUrl'>Vérifier mon email</a></p>";
        Email::send($user['email'], $subject, $message);
    }

    private function sendPasswordResetEmail($email, $token) {
        $resetUrl = BASE_URL . '/reset-password?token=' . $token;
        $subject = 'Réinitialisation de mot de passe';
        $message = "<h2>Réinitialisation</h2><p><a href='$resetUrl'>Lien</a></p>";
        Email::send($email, $subject, $message);
    }
}
