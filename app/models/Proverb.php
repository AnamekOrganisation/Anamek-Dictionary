<?php

class Proverb {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT id, proverb_tfng, proverb_lat, translation_fr, explanation FROM proverbs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRandom() {
        // Use current date as seed for consistent "Daily" proverb
        $seed = date('Ymd');
        $sql = "SELECT id, proverb_tfng, proverb_lat, translation_fr, explanation 
                FROM proverbs 
                WHERE (proverb_tfng IS NOT NULL OR proverb_lat IS NOT NULL)
                AND proverb_tfng != '' AND proverb_lat != ''
                ORDER BY RAND($seed) LIMIT 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM proverbs");
        return $stmt->fetchColumn();
    }

    public function getPaginated($page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("
            SELECT id, proverb_tfng, proverb_lat, translation_fr, explanation
            FROM proverbs 
            ORDER BY proverb_lat ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($query, $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%$query%";
        $stmt = $this->pdo->prepare("
            SELECT id, proverb_tfng, proverb_lat, translation_fr, explanation
            FROM proverbs 
            WHERE proverb_tfng LIKE :q 
               OR proverb_lat LIKE :q 
               OR translation_fr LIKE :q 
               OR explanation LIKE :q
            ORDER BY proverb_lat ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':q', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSearch($query) {
        $searchTerm = "%$query%";
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM proverbs 
            WHERE proverb_tfng LIKE :q 
               OR proverb_lat LIKE :q 
               OR translation_fr LIKE :q 
               OR explanation LIKE :q
        ");
        $stmt->bindValue(':q', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function create(array $data) {
        $sql = "INSERT INTO proverbs (proverb_tfng, proverb_lat, translation_fr, explanation) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['proverb_tfng'],
            $data['proverb_lat'],
            $data['translation_fr'],
            $data['explanation'] ?? ''
        ]);
    }

    public function update($id, array $data) {
        $sql = "UPDATE proverbs SET proverb_tfng = ?, proverb_lat = ?, translation_fr = ?, explanation = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['proverb_tfng'],
            $data['proverb_lat'],
            $data['translation_fr'],
            $data['explanation'] ?? '',
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM proverbs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
