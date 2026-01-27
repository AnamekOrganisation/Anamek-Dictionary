<?php

class AdminWordController {
    private $pdo;
    private $wordService;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    // addSynonyms and addAntonyms moved to WordService

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
                try {
                    $this->pdo->beginTransaction();
                    $updated = $this->updateWordLogic($id, $_POST);
                    if ($updated) {
                        // Handle synonyms and antonyms as arrays from dynamic fields
                        $syns_tfng = $_POST['synonyms_tfng'] ?? [];
                        $syns_lat = $_POST['synonyms_lat'] ?? [];
                        $this->updateSynonyms($id, $syns_tfng, $syns_lat);
                        
                        $ants_tfng = $_POST['antonyms_tfng'] ?? [];
                        $ants_lat = $_POST['antonyms_lat'] ?? [];
                        $this->updateAntonyms($id, $ants_tfng, $ants_lat);
                        
                        $this->updateExamples($id, $_POST);
                        $this->pdo->commit();
                        $message = 'Mot mis à jour avec succès !';
                        $result = true;
                        if (class_exists('App\Helpers\SiteStats')) App\Helpers\SiteStats::clearCache();
                    } else {
                        $this->pdo->rollBack();
                        $message = 'Erreur lors de la mise à jour.';
                        $result = false;
                    }
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    error_log("Update word error: " . $e->getMessage());
                    $message = 'Erreur: ' . $e->getMessage();
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

    private function updateWordLogic($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE words SET word_tfng = ?, word_lat = ?, translation_fr = ?, definition_tfng = ?, definition_lat = ?, plural_tfng = ?, plural_lat = ?, feminine_tfng = ?, feminine_lat = ?, annexed_tfng = ?, annexed_lat = ?, root_tfng = ?, root_lat = ?, part_of_speech = ?, example_tfng = ?, example_lat = ? WHERE id = ?");
        return $stmt->execute([
            $data['word_tfng'] ?? '', $data['word_lat'] ?? '', $data['translation_fr'] ?? '', $data['definition_tfng'] ?? '', $data['definition_lat'] ?? '',
            $data['plural_tfng'] ?? '', $data['plural_lat'] ?? '', $data['feminine_tfng'] ?? '', $data['feminine_lat'] ?? '',
            $data['annexed_tfng'] ?? '', $data['annexed_lat'] ?? '', $data['root_tfng'] ?? '', $data['root_lat'] ?? '',
            $data['part_of_speech'] ?? '', $data['example_tfng'] ?? '', $data['example_lat'] ?? '', $id
        ]);
    }

    private function updateSynonyms($wordId, $synonyms, $synonyms_lat = null) {
        $this->pdo->prepare("DELETE FROM synonyms WHERE word_id = ?")->execute([$wordId]);
        
        $tfng = is_array($synonyms) ? $synonyms : (is_string($synonyms) ? array_filter(array_map('trim', explode(',', $synonyms))) : []);
        $lat = is_array($synonyms_lat) ? $synonyms_lat : (is_string($synonyms_lat) ? array_filter(array_map('trim', explode(',', $synonyms_lat))) : []);
        
        $max = max(count($tfng), count($lat));
        if ($max === 0) return true;

        $stmt = $this->pdo->prepare("INSERT INTO synonyms (word_id, synonym_tfng, synonym_lat) VALUES (?, ?, ?)");
        for ($i = 0; $i < $max; $i++) {
            $t = !empty($tfng[$i]) ? trim($tfng[$i]) : null;
            $l = !empty($lat[$i]) ? trim($lat[$i]) : null;
            if ($t !== null || $l !== null) {
                $stmt->execute([$wordId, $t, $l]);
            }
        }
        return true;
    }

    private function updateAntonyms($wordId, $antonyms, $antonyms_lat = null) {
        $this->pdo->prepare("DELETE FROM antonyms WHERE word_id = ?")->execute([$wordId]);
        
        $tfng = is_array($antonyms) ? $antonyms : (is_string($antonyms) ? array_filter(array_map('trim', explode(',', $antonyms))) : []);
        $lat = is_array($antonyms_lat) ? $antonyms_lat : (is_string($antonyms_lat) ? array_filter(array_map('trim', explode(',', $antonyms_lat))) : []);
        
        $max = max(count($tfng), count($lat));
        if ($max === 0) return true;

        $stmt = $this->pdo->prepare("INSERT INTO antonyms (word_id, antonym_tfng, antonym_lat) VALUES (?, ?, ?)");
        for ($i = 0; $i < $max; $i++) {
            $t = !empty($tfng[$i]) ? trim($tfng[$i]) : null;
            $l = !empty($lat[$i]) ? trim($lat[$i]) : null;
            if ($t !== null || $l !== null) {
                $stmt->execute([$wordId, $t, $l]);
            }
        }
        return true;
    }

    public function deleteWord() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $this->verifyCsrf();
            $id = intval($_POST['id']);
            require_once ROOT_PATH . '/app/models/Word.php';
            $wordModel = new Word($this->pdo);
            if ($wordModel->delete($id)) {
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

    private function updateExamples($wordId, $data) {
        // IDs of examples that should be kept
        $submittedIds = array_filter($data['example_ids'] ?? []);
        
        // If we have submitted IDs, delete others. If none, delete ALL for this word.
        if (!empty($submittedIds)) {
            $placeholders = str_repeat('?,', count($submittedIds) - 1) . '?';
            $stmt = $this->pdo->prepare("DELETE FROM examples WHERE word_id = ? AND id NOT IN ($placeholders)");
            $stmt->execute(array_merge([$wordId], array_values($submittedIds)));
        } else {
            $stmt = $this->pdo->prepare("DELETE FROM examples WHERE word_id = ?");
            $stmt->execute([$wordId]);
        }

        // Update or insert examples
        $tfngArray = $data['examples_tfng'] ?? [];
        $latArray = $data['examples_lat'] ?? [];
        $frArray = $data['examples_fr'] ?? [];
        $idArray = $data['example_ids'] ?? [];

        foreach ($tfngArray as $i => $tfng) {
            $lat = $latArray[$i] ?? '';
            $fr = $frArray[$i] ?? '';
            $exampleId = $idArray[$i] ?? '';

            // Skip empty examples
            if (empty(trim($tfng)) && empty(trim($lat))) continue;

            if (!empty($exampleId)) {
                // Update existing example
                $stmt = $this->pdo->prepare("UPDATE examples SET example_tfng = ?, example_lat = ?, example_fr = ? WHERE id = ? AND word_id = ?");
                $stmt->execute([$tfng, $lat, $fr, $exampleId, $wordId]);
            } else {
                // Insert new example
                $stmt = $this->pdo->prepare("INSERT INTO examples (word_id, example_tfng, example_lat, example_fr) VALUES (?, ?, ?, ?)");
                $stmt->execute([$wordId, $tfng, $lat, $fr]);
            }
        }
        return true;
    }
}
