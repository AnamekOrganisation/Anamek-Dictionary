<?php
// API endpoint for autocomplete suggestions
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    require_once __DIR__ . '/../config/init.php';
    require_once __DIR__ . '/../app/models/Word.php';

    // Ensure no previous output
    ob_clean();
    
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $query = $_GET['q'] ?? '';
    $lang = $_GET['lang'] ?? 'ber'; // Default to Amazigh
    $query = trim($query);
    
    // SECURITY: Whitelist allowed languages
    $allowedLangs = ['fr', 'ber', 'tfng'];
    if (!in_array($lang, $allowedLangs, true)) {
        throw new Exception('Invalid language parameter');
    }
    
    if (strlen($query) < 2) {
        echo json_encode(['results' => []]);
        exit;
    }
    
    if ($lang === 'fr') {
        // French Search: Unique translations starting with query
        $sql = "SELECT translation_fr as res_fr, MIN(word_tfng) as res_tfng, MIN(word_lat) as res_lat, COUNT(*) as occurrence_count, MIN(id) as word_id FROM words WHERE ";
        $sql .= "(translation_fr LIKE :start_query OR word_lat LIKE :start_query)";
        $sql .= " GROUP BY translation_fr";
        $sql .= " ORDER BY CASE 
                    WHEN translation_fr LIKE :exact THEN 1
                    WHEN translation_fr LIKE :start_query THEN 2
                    ELSE 3 END, res_fr ASC";
    } else {
        // Amazigh Search: Unique Latin words starting with query
        $sql = "SELECT MIN(word_tfng) as res_tfng, word_lat as res_lat, COUNT(*) as occurrence_count, MIN(id) as word_id FROM words WHERE ";
        $sql .= "(word_lat LIKE :start_query OR word_tfng LIKE :start_query OR translation_fr LIKE :start_query)";
        $sql .= " GROUP BY word_lat";
        $sql .= " ORDER BY CASE 
                    WHEN word_lat LIKE :exact THEN 1
                    WHEN word_lat LIKE :start_query THEN 2
                    ELSE 3 END, res_lat ASC";
    }
    
    $sql .= " LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':start_query' => $query . '%', // Only matches at the beginning
        ':exact' => $query
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Security: Sanitize all output before JSON encoding
    $safeResults = array_map(function($result) use ($lang) {
        $item = [
            'word_tfng' => htmlspecialchars($result['res_tfng'] ?? '', ENT_QUOTES, 'UTF-8'),
            'word_lat' => htmlspecialchars($result['res_lat'] ?? '', ENT_QUOTES, 'UTF-8'),
            'count' => (int)$result['occurrence_count'],
            'id' => (int)$result['word_id']
        ];
        if ($lang === 'fr') {
            $item['translation_fr'] = htmlspecialchars($result['res_fr'] ?? '', ENT_QUOTES, 'UTF-8');
        }
        return $item;
    }, $results);
    
    echo json_encode([
        'results' => $safeResults,
        'count' => count($results)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage(),
        'results' => []
    ]);
}
