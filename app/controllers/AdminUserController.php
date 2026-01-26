<?php

class AdminUserController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function users() {
        $this->checkAuth();
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $page_title = 'Gestion des utilisateurs';
        require_once ROOT_PATH . '/app/views/admin/users.php';
    }
}
