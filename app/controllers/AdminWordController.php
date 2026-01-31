<?php

class AdminWordController {
    private $pdo;
    private $wordService;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Ensure WordService is loaded. In a real app this would be autoloaded or injected.
        if (!class_exists('\App\Services\WordService')) {
             require_once ROOT_PATH . '/app/Services/WordService.php';
        }
        $this->wordService = new \App\Services\WordService($this->pdo);
    }

    private function verifyCsrf() {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            die('CSRF validation failed.');
        }
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

    public function words() {
        $this->checkAuth();
        
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $params = [];
        
        $sql = "SELECT * FROM words";
        $countSql = "SELECT COUNT(*) FROM words";
        
        if (!empty($search)) {
            $where = " WHERE word_tfng LIKE :q OR word_lat LIKE :q OR translation_fr LIKE :q";
            $sql .= $where;
            $countSql .= $where;
            $params[':q'] = "%$search%";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total_words = $stmt->fetchColumn();
        $total_pages = ceil($total_words / $limit);
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $words = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $current_page_num = $page;
        $message = $_COOKIE['admin_message'] ?? '';
        setcookie('admin_message', '', time() - 3600, '/');
        
        $page_title = 'Liste des mots';
        require_once ROOT_PATH . '/app/views/admin/words.php';
    }

    public function addWord(array $data) {
        $this->verifyCsrf();
        
        $validation = $this->wordService->validateWord($data);
        if (!$validation['success']) {
            $_SESSION['errors'] = $validation['errors'];
            return false;
        }

        $result = $this->wordService->createWord($data);
        return $result['success'];
    }

    public function addWordPage() {
        $this->checkAuth();
        $message = '';
        $result = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->addWord($_POST)) {
                if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                $message = 'Mot ajouté avec succès !';
                $result = true;
            } else {
                $message = 'Erreur lors de l\'ajout du mot.';
                $result = false;
            }
        }
        $page_title = 'Ajouter un mot';
        require_once ROOT_PATH . '/app/views/admin/add-word.php';
    }

    public function editWord() {
        $this->checkAuth();
        $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
        if (!$id) {
             header('Location: ' . BASE_URL . '/admin/words');
             exit;
        }

        $message = '';
        $result = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            if (isset($_POST['update_word'])) {
                // Use Service for Logic
                $updateResult = $this->wordService->updateWord($id, $_POST);
                
                if ($updateResult['success']) {
                    if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                    $message = 'Mot mis à jour avec succès !';
                    $result = true;
                } else {
                    $errors = $updateResult['errors'] ?? ['Erreur inconnue'];
                    $message = 'Erreur: ' . implode(', ', $errors);
                    $result = false;
                }
            }
        }
        
        $word = $this->getWordById($id);
        if ($word) {
            $synonyms = $this->getWordSynonyms($id);
            $antonyms = $this->getWordAntonyms($id);
            $examples = $this->getWordExamples($id);
        } else {
             header('Location: ' . BASE_URL . '/admin/words');
             exit;
        }

        $page_title = 'Modifier un mot';
        require_once ROOT_PATH . '/app/views/admin/edit-word.php';
    }

    public function deleteWord() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $this->verifyCsrf();
            $id = intval($_POST['id']);
            
            $deleteResult = $this->wordService->deleteWord($id);

            if ($deleteResult['success']) {
                if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                setcookie('admin_message', '<div class="alert success">Mot supprimé avec succès !</div>', time() + 10, '/');
            } else {
                setcookie('admin_message', '<div class="alert error">Erreur lors de la suppression.</div>', time() + 10, '/');
            }
        }
        header('Location: ' . BASE_URL . '/admin/words');
        exit;
    }

    public function getWordById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM words WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getWordSynonyms($wordId) {
        $stmt = $this->pdo->prepare("SELECT synonym_tfng, synonym_lat FROM synonyms WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWordAntonyms($wordId) {
        $stmt = $this->pdo->prepare("SELECT antonym_tfng, antonym_lat FROM antonyms WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWordExamples($wordId) {
        $stmt = $this->pdo->prepare("SELECT id, example_tfng, example_lat, example_fr FROM examples WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
