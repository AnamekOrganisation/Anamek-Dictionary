<?php

namespace App\Models;

use PDO;
use Exception;

/**
 * SECURITY: Audit Log Model
 * Tracks security-relevant events: admin actions, failed auth, sensitive operations
 */
class AuditLog {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->ensureTableExists();
    }

    /**
     * Ensure audit_logs table exists with proper schema
     */
    private function ensureTableExists() {
        $sql = "CREATE TABLE IF NOT EXISTS audit_logs (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NULL,
            action VARCHAR(255) NOT NULL,
            resource_type VARCHAR(100) NOT NULL,
            resource_id INT NULL,
            old_values JSON,
            new_values JSON,
            status VARCHAR(50) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_timestamp (timestamp),
            INDEX idx_resource (resource_type, resource_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $this->pdo->exec($sql);
        } catch (Exception $e) {
            error_log("Failed to create audit_logs table: " . $e->getMessage());
        }
    }

    /**
     * Log an action
     * 
     * @param string $action Action name (e.g., 'DELETE_WORD', 'LOGIN_FAILED')
     * @param string $resourceType Resource type (e.g., 'word', 'user', 'auth')
     * @param int|null $resourceId Resource ID
     * @param string $status Status ('success', 'failure', 'attempted')
     * @param array|null $oldValues Previous values (for updates)
     * @param array|null $newValues New values (for updates/creates)
     * @return bool Success status
     */
    public function log(
        $action,
        $resourceType,
        $resourceId = null,
        $status = 'success',
        $oldValues = null,
        $newValues = null
    ) {
        try {
            $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
            $ipAddress = $this->getClientIp();
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

            $sql = "INSERT INTO audit_logs (
                user_id, action, resource_type, resource_id,
                old_values, new_values, status, ip_address, user_agent
            ) VALUES (
                :user_id, :action, :resource_type, :resource_id,
                :old_values, :new_values, :status, :ip_address, :user_agent
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'status' => $status,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Audit log error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get audit logs for a user
     * 
     * @param int $userId User ID
     * @param int $limit Limit number of results
     * @return array Audit logs
     */
    public function getUserLogs($userId, $limit = 100) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM audit_logs WHERE user_id = ? ORDER BY timestamp DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent failed authentication attempts
     * 
     * @param int $minutes Look back this many minutes
     * @return array Failed auth attempts
     */
    public function getFailedAuthAttempts($minutes = 30) {
        $sql = "SELECT * FROM audit_logs 
                WHERE action = 'LOGIN_FAILED' 
                AND status = 'failure'
                AND timestamp > DATE_SUB(NOW(), INTERVAL ? MINUTE)
                ORDER BY timestamp DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$minutes]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get suspicious activity (multiple failed attempts from same IP)
     * 
     * @return array Suspicious activity
     */
    public function getSuspiciousActivity() {
        $sql = "SELECT ip_address, COUNT(*) as attempt_count, MAX(timestamp) as last_attempt
                FROM audit_logs
                WHERE action = 'LOGIN_FAILED' 
                AND timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                GROUP BY ip_address
                HAVING attempt_count > 5
                ORDER BY attempt_count DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get admin actions for audit trail
     * 
     * @param int $limit Limit results
     * @return array Admin actions
     */
    public function getAdminActions($limit = 100) {
        $adminActions = ['DELETE_WORD', 'UPDATE_WORD', 'DELETE_PROVERB', 'UPDATE_USER', 'DELETE_USER'];
        $placeholders = implode(',', array_fill(0, count($adminActions), '?'));

        $sql = "SELECT * FROM audit_logs
                WHERE action IN ($placeholders)
                ORDER BY timestamp DESC
                LIMIT ?";

        $stmt = $this->pdo->prepare($sql);
        $params = array_merge($adminActions, [$limit]);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * SECURITY: Get client IP address (considering proxies)
     * 
     * @return string Client IP address
     */
    private function getClientIp() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '0.0.0.0';
    }
}
