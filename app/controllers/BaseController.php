<?php

use App\Core\Cache;

abstract class BaseController {
    protected $pdo;
    protected $cache;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->cache = new Cache();
    }

    /**
     * Send a JSON response and exit
     */
    protected function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect with a session message
     */
    protected function redirectWith($url, $message, $type = 'success') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    /**
     * Redirect with error message
     */
    protected function redirectWithError($url, $message) {
        $this->redirectWith($url, $message, 'danger');
    }

    /**
     * Get common data for views (sidebar, stats, etc.)
     */
    protected function getCommonData() {
        return DictionaryController::getSharedData($this->pdo);
    }
}
