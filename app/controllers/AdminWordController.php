<?php

class AdminWordController extends BaseController {
    private $wordService;
    private $wordRepo;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->wordRepo = new \App\Repositories\WordRepository($pdo);
        $this->wordService = new \App\Services\WordService($pdo);
    }

    public function words() {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 20;
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        $words = $this->wordRepo->getPaginated($page, $limit, $search);
        $total_words = !empty($search) ? $this->wordRepo->countSearch($search) : $this->wordRepo->countAll();
        $total_pages = ceil($total_words / $limit);
        
        $current_page_num = $page;
        $page_title = 'Liste des mots';
        require_once ROOT_PATH . '/app/views/admin/words.php';
    }

    public function addWordPage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validation = $this->wordService->validateWord($_POST);
            if (!$validation['success']) {
                $_SESSION['errors'] = $validation['errors'];
                $this->redirectWithError('/admin/words/add', 'Veuillez corriger les erreurs.');
            }

            $result = $this->wordService->createWord($_POST);
            if ($result['success']) {
                if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                $this->redirectWith('/admin/words', 'Mot ajouté avec succès !');
            } else {
                $this->redirectWithError('/admin/words/add', 'Erreur lors de l\'ajout du mot.');
            }
        }
        $page_title = 'Ajouter un mot';
        require_once ROOT_PATH . '/app/views/admin/add-word.php';
    }

    public function editWord() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
        if (!$id) {
             $this->redirectWithError('/admin/words', 'ID manquant.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_word'])) {
                $updateResult = $this->wordService->updateWord($id, $_POST);
                if ($updateResult['success']) {
                    if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                    $this->redirectWith('/admin/words', 'Mot mis à jour avec succès !');
                } else {
                    $errors = $updateResult['errors'] ?? ['Erreur inconnue'];
                    $this->redirectWithError('/admin/words/edit?id=' . $id, 'Erreur: ' . implode(', ', $errors));
                }
            }
        }
        
        $word = $this->wordRepo->find($id);
        if (!$word) {
             $this->redirectWithError('/admin/words', 'Mot introuvable.');
        }

        // De-structure for view compatibility if needed, or update view
        $synonyms = $word['synonyms'];
        $antonyms = $word['antonyms'];
        $examples = $word['examples'];

        $page_title = 'Modifier un mot';
        require_once ROOT_PATH . '/app/views/admin/edit-word.php';
    }

    public function deleteWord() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $deleteResult = $this->wordService->deleteWord($id);

            if ($deleteResult['success']) {
                if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                $this->redirectWith('/admin/words', 'Mot supprimé avec succès !');
            } else {
                $this->redirectWithError('/admin/words', 'Erreur lors de la suppression.');
            }
        }
        $this->redirectWithError('/admin/words', 'Action non autorisée.');
    }
}
