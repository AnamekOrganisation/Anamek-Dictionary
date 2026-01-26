<?php

/**
 * Contribution Controller
 * Handles user submission of new dictionary content
 */
class ContributionController {
    private $pdo;
    private $contributionModel;
    private $notificationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->contributionModel = new Contribution($pdo);
        $this->notificationModel = new Notification($pdo);
        
        // Ensure user is logged in for all contribution actions
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Display my contributions list
     */
    public function myContributions() {
        $userId = $_SESSION['user_id'];
        $contributions = $this->contributionModel->findByUser($userId);
        $stats = $this->contributionModel->getStats($userId);
        
        $page_title = "Mes Contributions - Amawal";
        include ROOT_PATH . '/app/views/user/my-contributions.php';
    }

    /**
     * Show form to contribute a new word
     */
    public function showWordForm() {
        $edit_id = $_GET['edit_id'] ?? null;
        $word = null;
        if ($edit_id) {
            // If user is admin, redirect directly to admin edit page
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/edit-word?id=' . intval($edit_id));
                exit;
            }
            
            $wordModel = new Word($this->pdo);
            $word = $wordModel->find($edit_id);
        }

        $page_title = $word ? "Suggérer une modification - Amawal" : "Contribuer un mot - Amawal";
        $csrf_token = $this->generateCsrfToken();
        include ROOT_PATH . '/app/views/contribute/word.php';
    }

    /**
     * Process word contribution
     */
    public function submitWord() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/contribute/word');
            exit;
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            die("Security check failed");
        }

        $data = [
            'word_tfng' => trim($_POST['word_tfng'] ?? ''),
            'word_lat' => trim($_POST['word_lat'] ?? ''),
            'translation_fr' => trim($_POST['translation_fr'] ?? ''),
            'definition_tfng' => trim($_POST['definition_tfng'] ?? ''),
            'definition_lat' => trim($_POST['definition_lat'] ?? ''),
            'plural_tfng' => trim($_POST['plural_tfng'] ?? ''),
            'plural_lat' => trim($_POST['plural_lat'] ?? ''),
            'feminine_tfng' => trim($_POST['feminine_tfng'] ?? ''),
            'feminine_lat' => trim($_POST['feminine_lat'] ?? ''),
            'annexed_tfng' => trim($_POST['annexed_tfng'] ?? ''),
            'annexed_lat' => trim($_POST['annexed_lat'] ?? ''),
            'root_tfng' => trim($_POST['root_tfng'] ?? ''),
            'root_lat' => trim($_POST['root_lat'] ?? ''),
            'part_of_speech' => $_POST['word_type'] ?? '', // Map word_type to part_of_speech
            'example_tfng' => trim($_POST['example_tfng'] ?? ''),
            'example_lat' => trim($_POST['example_lat'] ?? '')
        ];

        // Handle Synonyms/Antonyms as arrays
        if (!empty($_POST['synonyms_tfng'])) {
            $syns = [];
            foreach ($_POST['synonyms_tfng'] as $i => $tfng) {
                 if (!empty($tfng)) {
                     $syns[] = ['tfng' => $tfng, 'lat' => $_POST['synonyms_lat'][$i] ?? ''];
                 }
            }
            $data['synonyms'] = $syns;
        }

        if (!empty($_POST['antonyms_tfng'])) {
            $ants = [];
            foreach ($_POST['antonyms_tfng'] as $i => $tfng) {
                 if (!empty($tfng)) {
                     $ants[] = ['tfng' => $tfng, 'lat' => $_POST['antonyms_lat'][$i] ?? ''];
                 }
            }
            $data['antonyms'] = $ants;
        }

        // Handle Examples as arrays
        if (!empty($_POST['examples_tfng'])) {
            $exs = [];
            foreach ($_POST['examples_tfng'] as $i => $tfng) {
                 $lat = $_POST['examples_lat'][$i] ?? '';
                 $fr = $_POST['examples_fr'][$i] ?? '';
                 if (!empty($tfng) || !empty($lat)) {
                     $exs[] = ['tfng' => $tfng, 'lat' => $lat, 'fr' => $fr];
                 }
            }
            $data['examples'] = $exs;
        }

        $action = !empty($_POST['target_id']) ? 'update' : 'create';
        $targetId = $_POST['target_id'] ?? null;
        $contentBefore = null;

        if ($action === 'update' && $targetId) {
            $wordModel = new Word($this->pdo);
            $contentBefore = $wordModel->find($targetId);
        }

        // Basic validation
        if (empty($data['word_tfng']) || empty($data['word_lat']) || empty($data['translation_fr'])) {
            $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
            header('Location: ' . BASE_URL . '/contribute/word');
            exit;
        }

        $contribId = $this->contributionModel->create($_SESSION['user_id'], 'word', $data, $action, $targetId, $contentBefore);

        if ($contribId) {
            $_SESSION['success'] = "Votre contribution a été soumise et sera examinée par un modérateur. Merci !";
            header('Location: ' . BASE_URL . '/user/contributions');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la soumission.";
            header('Location: ' . BASE_URL . '/contribute/word');
        }
        exit;
    }

    /**
     * Show form to contribute an example
     */
    public function showExampleForm() {
        $wordId = $_GET['word_id'] ?? null;
        if (!$wordId) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $wordModel = new Word($this->pdo);
        $word = $wordModel->find($wordId);
        if (!$word) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $page_title = "Ajouter un exemple - Amawal";
        $csrf_token = $this->generateCsrfToken();
        include ROOT_PATH . '/app/views/contribute/example.php';
    }

    /**
     * Process example contribution
     */
    public function submitExample() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) die("Security check failed");

        $data = [
            'word_id' => $_POST['word_id'],
            'example_tfng' => trim($_POST['example_tfng'] ?? ''),
            'example_lat' => trim($_POST['example_lat'] ?? ''),
            'translation_fr' => trim($_POST['translation_fr'] ?? '')
        ];

        if (empty($data['example_tfng']) || empty($data['example_lat']) || empty($data['translation_fr'])) {
            $_SESSION['error'] = "Veuillez remplir tous les champs.";
            header('Location: ' . BASE_URL . '/contribute/example?word_id=' . $data['word_id']);
            exit;
        }

        $contribId = $this->contributionModel->create($_SESSION['user_id'], 'example', $data);

        if ($contribId) {
            $_SESSION['success'] = "Exemple soumis avec succès !";
            header('Location: ' . BASE_URL . '/user/contributions');
        } else {
            $_SESSION['error'] = "Erreur lors de la soumission.";
            header('Location: ' . BASE_URL . '/user/contributions');
        }
        exit;
    }

    private function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
