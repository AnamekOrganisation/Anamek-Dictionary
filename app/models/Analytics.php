<?php

class Analytics {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Record a site visit with unique visitor detection
     */
    public function recordVisit($data = []) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        // Creative Unique Visitor Detection: Hash of IP + UA + Today's Date
        $visitorHash = hash('sha256', $ip . $ua . date('Y-m-d'));

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO site_visits (visitor_hash, ip_address, user_agent, page_url, referrer)
                VALUES (:hash, :ip, :ua, :url, :ref)
            ");
            $stmt->execute([
                'hash' => $visitorHash,
                'ip'   => $ip, // We store IP but unique count is based on hash
                'ua'   => $ua,
                'url'  => $url,
                'ref'  => $referrer
            ]);
        } catch (Exception $e) {
            error_log("Analytics Error: " . $e->getMessage());
        }
    }

    /**
     * Record a search query
     */
    public function recordSearch($query, $resultsCount = 0, $lang = 'tfng') {
        if (empty($query)) return;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO search_analytics (query, results_count, language)
                VALUES (:query, :count, :lang)
            ");
            $stmt->execute([
                'query' => $query,
                'count' => $resultsCount,
                'lang'  => $lang
            ]);
        } catch (Exception $e) {
            error_log("Search Analytics Error: " . $e->getMessage());
        }
    }

    /**
     * Get Daily Unique Visitors for the last X days
     */
    public function getDailyUniqueVisitors($days = 30) {
        $sql = "
            SELECT DATE(visited_at) as date, COUNT(DISTINCT visitor_hash) as count
            FROM site_visits
            WHERE visited_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(visited_at)
            ORDER BY date ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Total Unique Visitors for today
     */
    public function getTodayUniqueCount() {
        $stmt = $this->pdo->query("
            SELECT COUNT(DISTINCT visitor_hash) 
            FROM site_visits 
            WHERE DATE(visited_at) = CURDATE()
        ");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get Popular Searches
     */
    public function getPopularSearches($limit = 10) {
        $sql = "
            SELECT query, COUNT(*) as count
            FROM search_analytics
            GROUP BY query
            ORDER BY count DESC
            LIMIT :limit
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Top Pages
     */
    public function getTopPages($limit = 10) {
        $sql = "
            SELECT page_url, COUNT(*) as count
            FROM site_visits
            GROUP BY page_url
            ORDER BY count DESC
            LIMIT :limit
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
