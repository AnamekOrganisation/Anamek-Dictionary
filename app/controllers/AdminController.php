<?php

class AdminController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function dashboard() {
        require_once ROOT_PATH . '/app/models/Analytics.php';
        $analyticsModel = new Analytics($this->pdo);

        try {
            $counts = $this->getCounts();
            $socialLinks = $this->getSocialLinks();
            
            $dailyVisits = $analyticsModel->getDailyUniqueVisitors(30);
            $popularSearches = $analyticsModel->getPopularSearches(10);
            $activeUsers = $analyticsModel->getTodayUniqueCount();
            
            require_once ROOT_PATH . '/app/models/Contribution.php';
            $contributionModel = new Contribution($this->pdo);
            $pendingCount = count($contributionModel->findPending());
            
            $recentActivity = $this->getRecentActivity();

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
            $pendingCount = 0;
            $recentActivity = [];
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

    public function analytics() {
        require_once ROOT_PATH . '/app/models/Analytics.php';
        $analyticsModel = new Analytics($this->pdo);
        $dailyVisits = $analyticsModel->getDailyUniqueVisitors(30);
        $popularSearches = $analyticsModel->getPopularSearches(20);
        $topPages = $analyticsModel->getTopPages(20);
        
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
        require_once ROOT_PATH . '/app/models/Contribution.php';
        $contrib = new Contribution($this->pdo);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $id = $_POST['id'];
            try {
                if ($_POST['action'] === 'approve') {
                    $contrib->approve($id, $_SESSION['user_id'], $_POST['notes'] ?? '');
                    $this->redirectWith('/admin/reviews', 'Contribution approuvée avec succès.');
                } elseif ($_POST['action'] === 'reject') {
                    $contrib->reject($id, $_SESSION['user_id'], $_POST['notes'] ?? '');
                    $this->redirectWith('/admin/reviews', 'Contribution rejetée.');
                }
            } catch (Exception $e) {
                $this->redirectWithError('/admin/reviews', 'Erreur : ' . $e->getMessage());
            }
        }
        
        $pending = $contrib->findPending();
        $page_title = 'Révisions en attente';
        require_once ROOT_PATH . '/app/views/admin/review-contributions.php';
    }
}
