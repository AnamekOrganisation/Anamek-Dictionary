<?php

namespace App\Repositories;

use PDO;
use Exception;

class WordRepository {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Find a word by ID, Slug, or partial text match
     */
    public function find($id) {
        $searchId = null;
        
        // Primary ID check
        if (is_numeric($id)) {
            $searchId = $id;
        } 
        // Strictly check for Slug-ID format (e.g. Ner-212)
        elseif (preg_match('/-(\d+)$/', $id, $matches)) {
            $searchId = $matches[1];
        }

        if ($searchId) {
            $stmt = $this->pdo->prepare("SELECT * FROM words WHERE id = ?");
            $stmt->execute([$searchId]);
        } else {
            // Otherwise search by slug (word_lat) OR French translation (partial match)
            $slug = urldecode($id);
            $stmt = $this->pdo->prepare("SELECT * FROM words WHERE word_lat = ? OR word_tfng = ? OR translation_fr LIKE ?");
            $stmt->execute([$slug, $slug, '%' . $slug . '%']);
        }
        
        $word = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($word) {
            $this->hydrateRelations($word);
        }
        return $word;
    }

    /**
     * Search words with weighted relevance
     */
    public function search($query, $lang = 'tfng', $type = 'start', $groupBy = false) {
        $columns = ['word_tfng']; 
        switch ($lang) {
            case 'fr': $columns = ['translation_fr']; break;
            case 'ber': $columns = ['word_tfng', 'word_lat']; break;
        }

        $conditions = [];
        $params = [];
        foreach ($columns as $idx => $column) {
            $param = ":query$idx";
            switch($type) {
                case 'exact':
                    $conditions[] = "$column = $param";
                    $params[$param] = $query;
                    break;
                case 'contain':
                    $conditions[] = "$column LIKE $param";
                    $params[$param] = "%$query%";
                    break;
                default: // start
                    $conditions[] = "$column LIKE $param";
                    $params[$param] = "$query%";
            }
        }

        $mainQuery = "SELECT * FROM words WHERE (" . implode(' OR ', $conditions) . ")";
        
        // Step 9: Search Ranking Optimization (Simplified weighted ordering)
        $orderBy = "CASE 
            WHEN word_lat = :exact OR word_tfng = :exact THEN 1 
            WHEN word_lat LIKE :prefix OR word_tfng LIKE :prefix THEN 2 
            ELSE 3 
        END ASC, word_lat ASC";
        
        $params[':exact'] = $query;
        $params[':prefix'] = "$query%";

        if ($groupBy) {
            $sql = "SELECT * FROM words WHERE id IN (
                        SELECT MIN(id) FROM words 
                        WHERE (" . implode(' OR ', $conditions) . ")
                        GROUP BY word_lat
                    ) ORDER BY $orderBy";
        } else {
            $sql = "$mainQuery ORDER BY $orderBy";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($words)) {
            $wordIds = array_column($words, 'id');
            $this->loadRelationsForWords($words, $wordIds);
        }
        
