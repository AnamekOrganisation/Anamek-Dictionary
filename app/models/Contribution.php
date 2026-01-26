<?php

/**
 * Contribution Model
 * Handles user contributions and admin review process
 */
class Contribution {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new contribution
     * @param int $userId
     * @param string $type ('word', 'definition', 'example', 'pronunciation', 'image', 'correction', 'translation', 'proverb')
     * @param array $content The new content being submitted
     * @param string $action ('create', 'update', 'delete')
     * @param int|null $targetId If update/delete, the ID of the existing record
     * @param array|null $contentBefore If update/delete, the current state of the record
     * @return int|false Contribution ID or false
     */
    public function create($userId, $type, $content, $action = 'create', $targetId = null, $contentBefore = null) {
        try {
            $sql = "INSERT INTO user_contributions (
                        user_id, 
                        contribution_type, 
                        action_type, 
                        target_id, 
                        content_before, 
                        content_after, 
                        status, 
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userId,
                $type,
                $action,
                $targetId,
                $contentBefore ? json_encode($contentBefore) : null,
                json_encode($content)
            ]);

            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Contribution creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a contribution by ID
     * @param int $id
     * @return array|false
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT c.*, u.username, u.email FROM user_contributions c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
        $stmt->execute([$id]);
        $contribution = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($contribution) {
            $contribution['content_before'] = json_decode($contribution['content_before'], true);
            $contribution['content_after'] = json_decode($contribution['content_after'], true);
        }

