<?php

class SitemapController extends BaseController {
    private $itemsPerPage = 5000;

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    /**
     * Master Search Index (sitemap.xml)
     */
    public function index() {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Main pages sitemap
        $this->addIndexUrl(BASE_URL . '/sitemap-main.xml');

        // Words sitemaps (Chunked)
        $totalWords = $this->pdo->query("SELECT COUNT(*) FROM words")->fetchColumn();
        $wordChunks = ceil($totalWords / $this->itemsPerPage);

        for ($i = 1; $i <= $wordChunks; $i++) {
            $this->addIndexUrl(BASE_URL . "/sitemap-words-$i.xml");
        }

        // Proverbs sitemap
        $this->addIndexUrl(BASE_URL . '/sitemap-proverbs.xml');

        echo '</sitemapindex>';
    }

    /**
     * Main Pages Sitemap (Static + Quizzes)
     */
    public function main() {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $this->addUrl(BASE_URL . '/', '1.0', 'daily');
        $this->addUrl(BASE_URL . '/proverbs', '0.8', 'weekly');
        $this->addUrl(BASE_URL . '/quizzes', '0.8', 'weekly');
        $this->addUrl(BASE_URL . '/about', '0.5', 'monthly');
        $this->addUrl(BASE_URL . '/contact', '0.5', 'monthly');

        echo '</urlset>';
    }

    /**
     * Chunked Words Sitemap
     */
    public function words($params) {
        $chunk = isset($params['id']) ? (int)$params['id'] : 1;
        $offset = ($chunk - 1) * $this->itemsPerPage;

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        try {
            $stmt = $this->pdo->prepare("SELECT id, word_lat FROM words LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $this->itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $url = BASE_URL . '/word/' . urlencode($row['word_lat']) . '-' . $row['id'];
                $this->addUrl($url, '0.8', 'monthly');
            }
        } catch (Exception $e) {
            error_log("Sitemap generation error: " . $e->getMessage());
        }

        echo '</urlset>';
    }

    /**
     * Proverbs Sitemap
     */
    public function proverbs() {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $stmt = $this->pdo->query("SELECT id FROM proverbs");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $url = BASE_URL . '/proverb/' . $row['id'];
            $this->addUrl($url, '0.7', 'monthly');
        }

        echo '</urlset>';
    }

    private function addIndexUrl($url) {
        echo '<sitemap>';
        echo '<loc>' . htmlspecialchars($this->ensureAbsolute($url)) . '</loc>';
        echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
        echo '</sitemap>';
    }

    private function addUrl($url, $priority, $changefreq) {
        echo '<url>';
        echo '<loc>' . htmlspecialchars($this->ensureAbsolute($url)) . '</loc>';
        echo '<changefreq>' . $changefreq . '</changefreq>';
        echo '<priority>' . $priority . '</priority>';
        echo '</url>';
    }

    private function ensureAbsolute($url) {
        if (strpos($url, 'http') !== 0) {
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            return $scheme . '://' . $host . $url;
        }
        return $url;
    }
}
