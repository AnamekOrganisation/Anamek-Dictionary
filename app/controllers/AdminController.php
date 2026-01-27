<?php

class AdminController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    public function dashboard() {
        $this->checkAuth();
        
        require_once ROOT_PATH . '/app/models/Analytics.php';
        $analyticsModel = new Analytics($this->pdo);

        try {
            $counts = $this->getCounts();
            $socialLinks = $this->getSocialLinks();
            
            // Analytics Data (Accurate Unique Visitors)
            $dailyVisits = $analyticsModel->getDailyUniqueVisitors(30);
            $popularSearches = $analyticsModel->getPopularSearches(10);
            $activeUsers = $analyticsModel->getTodayUniqueCount();
            
            // Contributions
            require_once ROOT_PATH . '/app/models/Contribution.php';
            $contributionModel = new Contribution($this->pdo);
            $pendingCount = count($contributionModel->findPending());
            
            // Recent Activity Feed
            $recentActivity = $this->getRecentActivity();

            // Prepare Chart.js data
            $chartLabels = [];
            $chartData = [];
            foreach ($dailyVisits as $visit) {
                $chartLabels[] = date('d M', strtotime($visit['date']));
                $chartData[] = $visit['count'];
            }
            
        } catch (Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            $counts = ['words' => 0, 'proverbs' => 0];
            $socialLinks = [];
            $popularSearches = [];
            $chartLabels = [];
            $chartData = [];
            $activeUsers = 0;
        }

        $page_title = 'Tableau de bord';
        require_once ROOT_PATH . '/app/views/admin/dashboard.php';
    }

    private function getSocialLinks() {
        $result = $this->pdo->query("SELECT platform, url FROM social_links")->fetchAll(PDO::FETCH_KEY_PAIR);
        return $result ?: [];
    }

    private function getRecentActivity($limit = 5) {
        $activity = [];
        
        // Recent Contributions
        $stmt = $this->pdo->prepare("SELECT 'contribution' as type, contribution_type as item, created_at, status FROM user_contributions ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $activity = array_merge($activity, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        usort($activity, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activity, 0, $limit);
    }

    public function getCounts() {
        $counts = ['words' => 0, 'proverbs' => 0];
        try {
            $counts['words'] = (int)$this->pdo->query("SELECT COUNT(*) FROM words")->fetchColumn();
            $counts['proverbs'] = (int)$this->pdo->query("SELECT COUNT(*) FROM proverbs")->fetchColumn();
        } catch (Exception $e) {
            error_log("Error fetching counts: " . $e->getMessage());
        }
        return $counts;
    }
    
    // Kept some search methods for internal use if needed, but they are now in specialized controllers
    public function searchWord($query) {
        $stmt = $this->pdo->prepare("SELECT * FROM words WHERE word_tfng LIKE :q OR word_lat LIKE :q OR translation_fr LIKE :q");
        $stmt->execute(['q' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Remaining methods like pendingReviews, sessions, analytics should also be moved eventually
    public function analytics() {
        $this->checkAuth();
        require_once ROOT_PATH . '/app/models/Analytics.php';
        $analyticsModel = new Analytics($this->pdo);
        $dailyVisits = $analyticsModel->getDailyUniqueVisitors(30);
        $popularSearches = $analyticsModel->getPopularSearches(20);
        $topPages = $analyticsModel->getTopPages(20);
        
        // Prepare Chart data
        $chartLabels = [];
        $chartData = [];
        foreach ($dailyVisits as $visit) {
            $chartLabels[] = date('d M', strtotime($visit['date']));
            $chartData[] = $visit['count'];
        }

        $page_title = 'Statistiques Détaillées';
        require_once ROOT_PATH . '/app/views/admin/analytics.php';
    }

    public function pendingReviews() {
        $this->checkAuth();
        require_once ROOT_PATH . '/app/models/Contribution.php';
        $contrib = new Contribution($this->pdo);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $this->verifyCsrf();
            $id = $_POST['id'];
            if ($_POST['action'] === 'approve') {
                $contrib->approve($id, $_SESSION['user_id'], $_POST['notes'] ?? '');
            } elseif ($_POST['action'] === 'reject') {
                $contrib->reject($id, $_SESSION['user_id'], $_POST['notes'] ?? '');
            }
            header('Location: ' . BASE_URL . '/admin/reviews');
            exit;
        }
        
        $pending = $contrib->findPending();
        $page_title = 'Révisions en attente';
        require_once ROOT_PATH . '/app/views/admin/review-contributions.php';
    }
}
