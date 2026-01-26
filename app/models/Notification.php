<?php

/**
 * Notification Model
 * Manages system notifications for users
 */
class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a notification
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return bool
     */
    public function create($userId, $type, $title, $message, $link = null) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notifications (user_id, notification_type, title, message, link, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        return $stmt->execute([$userId, $type, $title, $message, $link]);
    }

    /**
     * Get unread notifications for a user
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnread($userId, $limit = 10) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all notifications for a user (paginated)
     * @param int $userId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAll($userId, $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare(
            "SELECT * FROM notifications 
             WHERE user_id = ? 
             ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $perPage, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark a notification as read
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function markAsRead($id, $userId) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Mark all notifications as read for a user
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    /**
     * Count unread notifications
     * @param int $userId
     * @return int
     */
    public function countUnread($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
