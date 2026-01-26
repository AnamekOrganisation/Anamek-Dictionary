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
    
    if (strlen($query) < 2) {
        echo json_encode(['results' => []]);
        exit;
    }
    
    // Prepare SQL based on language
    $sql = "SELECT id, word_tfng, word_lat, translation_fr FROM words WHERE ";
    $params = [];
    
    // Logic:
    // If lang == 'fr', we search PRIMARILY in translation_fr, but also other fields for flexibility.
    // If lang == 'ber', we search PRIMARILY in word_tfng/word_lat.
    // To make it strict:
    
    if ($lang === 'fr') {
        // French Search: Prioritize translation_fr
        $sql .= "(translation_fr LIKE :query OR word_lat LIKE :query)";
        $sql .= " ORDER BY CASE 
                    WHEN translation_fr LIKE :exact THEN 1
                    WHEN translation_fr LIKE :start THEN 2
                    WHEN word_lat LIKE :start THEN 3
                    ELSE 4 END, translation_fr ASC";
    } else {
        // Amazigh Search (ber/tfng): Prioritize word_tfng and word_lat
        $sql .= "(word_lat LIKE :query OR word_tfng LIKE :query OR translation_fr LIKE :query)";
        $sql .= " ORDER BY CASE 
                    WHEN word_lat LIKE :exact THEN 1
                    WHEN word_tfng LIKE :exact THEN 2
                    WHEN word_lat LIKE :start THEN 3
                    WHEN word_tfng LIKE :start THEN 4
                    ELSE 5 END, word_lat ASC";
    }
    
    $sql .= " LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':query' => '%' . $query . '%',
        ':exact' => $query,
        ':start' => $query . '%'
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'results' => $results,
        'count' => count($results)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage(),
        'results' => []
    ]);
}
