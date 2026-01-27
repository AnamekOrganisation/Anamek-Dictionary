<?php

/**
 * Authentication Controller
 * Handles user registration, login, logout, and password management
 */
class AuthController {
    private $pdo;
    private $userModel;
    private $authService;

    public function __construct($pdo) {
        $this->pdo = $pdo;
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
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        // CSRF check
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['errors'] = ['Invalid security token'];
            header('Location: ' . BASE_URL . '/register');
            exit;
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
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $user = $result['user'];

        // Send verification email (if email is configured)
        if (defined('SMTP_HOST') && SMTP_HOST) {
            $this->sendVerificationEmail($user);
        }

        // Log the user in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = 'regular';
        
        $_SESSION['success'] = 'Inscription réussie ! Bienvenue à Anamek.';
        header('Location: ' . BASE_URL . '/user/dashboard');
        exit;
    }

    /**
     * Show login form
     */
    public function showLogin() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/user/dashboard');
            exit;
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
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // CSRF check
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['errors'] = ['Invalid security token'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $emailOrUsername = trim($_POST['email_or_username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        // Attempt login via service
        $result = $this->authService->loginUser($emailOrUsername, $password);
        
        if (!$result['success']) {
            $_SESSION['errors'] = $result['errors'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = $result['user'];

        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['email_verified'] = $user['email_verified'];

        // Create persistent session if "remember me" is checked
        if ($rememberMe) {
            $sessionToken = $this->userModel->createSession(
                $user['id'],
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );
            
            // Set cookie for 30 days
            setcookie('session_token', $sessionToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        }

        // Redirect to intended page or dashboard based on user type
        $defaultDashboard = ($user['user_type'] === 'admin') ? BASE_URL . '/dashboard' : BASE_URL . '/user/dashboard';
        $redirectUrl = $_SESSION['intended_url'] ?? $defaultDashboard;
        unset($_SESSION['intended_url']);
        
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Logout user
     */
    public function logout() {
        // Destroy session token if exists
        if (isset($_COOKIE['session_token'])) {
            $this->userModel->destroySession($_COOKIE['session_token']);
            setcookie('session_token', '', time() - 3600, '/');
        }

        // Clear session
        session_unset();
        session_destroy();
        
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        $page_title = "Mot de passe oublié - Amawal";
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? '';
        
        unset($_SESSION['errors']);
        unset($_SESSION['success']);
        
        include ROOT_PATH . '/app/views/auth/forgot-password.php';
    }

    /**
     * Send password reset link
     */
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/forgot-password');
            exit;
        }

        // CSRF check
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['errors'] = ['Invalid security token'];
            header('Location: ' . BASE_URL . '/forgot-password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errors'] = ['Please enter a valid email address'];
            header('Location: ' . BASE_URL . '/forgot-password');
            exit;
        }

        // Request password reset
        $resetToken = $this->userModel->requestPasswordReset($email);
        
        if ($resetToken) {
            // Send reset email
            $this->sendPasswordResetEmail($email, $resetToken);
        }

        // Always show success message (security - don't reveal if email exists)
        $_SESSION['success'] = ' Si un compte existe avec cette adresse e-mail, un lien de réinitialisation du mot de passe a été envoyé. \n Veuillez vérifier votre boîte de réception ainsi que votre dossier de courrier indésirable (spam).';
        header('Location: ' . BASE_URL . '/forgot-password');
        exit;
    }

    /**
     * Show password reset form
     */
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $page_title = "Réinitialiser le mot de passe - Amawal";
        $errors = $_SESSION['errors'] ?? [];
        
        unset($_SESSION['errors']);
        
        include ROOT_PATH . '/app/views/auth/reset-password.php';
    }

    /**
     * Process password reset
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // CSRF check
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['errors'] = ['Invalid security token'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validation
        $errors = [];
        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '/reset-password?token=' . urlencode($token));
            exit;
        }

        // Reset password
        $success = $this->userModel->resetPassword($token, $password);
        
        if (!$success) {
            $_SESSION['errors'] = ['Invalid or expired reset token'];
            header('Location: ' . BASE_URL . '/forgot-password');
            exit;
        }

        $_SESSION['success'] = 'Réinitialisation du mot de passe réussie. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /**
     * Verify email with token
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $success = $this->userModel->verifyEmail($token);
        
        if ($success) {
            if (isset($_SESSION['user_id'])) {
                $_SESSION['email_verified'] = true;
            }
            $_SESSION['success'] = 'Adresse e-mail vérifiée avec succès!';
        } else {
            $_SESSION['errors'] = ['Invalid or expired verification link'];
        }

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    // validateRegistration method removed in favor of Service & Validator

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * @deprecated Use verify_csrf() helper instead
     */
    private function verifyCsrfToken($token) {
        return verify_csrf($token);
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail($user) {
        // Only send if email helper exists
        if (!class_exists('Email')) {
            return;
        }

        $verificationUrl = BASE_URL . '/verify-email?token=' . $user['verification_token'];
        
        $subject = 'Vérifiez votre email - Anamek';
        $message = "
            <h2>Bienvenue sur Anamek!</h2>
            <p>Merci de vous être inscrit. Veuillez vérifier votre adresse email en cliquant sur le lien ci-dessous:</p>
            <p><a href='$verificationUrl'>Vérifier mon email</a></p>
            <p>Si vous n'avez pas créé de compte, vous pouvez ignorer cet email.</p>
        ";

        Email::send($user['email'], $subject, $message);
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($email, $token) {
        // Only send if email helper exists
        if (!class_exists('Email')) {
            return;
        }

        $resetUrl = BASE_URL . '/reset-password?token=' . $token;
        
        $subject = 'Réinitialisation de mot de passe - Anamek';
        $message = "
            <h2>Réinitialisation de mot de passe</h2>
            <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous:</p>
            <p><a href='$resetUrl'>Réinitialiser mon mot de passe</a></p>
            <p>Ce lien expirera dans 1 heure.</p>
            <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
        ";

        Email::send($email, $subject, $message);
    }
}
