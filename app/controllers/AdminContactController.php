<?php

class AdminContactController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function index() {
        $stmt = $this->pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll();
        include ROOT_PATH . '/app/views/admin/messages/index.php';
    }

    public function view($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        $message = $stmt->fetch();

        if (!$message) {
            $_SESSION['flash_error'] = "Message non trouvé.";
            header('Location: ' . BASE_URL . '/admin/messages');
            exit;
        }

        // Mark as read
        if (!$message['is_read']) {
            $update = $this->pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $update->execute([$id]);
        }

        include ROOT_PATH . '/app/views/admin/messages/view.php';
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        
        $id = $_POST['id'] ?? 0;
        $stmt = $this->pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = "Message supprimé avec succès.";
        header('Location: ' . BASE_URL . '/admin/messages');
        exit;
    }
}
