<?php

class AdminUserController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function users() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            if (!$userId) $this->redirectWithError('/admin/users', 'ID utilisateur manquant.');

            try {
                if (isset($_POST['change_role'])) {
                    $role = $_POST['role'] ?? 'regular';
                    $stmt = $this->pdo->prepare("UPDATE users SET user_type = ? WHERE id = ?");
                    $stmt->execute([$role, $userId]);
                    $message = "Rôle mis à jour avec succès.";
                } elseif (isset($_POST['toggle_status'])) {
                    $stmt = $this->pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$userId]);
                    $message = "Statut de l'utilisateur modifié.";
                }
                
                if (isset($message)) {
                    $_SESSION['flash_message'] = $message;
                    header('Location: ' . BASE_URL . '/admin/users');
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
                header('Location: ' . BASE_URL . '/admin/users');
                exit;
            }
        }

        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $message = $_SESSION['flash_message'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $page_title = 'Gestion des utilisateurs';
        require_once ROOT_PATH . '/app/views/admin/users.php';
    }

    /**
     * Show detailed user info and their contributions
     */
    public function userDetails($id) {
        // Fetch user basic info
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->redirectWithError('/admin/users', 'Utilisateur introuvable.');
        }

        // Fetch user contributions
        require_once ROOT_PATH . '/app/models/Contribution.php';
        $contributionModel = new Contribution($this->pdo);
        $contributions = $contributionModel->findByUser($id);

        $page_title = 'Détails Utilisateur : ' . $user['username'];
        require_once ROOT_PATH . '/app/views/admin/user-details.php';
    }
}