        return $contribution;
    }

    /**
     * Get contributions by user
     * @param int $userId
     * @param string|null $status filter by status
     * @return array
     */
    public function findByUser($userId, $status = null) {
        $sql = "SELECT * FROM user_contributions WHERE user_id = ?";
        $params = [$userId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['content_after'] = json_decode($row['content_after'], true);
        }

        return $results;
    }

    /**
     * Get pending contributions for admin review
     * @return array
     */
    public function findPending() {
        $sql = "SELECT c.*, u.username FROM user_contributions c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.status = 'pending' 
                ORDER BY c.created_at ASC";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['content_after'] = json_decode($row['content_after'], true);
        }

        return $results;
    }

    /**
     * Approve a contribution
     * @param int $id
     * @param int $adminId
     * @param string|null $notes
     * @param int $points Points to award
     * @return bool
     */
    public function approve($id, $adminId, $notes = null, $points = 10) {
        try {
            $this->pdo->beginTransaction();

            $contribution = $this->find($id);
            if (!$contribution || $contribution['status'] !== 'pending') {
                throw new Exception("Invalid contribution or already processed");
            }

            // Update contribution record
            $stmt = $this->pdo->prepare(
                "UPDATE user_contributions SET 
                    status = 'approved', 
                    reviewed_by = ?, 
                    review_notes = ?, 
                    points_awarded = ?, 
                    reviewed_at = NOW() 
                 WHERE id = ?"
            );
            $stmt->execute([$adminId, $notes, $points, $id]);

            // Update user's total points
            $stmt = $this->pdo->prepare("UPDATE users SET contribution_points = contribution_points + ? WHERE id = ?");
            $stmt->execute([$points, $contribution['user_id']]);

            // Create notification
            require_once ROOT_PATH . '/app/models/Notification.php';
            $notif = new Notification($this->pdo);
            $typeLabel = $contribution['contribution_type'];
            $notif->create(
                $contribution['user_id'], 
                'contribution_approved', 
                'Contribution approuvée', 
                "Votre contribution ({$typeLabel}) a été approuvée ! +{$points} points.", 
                BASE_URL . '/user/contributions'
            );

            // Send email notification
            require_once ROOT_PATH . '/app/helpers/Email.php';
            Email::send(
                $contribution['email'], 
                "Contribution approuvée - Amawal", 
                "<h2>Félicitations !</h2><p>Votre contribution (<b>" . htmlspecialchars($typeLabel) . "</b>) a été approuvée par l'équipe Amawal.</p><p>Vous avez gagné <b>{$points}</b> points de réputation !</p><p><a href='" . BASE_URL . "/user/contributions'>Voir mes contributions</a></p>"
            );

            // Now apply the change to the target tables
            $this->applyApprovedContribution($contribution);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Contribution approval error: " . $e->getMessage());
            throw $e; // Re-throw to let controller handle/display it
        }
    }

    /**
     * Reject a contribution
     * @param int $id
     * @param int $adminId
     * @param string|null $notes
     * @return bool
     */
    public function reject($id, $adminId, $notes = null) {
        $stmt = $this->pdo->prepare(
            "UPDATE user_contributions SET 
                status = 'rejected', 
                reviewed_by = ?, 
                review_notes = ?, 
                reviewed_at = NOW() 
             WHERE id = ? AND status = 'pending'"
        );
        $contribution = $this->find($id);
        $result = $stmt->execute([$adminId, $notes, $id]);

        if ($result && $contribution) {
            require_once ROOT_PATH . '/app/models/Notification.php';
            $notif = new Notification($this->pdo);
            $notif->create(
                $contribution['user_id'], 
                'contribution_rejected', 
                'Contribution rejetée', 
                "Votre contribution a été rejetée. Motif : " . ($notes ?: 'Aucun motif spécifié'), 
                BASE_URL . '/user/contributions'
            );

            // Send email notification
            require_once ROOT_PATH . '/app/helpers/Email.php';
            Email::send(
                $contribution['email'], 
                "Mise à jour de votre contribution - Amawal", 
                "<h2>Bonjour,</h2><p>Votre contribution a été examinée par notre équipe et n'a pas pu être approuvée pour le moment.</p><p><b>Motif :</b> " . ($notes ?: 'Aucun motif spécifié') . "</p><p><a href='" . BASE_URL . "/user/contributions'>Voir les détails</a></p>"
            );
        }

        return $result;
    }

    /**
     * Apply the approved changes to the real database tables
     * @param array $contribution
     */
    private function applyApprovedContribution($contribution) {
        $type = $contribution['contribution_type'];
        $action = $contribution['action_type'];
        $data = $contribution['content_after'];
        switch ($type) {
            case 'word':
                if ($action === 'create') {
                    $wordId = $this->insertWord($data);
                    if ($wordId) {
                        $this->saveRelations($wordId, $data);
                    }
                } elseif ($action === 'update') {
                    $this->updateWord($contribution['target_id'], $data);
                    $this->saveRelations($contribution['target_id'], $data);
                }
                break;
            case 'example':
                if ($action === 'create') {
                    $this->insertExample($data);
                }
                break;
            case 'proverb':
                if ($action === 'create') {
                    $this->insertProverb($data);
                }
                break;
            // Add more cases as needed
        }
    }

    private function insertWord($data) {
        $sql = "INSERT INTO words (
                    word_tfng, word_lat, translation_fr, 
                    definition_tfng, definition_lat, 
                    part_of_speech, plural_tfng, plural_lat, 
                    feminine_tfng, feminine_lat, 
                    annexed_tfng, annexed_lat, 
                    root_tfng, root_lat
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['word_tfng'],
            $data['word_lat'],
            $data['translation_fr'],
            $data['definition_tfng'] ?? null,
            $data['definition_lat'] ?? null,
            $data['word_type'] ?? $data['part_of_speech'] ?? null,
            $data['plural_tfng'] ?? null,
            $data['plural_lat'] ?? null,
            $data['feminine_tfng'] ?? null,
            $data['feminine_lat'] ?? null,
            $data['annexed_tfng'] ?? null,
            $data['annexed_lat'] ?? null,
            $data['root_tfng'] ?? null,
            $data['root_lat'] ?? null
        ]);
        return $this->pdo->lastInsertId();
    }

    private function updateWord($id, $data) {
        $sql = "UPDATE words SET 
                    word_tfng = ?, word_lat = ?, translation_fr = ?, 
                    definition_tfng = ?, definition_lat = ?, 
                    part_of_speech = ?, plural_tfng = ?, plural_lat = ?, 
                    feminine_tfng = ?, feminine_lat = ?, 
                    annexed_tfng = ?, annexed_lat = ?, 
                    root_tfng = ?, root_lat = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['word_tfng'],
            $data['word_lat'],
            $data['translation_fr'],
            $data['definition_tfng'] ?? null,
            $data['definition_lat'] ?? null,
            $data['word_type'] ?? $data['part_of_speech'] ?? null,
            $data['plural_tfng'] ?? null,
            $data['plural_lat'] ?? null,
            $data['feminine_tfng'] ?? null,
            $data['feminine_lat'] ?? null,
            $data['annexed_tfng'] ?? null,
            $data['annexed_lat'] ?? null,
            $data['root_tfng'] ?? null,
            $data['root_lat'] ?? null,
            $id
        ]);
    }

    private function saveRelations($wordId, $data) {
        // Handle Synonyms
        if (!empty($data['synonyms'])) {
            $this->pdo->prepare("DELETE FROM synonyms WHERE word_id = ?")->execute([$wordId]);
            $synStmt = $this->pdo->prepare("INSERT INTO synonyms (word_id, synonym_tfng, synonym_lat) VALUES (?, ?, ?)");
            
            // data['synonyms'] could be an array of {tfng, lat} or a string (if from simple form)
            if (is_array($data['synonyms'])) {
                foreach ($data['synonyms'] as $syn) {
                    $synStmt->execute([$wordId, $syn['tfng'] ?? null, $syn['lat'] ?? null]);
                }
            }
        }

        // Handle Antonyms
        if (!empty($data['antonyms'])) {
            $this->pdo->prepare("DELETE FROM antonyms WHERE word_id = ?")->execute([$wordId]);
            $antStmt = $this->pdo->prepare("INSERT INTO antonyms (word_id, antonym_tfng, antonym_lat) VALUES (?, ?, ?)");
            
            if (is_array($data['antonyms'])) {
                foreach ($data['antonyms'] as $ant) {
                    $antStmt->execute([$wordId, $ant['tfng'] ?? null, $ant['lat'] ?? null]);
                }
            }
        }

        // Handle Single Example from Word Form
        if (!empty($data['example_tfng']) || !empty($data['example_lat'])) {
            // Check if this example already exists to avoid duplication if running multiple times or updates
            $check = $this->pdo->prepare("SELECT id FROM examples WHERE word_id = ? AND example_tfng = ? LIMIT 1");
            $check->execute([$wordId, $data['example_tfng']]);
            if (!$check->fetch()) {
                 $exStmt = $this->pdo->prepare("INSERT INTO examples (word_id, example_tfng, example_lat, example_fr) VALUES (?, ?, ?, ?)");
                 $exStmt->execute([
                     $wordId, 
                     $data['example_tfng'] ?? '', 
                     $data['example_lat'] ?? '',
                     $data['translation_fr'] ?? '' // Using word translation as fallback or empty
                 ]);
            }
        }
    }

    private function insertExample($data) {
        // Using example_fr to match database.sql if strictly followed, 
        // but AdminController doesn't show independent example insertion.
        // Assuming 'examples' table exists as per database.sql
        $sql = "INSERT INTO examples (word_id, example_tfng, example_lat, example_fr) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['word_id'],
            $data['example_tfng'],
            $data['example_lat'],
            $data['translation_fr'] // Mapping translation_fr to example_fr
        ]);
    }

    private function insertProverb($data) {
        // Matching AdminController schema for proverbs
        $sql = "INSERT INTO proverbs (proverb_tfng, proverb_lat, translation_fr, explanation) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['text_tfng'] ?? $data['proverb_tfng'] ?? '',
            $data['text_lat'] ?? $data['proverb_lat'] ?? '',
            $data['translation_fr'],
            $data['explanation_fr'] ?? $data['explanation'] ?? null
        ]);
    }

    /**
     * Get contribution statistics for a user
     * @param int $userId
     * @return array
     */
    public function getStats($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM user_contributions 
            WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
