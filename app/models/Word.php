<?php

class Word {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Increment search count for a word
     * (Maintain existing functionality until AnalyticsService is created)
     */
    public function incrementSearchCount($id) {
        $check = $this->pdo->prepare("SELECT id FROM words WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) return false;

        $sql = "INSERT INTO recent_searches (word_id, search_count, last_searched) 
                VALUES (?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE search_count = search_count + 1, last_searched = NOW()";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