        return $words;
    }

    /**
     * Hydrate a single word with all its relations
     */
    public function hydrateRelations(&$word) {
        $word['synonyms'] = $this->getRelated('synonyms', $word['id']);
        $word['antonyms'] = $this->getRelated('antonyms', $word['id']);
        $word['examples'] = $this->getRelated('examples', $word['id']);
        $word['etymology'] = $this->getEtymology($word['id']);
        $word['categories'] = $this->getCategories($word['id']);
        $word['pronunciations'] = $this->getMultimedia('pronunciations', $word['id']);
        $word['illustrations'] = $this->getMultimedia('illustrations', $word['id']);
    }

    /**
     * Eager load all relations for a list of words
     */
    public function loadRelationsForWords(&$words, $wordIds) {
        if (empty($wordIds)) return;

        $map = [];
        foreach ($words as &$word) {
            $word['synonyms'] = [];
            $word['antonyms'] = [];
            $word['examples'] = [];
            $word['pronunciations'] = [];
            $word['illustrations'] = [];
            $map[$word['id']] = &$word;
        }

        $placeholders = str_repeat('?,', count($wordIds) - 1) . '?';

        // 1. Fetch Synonyms
        $stmt = $this->pdo->prepare("SELECT word_id, synonym_tfng, synonym_lat FROM synonyms WHERE word_id IN ($placeholders)");
        $stmt->execute($wordIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($map[$row['word_id']])) $map[$row['word_id']]['synonyms'][] = $row;
        }

        // 2. Fetch Antonyms
        $stmt = $this->pdo->prepare("SELECT word_id, antonym_tfng, antonym_lat FROM antonyms WHERE word_id IN ($placeholders)");
        $stmt->execute($wordIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($map[$row['word_id']])) $map[$row['word_id']]['antonyms'][] = $row;
        }

        // 3. Fetch Examples
        $stmt = $this->pdo->prepare("SELECT word_id, example_tfng, example_lat, example_fr FROM examples WHERE word_id IN ($placeholders)");
        $stmt->execute($wordIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (isset($map[$row['word_id']])) $map[$row['word_id']]['examples'][] = $row;
        }

        // 4. Fetch Pronunciations
        $stmt = $this->pdo->prepare("SELECT * FROM pronunciations WHERE word_id IN ($placeholders)");
        $stmt->execute($wordIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             if (isset($map[$row['word_id']])) $map[$row['word_id']]['pronunciations'][] = $row;
        }

        // 5. Fetch Illustrations
        $stmt = $this->pdo->prepare("SELECT * FROM illustrations WHERE word_id IN ($placeholders)");
        $stmt->execute($wordIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             if (isset($map[$row['word_id']])) $map[$row['word_id']]['illustrations'][] = $row;
        }
    }

    public function getRandomWithDefinition() {
        $count = $this->pdo
            ->query("SELECT COUNT(*) FROM words WHERE word_tfng != '' AND word_lat != ''")
            ->fetchColumn();

        if ($count == 0) return null;

        $seed = intval(date('Ymd'));
        $index = $seed % $count;

        $stmt = $this->pdo->prepare("SELECT * FROM words WHERE word_tfng != '' AND word_lat != '' LIMIT 1 OFFSET :offset");
        $stmt->bindValue(':offset', $index, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllByText($text, $lang = '') {
        $params = ['text' => $text, 'prefix' => $text . '%'];
        $isShort = mb_strlen($text) <= 2;
        
        $conditions = [];
        if ($lang === 'fr') {
            $conditions[] = "translation_fr = :text";
            if (!$isShort) {
                $conditions[] = "translation_fr LIKE :partial";
                $params['partial'] = '%' . $text . '%';
            }
        } elseif ($lang === 'ber') {
            $conditions[] = "word_tfng = :text";
            $conditions[] = "word_lat = :text";
            $conditions[] = "word_tfng LIKE :prefix";
            $conditions[] = "word_lat LIKE :prefix";
        } else {
            // Default broad search
            $conditions[] = "word_tfng = :text";
            $conditions[] = "word_lat = :text";
            $conditions[] = "translation_fr = :text";
            $conditions[] = "word_tfng LIKE :prefix";
            $conditions[] = "word_lat LIKE :prefix";
            if (!$isShort) {
                $conditions[] = "translation_fr LIKE :partial";
                $params['partial'] = '%' . $text . '%';
            }
        }

        $sql = "SELECT *, 
                CASE 
                    WHEN word_tfng = :text OR word_lat = :text OR translation_fr = :text THEN 1
                    WHEN word_lat LIKE :prefix OR word_tfng LIKE :prefix THEN 2
                    ELSE 3 
                END as relevance
                FROM words 
                WHERE (" . implode(' OR ', $conditions) . ")
                ORDER BY relevance ASC, word_lat ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($words)) {
            $wordIds = array_column($words, 'id');
            $this->loadRelationsForWords($words, $wordIds);
        }

        return $words;
    }

    public function getRecentSearches($limit = 5) {
        $sql = "SELECT w.id, w.word_tfng, w.word_lat, w.translation_fr, 
                       COALESCE(rs.search_count, 0) AS search_count, rs.last_searched
                FROM words w
                LEFT JOIN recent_searches rs ON w.id = rs.word_id 
                WHERE rs.last_searched IS NOT NULL
                ORDER BY rs.last_searched DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaginated($page, $perPage, $search = '') {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = "";

        if (!empty($search)) {
            $where = " WHERE word_tfng LIKE :q OR word_lat LIKE :q OR translation_fr LIKE :q";
            $params[':q'] = "%$search%";
        }

        $sql = "SELECT * FROM words $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSearch($search) {
        $sql = "SELECT COUNT(*) FROM words WHERE word_tfng LIKE ? OR word_lat LIKE ? OR translation_fr LIKE ?";
        $q = "%$search%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$q, $q, $q]);
        return $stmt->fetchColumn();
    }

    public function getRecentWords($limit = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM words ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM words");
        return $stmt->fetchColumn();
    }

    private function getRelated($table, $wordId) {
        if (!in_array($table, ['synonyms', 'antonyms', 'examples'])) return [];
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEtymology($wordId) {
        $stmt = $this->pdo->prepare("SELECT * FROM etymologies WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories($wordId) {
        $stmt = $this->pdo->prepare("
            SELECT c.* FROM word_categories c
            JOIN word_category_mapping m ON c.id = m.category_id
            WHERE m.word_id = ?
        ");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getMultimedia($table, $wordId) {
        if (!in_array($table, ['pronunciations', 'illustrations'])) return [];
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
