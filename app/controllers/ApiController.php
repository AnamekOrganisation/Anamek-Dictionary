<?php

use App\Core\Cache;

class ApiController extends BaseController {
    private $wordRepo;
    private $wordModel;
    private $proverbModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->wordRepo = new \App\Repositories\WordRepository($pdo);
        $this->wordModel = new Word($pdo); // Still needed for incrementSearchCount
        $this->proverbModel = new Proverb($pdo);
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        $type = $_GET['stype'] ?? 'start';
        $lang = $_GET['lang'] ?? 'tfng';

        if (empty($query)) {
            $this->jsonResponse([]);
        }

        try {
            $results = $this->wordRepo->search($query, $lang, $type, true);
            
            // Record in analytics (Accurate & Optimized)
            require_once ROOT_PATH . '/app/models/Analytics.php';
            $analytics = new Analytics($this->pdo);
            $analytics->recordSearch($query, count($results), $lang);
            
            $this->jsonResponse($results);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getWordOfTheDay() {
        try {
            $cache = new Cache();
            $cacheKey = 'daily_word_' . date('Y-m-d');
            $word = $cache->get($cacheKey, 86400); // Cache for 24 hours

            if ($word === false) {
                $word = $this->wordRepo->getRandomWithDefinition();
                if ($word) {
                    $this->wordRepo->hydrateRelations($word);
                    $cache->set($cacheKey, $word);
                }
            }

            if (!$word) {
                $this->jsonResponse(['success' => false, 'message' => 'Aucun mot trouvé']);
            }
            $this->jsonResponse(['success' => true, 'data' => $word]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function getProverbOfTheDay() {
        try {
            $cache = new Cache();
            $cacheKey = 'daily_proverb_' . date('Y-m-d');
            $proverb = $cache->get($cacheKey, 86400); // Cache for 24 hours

            if ($proverb === false) {
                $proverb = $this->proverbModel->getRandom();
                if ($proverb) {
                    $cache->set($cacheKey, $proverb);
                }
            }

            if (!$proverb) {
                $this->jsonResponse(['success' => false, 'message' => 'Aucun proverbe trouvé']);
            }
            $this->jsonResponse(['success' => true, 'data' => $proverb]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function recentSearches() {
        try {
            $results = $this->wordRepo->getRecentSearches();
            $this->jsonResponse([
                'success' => true, 
                'data' => $results ?: [],
                'message' => empty($results) ? 'Aucune recherche récente' : null
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addRecentSearch() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $wordId = $data['word_id'] ?? null;

            if (!$wordId) {
                $this->jsonResponse(['success' => false, 'message' => 'Word ID required'], 400);
            }

            $success = $this->wordModel->incrementSearchCount($wordId);
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Saved']);
            } else {
                 $this->jsonResponse(['success' => false, 'message' => 'Word not found'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function getWordVariants() {
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
           $this->jsonResponse(['success' => false, 'message' => 'Query required'], 400);
        }

        try {
            // Find all variants/homonyms for this exact text
            $variants = $this->wordRepo->findAllByText($query);
            
            if (empty($variants)) {
                 $this->jsonResponse(['success' => false, 'message' => 'Word not found'], 404);
            }
            
            $this->jsonResponse(['success' => true, 'data' => $variants]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
