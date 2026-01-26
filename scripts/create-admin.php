<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Create admins table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Admin credentials
    $username = 'admin';
    $password = 'admin123'; // You should change this in production
    //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    if (!$stmt->fetch()) {
        // Insert admin user
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        echo "Admin user created successfully\n";
    } else {
        echo "Admin user already exists\n";
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}