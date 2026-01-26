<?php
require_once __DIR__ . '/../config/init.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Starting analytics cleanup and aggregation...\n";

// Aggregate for yesterday
$date = date('Y-m-d', strtotime('yesterday'));

try {
    // 1. Unique Visitors
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT session_id) FROM site_visits WHERE DATE(visited_at) = ?");
    $stmt->execute([$date]);
    $unique_visitors = $stmt->fetchColumn();

    // 2. Total Searches
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM search_analytics WHERE DATE(searched_at) = ?");
    $stmt->execute([$date]);
    $total_searches = $stmt->fetchColumn();

    // 3. New Words
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM words WHERE DATE(created_at) = ?");
    $stmt->execute([$date]);
    $new_words = $stmt->fetchColumn();

    // 4. New Contributions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_contributions WHERE DATE(created_at) = ?");
    $stmt->execute([$date]);
    $new_contributions = $stmt->fetchColumn();

    // Insert or Update daily_statistics
    $sql = "INSERT INTO daily_statistics (stat_date, total_searches, unique_visitors, new_words_added, new_contributions) 
            VALUES (:date, :searches, :visitors, :words, :contributions)
            ON DUPLICATE KEY UPDATE 
            total_searches = :searches,
            unique_visitors = :visitors,
            new_words_added = :words,
            new_contributions = :contributions";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'date' => $date,
        'searches' => $total_searches,
        'visitors' => $unique_visitors,
        'words' => $new_words,
        'contributions' => $new_contributions
    ]);

    echo "Aggregated stats for $date.\n";

    // Pruning: Delete visits older than 30 days
    $prune_date = date('Y-m-d', strtotime('-30 days'));
    $stmt = $pdo->prepare("DELETE FROM site_visits WHERE visited_at < ?");
    $stmt->execute([$prune_date]);
    $pruned_rows = $stmt->rowCount();
    echo "Pruned $pruned_rows old visit records (older than $prune_date).\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
