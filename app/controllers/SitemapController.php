<?php

class SitemapController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Static pages
        $this->addUrl(BASE_URL . '/', '1.0', 'daily');
        $this->addUrl(BASE_URL . '/proverbs', '0.8', 'weekly');
        $this->addUrl(BASE_URL . '/contact', '0.5', 'monthly');

        // Dynamic Words
        $stmt = $this->pdo->query("SELECT id, word_lat FROM words");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $url = BASE_URL . '/word/' . urlencode($row['word_lat']) . '-' . $row['id'];
            $this->addUrl($url, '0.9', 'monthly');
        }

        echo '</urlset>';
    }

    private function addUrl($url, $priority, $changefreq) {
        // Ensure full URL
        if (strpos($url, 'http') !== 0) {
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $url = $scheme . '://' . $host . $url;
        }

        echo '<url>';
        echo '<loc>' . htmlspecialchars($url) . '</loc>';
        echo '<changefreq>' . $changefreq . '</changefreq>';
        echo '<priority>' . $priority . '</priority>';
        echo '</url>';
    }
}
