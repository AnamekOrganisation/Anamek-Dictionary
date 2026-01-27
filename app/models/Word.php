<?php

class Word {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

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
            $word['synonyms'] = $this->getRelated('synonyms', $word['id']);
            $word['antonyms'] = $this->getRelated('antonyms', $word['id']);
            $word['examples'] = $this->getRelated('examples', $word['id']);
            $word['etymology'] = $this->getEtymology($word['id']);
            $word['categories'] = $this->getCategories($word['id']);
            $word['pronunciations'] = $this->getMultimedia('pronunciations', $word['id']);
            $word['illustrations'] = $this->getMultimedia('illustrations', $word['id']);
        }
        return $word;
    }

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
        
        if ($groupBy) {
            // Fix for ONLY_FULL_GROUP_BY: use IN (SELECT MIN(id) ... GROUP BY word_lat)
            $sql = "SELECT * FROM words WHERE id IN (
                        SELECT MIN(id) FROM words 
                        WHERE (" . implode(' OR ', $conditions) . ")
                        GROUP BY word_lat
                    )";
        } else {
            $sql = $mainQuery;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enrich with relations (optional for list view, maybe too heavy?)
        // For autocomplete, maybe we don't need all relations. 
        // But Oxford style implies rich results. Let's keep it light for search list.
        return $words;
    }

    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM words");
        return $stmt->fetchColumn();
    }

    public function getRandomWithDefinition()
    {
        $count = $this->pdo
            ->query("SELECT COUNT(*) FROM words WHERE word_tfng != '' AND word_lat != ''")
            ->fetchColumn();

        if ($count == 0) {
            return null;
        }

        $seed = intval(date('Ymd'));
        $index = $seed % $count;

        $sql = "
            SELECT *
            FROM words
            WHERE word_tfng != '' AND word_lat != ''
            LIMIT 1 OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':offset', $index, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    private function getRelated($table, $wordId) {
        // Sanitize table name to prevent SQL injection (internal use only)
        if (!in_array($table, ['synonyms', 'antonyms', 'examples'])) return [];
        
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEtymology($wordId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM etymologies WHERE word_id = ?");
            $stmt->execute([$wordId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCategories($wordId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.* FROM word_categories c
                JOIN word_category_mapping m ON c.id = m.category_id
                WHERE m.word_id = ?
            ");
            $stmt->execute([$wordId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getMultimedia($table, $wordId) {
        if (!in_array($table, ['pronunciations', 'illustrations'])) return [];
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE word_id = ?");
        $stmt->execute([$wordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function findAllByText($text) {
        // Normalization could be improved (e.g. trimming, forcing lowercase for latin)
        // For now, we search exact match on either Tifinagh OR Latin OR French columns (partial for French)
        $sql = "SELECT * FROM words WHERE word_tfng = :text OR word_lat = :text OR translation_fr LIKE :partial_text";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['text' => $text, 'partial_text' => '%' . $text . '%']);
        $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enrich all found words with relations
        foreach ($words as &$word) {
            $word['synonyms'] = $this->getRelated('synonyms', $word['id']);
            $word['antonyms'] = $this->getRelated('antonyms', $word['id']);
            $word['examples'] = $this->getRelated('examples', $word['id']);
            $word['pronunciations'] = $this->getMultimedia('pronunciations', $word['id']);
            $word['illustrations'] = $this->getMultimedia('illustrations', $word['id']);
        }
        return $words;
    }

    public function getRecentWords($limit = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM words ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
