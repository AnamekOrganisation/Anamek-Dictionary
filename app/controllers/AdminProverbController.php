<?php

class AdminProverbController {
    private $pdo;
    private $proverbService;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->proverbService = new \App\Services\ProverbService($this->pdo);
    }

    public function proverbs() {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 20;
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        $resultData = $this->proverbService->getProverbs($search, $page, $limit);
        
        $proverbs = $resultData['proverbs'];
        $total_proverbs = $resultData['total'];
        $total_pages = $resultData['pages'];
        
        $current_page_num = $page;
        $message = $_COOKIE['admin_message'] ?? '';
        setcookie('admin_message', '', time() - 3600, '/');
        
        $page_title = 'Liste des proverbes';
        require_once ROOT_PATH . '/app/views/admin/proverbs.php';
    }

    public function editProverbPage() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
        
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/proverbs');
            exit;
        }

        $message = '';
        $result = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_proverb'])) {
            if ($this->proverbService->updateProverb($id, $_POST)) {
                $message = 'Proverbe mis à jour avec succès !';
                $result = true;
            } else {
                $message = 'Erreur lors de la mise à jour.';
                $result = false;
            }
        }

        $proverb = $this->getProverbById($id);
        if (!$proverb) {
            header('Location: ' . BASE_URL . '/admin/proverbs');
            exit;
        }

        $page_title = 'Modifier un proverbe';
        require_once ROOT_PATH . '/app/views/admin/edit-proverb.php';
    }

    public function addProverbPage() {
        $message = '';
        $result = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->proverbService->createProverb($_POST)) {
                $message = 'Proverbe ajouté avec succès !';
                $result = true;
            } else {
                $message = 'Erreur lors de l\'ajout.';
                $result = false;
            }
        }
        $page_title = 'Ajouter un proverbe';
        require_once ROOT_PATH . '/app/views/admin/add-proverb.php';
    }

    public function getProverbById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proverbs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteProverb() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            if ($this->proverbService->deleteProverb($id)) {
                setcookie('admin_message', '<div class="alert success">Proverbe supprimé avec succès !</div>', time() + 10, '/');
            } else {
                setcookie('admin_message', '<div class="alert error">Erreur lors de la suppression.</div>', time() + 10, '/');
            }
        }
        header('Location: ' . BASE_URL . '/admin/proverbs');
        exit;
    }

    public function searchProverb($query) {
        $stmt = $this->pdo->prepare("SELECT * FROM proverbs WHERE proverb_tfng LIKE :q OR proverb_lat LIKE :q OR translation_fr LIKE :q LIMIT 20");
        $stmt->execute([':q' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
