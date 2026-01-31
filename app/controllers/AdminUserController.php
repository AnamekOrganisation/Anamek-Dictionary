<?php

class AdminUserController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function users() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $page_title = 'Gestion des utilisateurs';
        require_once ROOT_PATH . '/app/views/admin/users.php';
    }
}
