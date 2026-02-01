<?php

namespace App\Core\Middleware;

use PDO;

class RateLimitMiddleware {
    private $pdo;
    private $maxAttempts = 5;
    private $windowSeconds = 900; // 15 minutes

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->ensureTablesExist();
    }

    /**
     * Create callable for router middleware
     */
    public static function loginAttempts($pdo) {
        return function() use ($pdo) {
            $middleware = new self($pdo);
            $middleware->checkLoginAttempts();
        };
    }

    /**
     * Check if IP has exceeded login attempts
     */
    public function checkLoginAttempts() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Count recent attempts
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE ip_address = ? 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $stmt->execute([$ip]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['attempts'] >= $this->maxAttempts) {
            http_response_code(429);
            header('Retry-After: 900');
            die(json_encode([
                'success' => false,
                'error' => 'Too many login attempts. Please try again in 15 minutes.'
            ]));
        }

        // Record this attempt
        $stmt = $this->pdo->prepare("
            INSERT INTO login_attempts (ip_address, attempted_at) 
            VALUES (?, NOW())
        ");
        $stmt->execute([$ip]);

        return true;
    }

    /**
     * Clear attempts for successful login
     */
    public function clearAttempts() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt = $this->pdo->prepare("
            DELETE FROM login_attempts 
            WHERE ip_address = ? 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
        ");
        $stmt->execute([$ip]);
    }

    /**
     * Ensure login_attempts table exists
     */
    private function ensureTablesExist() {
        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS login_attempts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    ip_address VARCHAR(45) NOT NULL,
                    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_ip_time (ip_address, attempted_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            error_log("Failed to create login_attempts table: " . $e->getMessage());
        }
    }
}
