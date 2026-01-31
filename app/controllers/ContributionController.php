<?php

/**
 * Contribution Controller
 * Handles user submission of new dictionary content
 */
class ContributionController extends BaseController {
    private $contributionModel;
    private $notificationModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->contributionModel = new Contribution($pdo);
        $this->notificationModel = new Notification($pdo);
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
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                $this->redirectWith('/admin/edit-word?id=' . intval($edit_id), '');
            }
            
            $wordModel = new Word($this->pdo);
            $word = $wordModel->find($edit_id);
        }

        $page_title = $word ? "Suggérer une modification - Amawal" : "Contribuer un mot - Amawal";
        include ROOT_PATH . '/app/views/contribute/word.php';
    }

    /**
     * Process word contribution
     */
    public function submitWord() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('/contribute/word', 'Accès non autorisé.');
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
            'part_of_speech' => $_POST['word_type'] ?? '',
            'example_tfng' => trim($_POST['example_tfng'] ?? ''),
            'example_lat' => trim($_POST['example_lat'] ?? '')
        ];

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

        if (empty($data['word_tfng']) || empty($data['word_lat']) || empty($data['translation_fr'])) {
            $this->redirectWithError('/contribute/word', "Veuillez remplir tous les champs obligatoires.");
        }

        $contribId = $this->contributionModel->create($_SESSION['user_id'], 'word', $data, $action, $targetId, $contentBefore);

        if ($contribId) {
            $this->redirectWith('/user/contributions', "Votre contribution a été soumise et sera examinée par un modérateur. Merci !");
        } else {
            $this->redirectWithError('/contribute/word', "Une erreur est survenue lors de la soumission.");
        }
    }

    /**
     * Show form to contribute an example
     */
    public function showExampleForm() {
        $wordId = $_GET['word_id'] ?? null;
        if (!$wordId) $this->redirectWith('/', '');

        $wordModel = new Word($this->pdo);
        $word = $wordModel->find($wordId);
        if (!$word) $this->redirectWith('/', '');

        $page_title = "Ajouter un exemple - Amawal";
        include ROOT_PATH . '/app/views/contribute/example.php';
    }

    /**
     * Process example contribution
     */
    public function submitExample() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

        $data = [
            'word_id' => $_POST['word_id'],
            'example_tfng' => trim($_POST['example_tfng'] ?? ''),
            'example_lat' => trim($_POST['example_lat'] ?? ''),
            'translation_fr' => trim($_POST['translation_fr'] ?? '')
        ];

        if (empty($data['example_tfng']) || empty($data['example_lat']) || empty($data['translation_fr'])) {
            $this->redirectWithError('/contribute/example?word_id=' . $data['word_id'], "Veuillez remplir tous les champs.");
        }

        $contribId = $this->contributionModel->create($_SESSION['user_id'], 'example', $data);

        if ($contribId) {
            $this->redirectWith('/user/contributions', "Exemple soumis avec succès !");
        } else {
            $this->redirectWithError('/user/contributions', "Erreur lors de la soumission.");
        }
    }
}
