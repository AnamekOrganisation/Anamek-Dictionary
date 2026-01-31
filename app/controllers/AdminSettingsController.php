<?php

class AdminSettingsController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_social'])) {
                foreach ($_POST['social'] as $platform => $url) {
                    if (filter_var($url, FILTER_VALIDATE_URL) || empty($url)) {
                        $this->updateSocialLink($platform, $url);
                    }
                }
            }
            if (isset($_POST['update_ads'])) {
                require_once ROOT_PATH . '/app/models/Setting.php';
                $settingModel = new \App\Models\Setting($this->pdo);
                $settingModel->set('google_ads_slot_home', $_POST['ad_slot_home'] ?? '');
                $settingModel->set('google_ads_client', $_POST['ad_client'] ?? '');
            }
            header('Location: ' . BASE_URL . '/admin/settings');
            exit;
        }

        $socialLinks = $this->getSocialLinks();
        require_once ROOT_PATH . '/app/models/Setting.php';
        $settingModel = new \App\Models\Setting($this->pdo);
        $adSettings = [
            'slot_home' => $settingModel->get('google_ads_slot_home', ''),
            'client' => $settingModel->get('google_ads_client', '')
        ];

        $page_title = 'Paramètres';
        require_once ROOT_PATH . '/app/views/admin/settings.php';
    }

    private function getSocialLinks() {
        return $this->pdo->query("SELECT platform, url FROM social_links")->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    private function updateSocialLink($platform, $url) {
        $stmt = $this->pdo->prepare("UPDATE social_links SET url = ? WHERE platform = ?");
        $stmt->execute([$url, $platform]);
    }
}
