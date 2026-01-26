<?php

/**
 * User Controller
 * Handles user-specific pages like dashboard and profile
 */
class UserController {
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        require_once ROOT_PATH . '/app/models/User.php';
        $this->userModel = new User($pdo);
    }

    /**
     * Check if user is logged in
     */
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Show user dashboard
     */
    public function dashboard() {
        $this->checkAuth();
        
        $user = $this->userModel->find($_SESSION['user_id']);
        $stats = $this->userModel->getStats($_SESSION['user_id']);
        
        $page_title = "Tableau de bord - Amawal";
        include ROOT_PATH . '/app/views/user/dashboard.php';
    }

    /**
     * Show user profile
     */
    public function profile() {
        $this->checkAuth();
        
        $user = $this->userModel->find($_SESSION['user_id']);
        $stats = $this->userModel->getStats($_SESSION['user_id']);
        
        $page_title = "Mon Profil - Amawal";
        include ROOT_PATH . '/app/views/user/profile.php';
    }

    /**
     * Update user profile
     */
    public function updateProfile() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Check (using AuthController helper or similar)
            if (!isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
                die("Invalid security token");
            }

            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'bio' => trim($_POST['bio'] ?? '')
            ];

            if ($this->userModel->updateProfile($_SESSION['user_id'], $data)) {
                $_SESSION['success'] = "Profil mis à jour avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
            }

            header('Location: ' . BASE_URL . '/user/profile');
            exit;
        }
    }
}
