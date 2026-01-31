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
     * Show user dashboard
     */
    public function dashboard() {
        $user = $this->userModel->find($_SESSION['user_id']);
        $stats = $this->userModel->getStats($_SESSION['user_id']);
        
        $page_title = "Tableau de bord - Amawal";
        include ROOT_PATH . '/app/views/user/dashboard.php';
    }

    /**
     * Show user profile
     */
    public function profile() {
        $user = $this->userModel->find($_SESSION['user_id']);
        $stats = $this->userModel->getStats($_SESSION['user_id']);
        
        $page_title = "Mon Profil - Amawal";
        include ROOT_PATH . '/app/views/user/profile.php';
    }

    /**
     * Update user profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
