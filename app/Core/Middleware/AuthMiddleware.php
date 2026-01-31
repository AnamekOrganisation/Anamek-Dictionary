<?php

namespace App\Core\Middleware;

class AuthMiddleware {
    /**
     * Ensure user is logged in
     */
    public static function auth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Ensure user is an admin
     */
    public static function admin() {
        self::auth(); // Must be logged in first
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    /**
     * Validate CSRF for POST requests
     */
    public static function csrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                http_response_code(403);
                die('Security check failed: Invalid CSRF token.');
            }
        }
    }
}
