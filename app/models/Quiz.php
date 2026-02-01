<?php

/**
 * Quiz Model
 * Handles quiz management, questions, and user results
 */
class Quiz {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all active quizzes
     * @param string $difficulty Optional difficulty filter
     * @param int $categoryId Optional category filter
     * @return array List of quizzes
     */
    public function getAll($difficulty = null, $categoryId = null) {
        $sql = "SELECT q.*, u.username as creator_name, c.category_name_fr as category_name 
                FROM quizzes q
                LEFT JOIN users u ON q.created_by = u.id
                LEFT JOIN word_categories c ON q.category_id = c.id
                WHERE q.is_active = 1";
        
        $params = [];
        if ($difficulty) {
            $sql .= " AND q.difficulty_level = ?";
            $params[] = $difficulty;
        }
        if ($categoryId) {
            $sql .= " AND q.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY q.is_featured DESC, q.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get quiz by ID
     * @param int $id Quiz ID
     * @return array|false Quiz data or false
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT q.*, c.category_name_fr as category_name 
            FROM quizzes q 
            LEFT JOIN word_categories c ON q.category_id = c.id
            WHERE q.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get questions for a specific quiz
     * @param int $quizId Quiz ID
     * @return array Questions
     */
    public function getQuestions($quizId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quiz_questions 
            WHERE quiz_id = ? 
            ORDER BY display_order ASC, id ASC
        ");
        $stmt->execute([$quizId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode options JSON
        foreach ($questions as &$question) {
            if ($question['options']) {
                $question['options'] = json_decode($question['options'], true);
            }
        }
        
        return $questions;
    }

    /**
     * Record a quiz attempt result
     * @param array $data Result data
     * @return int|false Last insert ID or false
     */
    public function saveResult($data) {
        try {
            $sql = "INSERT INTO user_quiz_results 
                    (user_id, quiz_id, score, total_questions, correct_answers, percentage, time_taken_seconds, answers, passed, completed_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['user_id'],
                $data['quiz_id'],
                $data['score'],
                $data['total_questions'],
                $data['correct_answers'],
                $data['percentage'],
                $data['time_taken_seconds'] ?? 0,
                isset($data['answers']) ? json_encode($data['answers']) : null,
                $data['passed'] ? 1 : 0
            ]);
            
            $resultId = $this->pdo->lastInsertId();

            // Award points if passed
            if ($data['passed']) {
                $isDaily = isset($data['is_daily']) && $data['is_daily'];
                $points = $isDaily ? 20 : 10; // Bonus for daily challenge
                $stmt = $this->pdo->prepare("UPDATE users SET contribution_points = contribution_points + ? WHERE id = ?");
                $stmt->execute([$points, $data['user_id']]);
            }

            return $resultId;
        } catch (Exception $e) {
            error_log("Error saving quiz result: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's best result for a quiz
     * @param int $userId User ID
     * @param int $quizId Quiz ID
     * @return array|false Result or false
     */
    public function getUserBestResult($userId, $quizId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_quiz_results 
            WHERE user_id = ? AND quiz_id = ? 
            ORDER BY percentage DESC, time_taken_seconds ASC 
            LIMIT 1
        ");
        $stmt->execute([$userId, $quizId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get quiz leaderboard
     * @param int $quizId Quiz ID
     * @param int $limit Maximum number of entries
     * @return array Leaderboard
     */
    public function getLeaderboard($quizId = null, $limit = 10) {
        $sql = "SELECT r.*, u.username, u.full_name, q.title_fr as quiz_title
                FROM user_quiz_results r
                JOIN users u ON r.user_id = u.id
                JOIN quizzes q ON r.quiz_id = q.id";
        
        $params = [];
        if ($quizId) {
            $sql .= " WHERE r.quiz_id = ?";
            $params[] = $quizId;
        }
        
        $sql .= " ORDER BY r.percentage DESC, r.time_taken_seconds ASC LIMIT ?";
        $params[] = (int)$limit;
        
        $stmt = $this->pdo->prepare($sql);
        // Bind limit as integer
        $stmt->bindValue(count($params), $limit, PDO::PARAM_INT);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific quiz result
     * @param int $id Result ID
     * @return array|false Result or false
     */
    public function getResult($id) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, q.title_fr as quiz_title, u.username 
            FROM user_quiz_results r
            JOIN quizzes q ON r.quiz_id = q.id
            JOIN users u ON r.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['answers']) {
            $result['answers'] = json_decode($result['answers'], true);
        }
        
        return $result;
    }


    /**
     * Get quiz categories
     * @return array Categories
     */
    public function getCategories() {
        $stmt = $this->pdo->query("
            SELECT DISTINCT c.id, c.category_name_fr as name 
            FROM word_categories c
            JOIN quizzes q ON q.category_id = c.id
            WHERE q.is_active = 1
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin: Create a new quiz
     */
    public function create($data) {
        $sql = "INSERT INTO quizzes (title_fr, description_fr, category_id, difficulty_level, estimated_time, passing_score, is_active, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['title_fr'],
            $data['description_fr'],
            $data['category_id'],
            $data['difficulty_level'],
            $data['estimated_time'],
            $data['passing_score'],
            $data['is_active'] ?? 1,
            $data['created_by']
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Admin: Update an existing quiz
     */
    public function update($id, $data) {
        $sql = "UPDATE quizzes SET 
                title_fr = ?, 
                description_fr = ?, 
                category_id = ?, 
                difficulty_level = ?, 
                estimated_time = ?, 
                passing_score = ?, 
                is_active = ?,
                is_featured = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title_fr'],
            $data['description_fr'],
            $data['category_id'],
            $data['difficulty_level'],
            $data['estimated_time'],
            $data['passing_score'],
            $data['is_active'],
            $data['is_featured'] ?? 0,
            $id
        ]);
    }

    /**
     * Admin: Delete a quiz and its questions
     */
    public function delete($id) {
        try {
            $this->pdo->beginTransaction();
            
            // Delete questions first (though DB might have cascading deletes, safer to be explicit)
            $stmt = $this->pdo->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
            $stmt->execute([$id]);
            
            $stmt = $this->pdo->prepare("DELETE FROM quizzes WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Admin: Add a question to a quiz
     */
    public function addQuestion($data) {
        $sql = "INSERT INTO quiz_questions (quiz_id, question_text_fr, question_text_tfng, options, correct_answer, points, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['quiz_id'],
            $data['question_text_fr'],
            $data['question_text_tfng'] ?? null,
            json_encode($data['options']),
            $data['correct_answer'],
            $data['points'] ?? 1,
            $data['display_order'] ?? 0
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Admin: Update a question
     */
    public function updateQuestion($id, $data) {
        $sql = "UPDATE quiz_questions SET 
                question_text_fr = ?, 
                question_text_tfng = ?, 
                options = ?, 
                correct_answer = ?, 
                points = ?, 
                display_order = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['question_text_fr'],
            $data['question_text_tfng'] ?? null,
            json_encode($data['options']),
            $data['correct_answer'],
            $data['points'],
            $data['display_order'],
            $id
        ]);
    }

    /**
     * Admin: Delete a question
     */
    public function deleteQuestion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM quiz_questions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Admin: Get all categories (not just active ones)
     */
    public function getAllCategories() {
        $stmt = $this->pdo->query("SELECT id, category_name_fr as name FROM word_categories ORDER BY category_name_fr ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
