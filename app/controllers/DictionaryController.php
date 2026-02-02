<?php

use App\Core\Cache;

class DictionaryController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }


    public static function getSharedData($pdo = null) {
        if ($pdo === null) {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
        }

        $wordRepo = new \App\Repositories\WordRepository($pdo);
        $cache = new Cache();

        // Get counts (cache these for 1 hour)
        $cacheKeyCounts = 'site_counts';
        $counts = $cache->get($cacheKeyCounts, 3600);
        if ($counts === false) {
             $counts = [
                'words' => $wordRepo->countAll(),
                'proverbs' => $pdo->query("SELECT COUNT(*) FROM proverbs")->fetchColumn()
            ];
            $cache->set($cacheKeyCounts, $counts);
        }

        // Get Word of the Day
        $cacheKeyWord = 'daily_word_' . date('Y-m-d');
        $wordOfTheDay = $cache->get($cacheKeyWord, 86400);
        if ($wordOfTheDay === false) {
            $wordOfTheDay = $wordRepo->getRandomWithDefinition();
            if ($wordOfTheDay) {
                // Pre-hydrate for the widget
                $wordRepo->hydrateRelations($wordOfTheDay);
                $cache->set($cacheKeyWord, $wordOfTheDay);
            }
        }

        // Get newest words
        $recentWords = $wordRepo->getRecentWords(6);
        
        // Get trending words
        $trendingWords = $wordRepo->getRecentSearches(6);

        // Ad Settings
        require_once ROOT_PATH . '/app/models/Setting.php';
        $settingModel = new \App\Models\Setting($pdo);
        $adSlotHome = $settingModel->get('google_ads_slot_home', '0000000000');

        return [
            'wordCount' => $counts['words'],
            'proverbCount' => $counts['proverbs'],
            'wordOfTheDay' => $wordOfTheDay,
            'recentWords' => $recentWords,
            'trendingWords' => $trendingWords,
            'adSlotHome' => $adSlotHome
        ];
    }

    public function home() {
        if (isset($_GET['clear_cache'])) {
            array_map('unlink', glob(ROOT_PATH . '/cache/*.cache'));
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $commonData = self::getSharedData($this->pdo);
        extract($commonData);

        $proverbModel = new Proverb($this->pdo);
        $cache = new Cache();

        // Cache Proverb of the Day for 24 hours
        $cacheKeyProverb = 'daily_proverb_' . date('Y-m-d');
        $proverbOfTheDay = $cache->get($cacheKeyProverb, 86400);
        if ($proverbOfTheDay === false) {
            $proverbOfTheDay = $proverbModel->getRandom();
            if ($proverbOfTheDay) {
                $cache->set($cacheKeyProverb, $proverbOfTheDay);
            }
        }

        $preLoadedWord = $wordOfTheDay;
        $isHomepage = true;
        
        include ROOT_PATH . '/app/views/home.php';
    }

    public function showWord($params) {
        $id = $params['id'] ?? 0;
        
        $wordRepo = new \App\Repositories\WordRepository($this->pdo);
        $word = $wordRepo->find($id);

        $commonData = self::getSharedData($this->pdo);
        extract($commonData);
        $featuredWord = $wordOfTheDay; // For sidebar compatibility

        if (!$word) {
            $page_title = __('Word not found');
            $page_description = __('Désolé, nous n\'avons trouvé aucun résultat pour votre recherche.');
            $variants = [];
            $word = null;
            
            include ROOT_PATH . '/app/views/word-page.php';
            return;
        }

        // --- SEO Enhancement ---
        $wordLat = e($word['word_lat']);
        $wordTfng = e($word['word_tfng']);
        $translation = e($word['translation_fr']);
        
        $page_title = "$wordTfng ($wordLat) - $translation | Dictionnaire Anamek";
        
        // Build detailed description
        $desc = "Découvrez la signification de '$wordTfng' ($wordLat) en français : $translation.";
        if (!empty($word['definition_fr'])) {
            $desc .= " " . mb_strimwidth(strip_tags($word['definition_fr']), 0, 150, "...");
        } elseif (!empty($word['part_of_speech'])) {
            $desc .= " Nature grammaticale : " . $word['part_of_speech'] . ".";
        }
        $page_description = $desc;

        // Keywords
        $page_keywords = "$wordTfng, $wordLat, $translation, dictionnaire amazigh, tamazight, traduction, amawal";
        if (!empty($word['root_lat'])) {
            $page_keywords .= ", racine " . $word['root_lat'];
        }

        // Open Graph
        $og_title = "$wordTfng ($wordLat) : Définition et Traduction";
        $og_description = $page_description;
        
        $params_id = $params['id'] ?? '';
        // If the URL contains a specific ID in Slug-ID format (e.g. tarrist-15976), show ONLY that word.
        if (preg_match('/-(\d+)$/', $params_id)) {
            $variants = [$word];
        } else {
            // If the URL is just a slug (e.g. 'tarrist' or 'pays'), show all variants.
            // Use the original search term to find all matches (including homonyms or french matches)
            $searchSlug = urldecode($params_id);
            $variants = $wordRepo->findAllByText($searchSlug);
            
            if (empty($variants)) {
                $variants = [$word]; // Fallback
            }
        }

        // Analytics (non-blocking)
        $wordModel = new Word($this->pdo);
        $wordModel->incrementSearchCount($word['id']);

        include ROOT_PATH . '/app/views/word-page.php';
    }

    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        if (empty($query)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Redirect to the word page logic
        header('Location: ' . BASE_URL . '/word/' . urlencode($query));
        exit;
    }

    public function proverbs() {
        if (isset($_GET['action']) && $_GET['action'] === 'index') {
            header('Location: ' . BASE_URL . '/proverbs');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 12;
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';

        $proverbModel = new Proverb($this->pdo);

        if (!empty($query)) {
            $totalProverbs = $proverbModel->countSearch($query);
            $proverbs = $proverbModel->search($query, $page, $perPage);
            
            // Record in analytics (Accurate & Optimized)
            require_once ROOT_PATH . '/app/models/Analytics.php';
            $analytics = new Analytics($this->pdo);
            $analytics->recordSearch("[Proverb] " . $query, $totalProverbs, 'multi');
        } else {
            $totalProverbs = $proverbModel->countAll();
            $proverbs = $proverbModel->getPaginated($page, $perPage);
        }

        $totalPages = ceil($totalProverbs / $perPage);

        include ROOT_PATH . '/app/views/proverbs.php';
    }

    public function showProverb($params) {
        $id = $params['id'] ?? 0;
        $proverbModel = new Proverb($this->pdo);
        $proverb = $proverbModel->find($id);

        $commonData = self::getSharedData($this->pdo);
        extract($commonData);
        $featuredWord = $wordOfTheDay;

        if (!$proverb) {
            $page_title = __('Proverb not found');
            include ROOT_PATH . '/app/views/home.php';
            return;
        }

        $page_title = __('Proverb') . " #" . $proverb['id'] . " - Amawal";
        include ROOT_PATH . '/app/views/proverb-page.php';
    }

    public function contact() {
        $page_title = __('Contactez-nous') . " - Amawal";
        $page_description = "N'hésitez pas à nous contacter pour toute question, suggestion ou signalement d'erreur sur le dictionnaire Anamek.";
        include ROOT_PATH . '/app/views/contact.php';
    }

    public function submitContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        // Verify CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = "Erreur de sécurité. Veuillez réessayer.";
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $_SESSION['flash_error'] = "Tous les champs obligatoires doivent être remplis.";
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Adresse email invalide.";
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $_SESSION['flash_message'] = "Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.";
            header('Location: ' . BASE_URL . '/contact');
        } catch (PDOException $e) {
            error_log("Contact form error: " . $e->getMessage());
            $_SESSION['flash_error'] = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer plus tard.";
            header('Location: ' . BASE_URL . '/contact');
        }
        exit;
    }

    public function about() {
        include ROOT_PATH . '/app/views/about.php';
    }

    public function privacy() {
        $page_title = __('Politique de confidentialité') . " - Amawal";
        include ROOT_PATH . '/app/views/legal/privacy.php';
    }

    public function terms() {
        $page_title = __('Conditions d’utilisation') . " - Amawal";
        include ROOT_PATH . '/app/views/legal/terms.php';
    }

    public function cookies() {
        $page_title = __('Politique relative aux cookies') . " - Amawal";
        include ROOT_PATH . '/app/views/legal/cookies.php';
    }
}
