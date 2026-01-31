<?php

class AdminProverbController extends BaseController {
    private $proverbService;

    public function __construct($pdo) {
        parent::__construct($pdo);
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
        $page_title = 'Liste des proverbes';
        require_once ROOT_PATH . '/app/views/admin/proverbs.php';
    }

    public function editProverbPage() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
        if (!$id) {
            $this->redirectWithError('/admin/proverbs', 'ID manquant.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_proverb'])) {
            if ($this->proverbService->updateProverb($id, $_POST)) {
                $this->redirectWith('/admin/proverbs', 'Proverbe mis à jour avec succès !');
            } else {
                $this->redirectWithError('/admin/proverbs/edit?id=' . $id, 'Erreur lors de la mise à jour.');
            }
        }

        $proverb = $this->getProverbById($id);
        if (!$proverb) {
            $this->redirectWithError('/admin/proverbs', 'Proverbe introuvable.');
        }

        $page_title = 'Modifier un proverbe';
        require_once ROOT_PATH . '/app/views/admin/edit-proverb.php';
    }

    public function addProverbPage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->proverbService->createProverb($_POST)) {
                $this->redirectWith('/admin/proverbs', 'Proverbe ajouté avec succès !');
            } else {
                $this->redirectWithError('/admin/proverbs/add', 'Erreur lors de l\'ajout.');
            }
        }
        $page_title = 'Ajouter un proverbe';
        require_once ROOT_PATH . '/app/views/admin/add-proverb.php';
    }

    public function deleteProverb() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            if ($this->proverbService->deleteProverb($id)) {
                $this->redirectWith('/admin/proverbs', 'Proverbe supprimé avec succès !');
            } else {
                $this->redirectWithError('/admin/proverbs', 'Erreur lors de la suppression.');
            }
        }
        $this->redirectWithError('/admin/proverbs', 'Action non autorisée.');
    }

    private function getProverbById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proverbs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
