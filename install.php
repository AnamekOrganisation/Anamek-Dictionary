<?php
/**
 * Anamek Dictionary Premium Installer
 * A creative, WordPress-like installer for project initialization.
 */

session_start();

// Check if installed (Deep-Check Security)
if (file_exists('.env')) {
    require_once 'config/init.php';
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            die('Installation already completed and database is healthy. If you need to reinstall, please remove the .env file and drop database tables manually.');
        }
    } catch (Exception $e) {
        // .env exists but DB connection fails - allow installer to proceed to fix settings
    }
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Helper to write .env
function writeEnv($data) {
    // Add default environment settings if missing
    $defaults = [
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'APP_URL' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'])
    ];
    $data = array_merge($defaults, $data);

    $content = "# Anamek Dictionary Configuration\n";
    foreach ($data as $key => $value) {
        $content .= "$key=\"$value\"\n";
    }
    return file_put_contents('.env', $content);
}

// Logic for steps
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        // Database Setup
        $host = $_POST['db_host'] ?? 'localhost';
        $name = $_POST['db_name'] ?? '';
        $user = $_POST['db_user'] ?? '';
        $pass = $_POST['db_pass'] ?? '';

        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $_SESSION['db_data'] = [
                'DB_HOST' => $host,
                'DB_NAME' => $name,
                'DB_USER' => $user,
                'DB_PASS' => $pass
            ];

            header('Location: install.php?step=3');
            exit;
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
        }
    }

    if ($step === 3) {
        // Schema Initialization
        $dbData = $_SESSION['db_data'] ?? [];
        if (empty($dbData)) {
            header('Location: install.php?step=2');
            exit;
        }

        try {
            $pdo = new PDO("mysql:host={$dbData['DB_HOST']};dbname={$dbData['DB_NAME']}", $dbData['DB_USER'], $dbData['DB_PASS']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(100),
                bio TEXT,
                avatar_url VARCHAR(255),
                verification_token VARCHAR(255),
                email_verified TINYINT(1) DEFAULT 0,
                reset_token VARCHAR(255),
                reset_token_expires DATETIME,
                contribution_points INT DEFAULT 0,
                user_type ENUM('user', 'admin') DEFAULT 'user',
                is_active TINYINT(1) DEFAULT 1,
                last_login DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS words (
                id INT AUTO_INCREMENT PRIMARY KEY,
                word_tfng VARCHAR(255) NOT NULL,
                word_lat VARCHAR(255) NOT NULL,
                translation_fr VARCHAR(255) NOT NULL,
                definition_tfng TEXT,
                definition_lat TEXT,
                part_of_speech VARCHAR(50),
                plural_tfng VARCHAR(255),
                plural_lat VARCHAR(255),
                feminine_tfng VARCHAR(255),
                feminine_lat VARCHAR(255),
                annexed_tfng VARCHAR(255),
                annexed_lat VARCHAR(255),
                root_tfng VARCHAR(255),
                root_lat VARCHAR(255),
                example_tfng TEXT,
                example_lat TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS proverbs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                proverb_tfng TEXT NOT NULL,
                proverb_lat TEXT NOT NULL,
                translation_fr TEXT NOT NULL,
                explanation TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS synonyms (
                id INT AUTO_INCREMENT PRIMARY KEY,
                word_id INT NOT NULL,
                synonym_tfng VARCHAR(255),
                synonym_lat VARCHAR(255),
                FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS antonyms (
                id INT AUTO_INCREMENT PRIMARY KEY,
                word_id INT NOT NULL,
                antonym_tfng VARCHAR(255),
                antonym_lat VARCHAR(255),
                FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS examples (
                id INT AUTO_INCREMENT PRIMARY KEY,
                word_id INT NOT NULL,
                example_tfng TEXT,
                example_lat TEXT,
                example_fr TEXT,
                FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS etymologies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                word_id INT NOT NULL,
                origin TEXT,
                description TEXT,
                FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS word_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_name_fr VARCHAR(100) NOT NULL,
                description TEXT
            );

            CREATE TABLE IF NOT EXISTS word_category_mapping (
                word_id INT NOT NULL,
                category_id INT NOT NULL,
                PRIMARY KEY (word_id, category_id),
                FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES word_categories(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS user_contributions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                contribution_type VARCHAR(50) NOT NULL,
                action_type VARCHAR(50) NOT NULL,
                target_id INT,
                content_before JSON,
                content_after JSON,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                points_awarded INT DEFAULT 0,
                reviewed_by INT,
                review_notes TEXT,
                reviewed_at DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                notification_type VARCHAR(50),
                title VARCHAR(255),
                message TEXT,
                link VARCHAR(255),
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS site_visits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visitor_hash VARCHAR(64) NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                page_url VARCHAR(255),
                referrer VARCHAR(255),
                visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_visitor_day (visitor_hash, visited_at),
                INDEX idx_visited_at (visited_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS search_analytics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                query VARCHAR(255) NOT NULL,
                results_count INT DEFAULT 0,
                language VARCHAR(10),
                searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_query (query),
                INDEX idx_searched_at (searched_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS recent_searches (
                word_id INT PRIMARY KEY,
                search_count INT DEFAULT 1,
                last_searched TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS quizzes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title_tfng VARCHAR(255),
                title_lat VARCHAR(255),
                title_fr VARCHAR(255),
                description TEXT,
                difficulty_level ENUM('easy', 'medium', 'hard'),
                category_id INT,
                is_active TINYINT(1) DEFAULT 1,
                is_featured TINYINT(1) DEFAULT 0,
                created_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS quiz_questions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                quiz_id INT NOT NULL,
                question_text TEXT NOT NULL,
                question_type VARCHAR(50),
                options JSON,
                correct_answer TEXT,
                points INT DEFAULT 1,
                display_order INT DEFAULT 0,
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS user_quiz_results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                quiz_id INT NOT NULL,
                score INT,
                total_questions INT,
                correct_answers INT,
                percentage DECIMAL(5,2),
                time_taken_seconds INT,
                answers JSON,
                passed TINYINT(1),
                completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
            );

            INSERT IGNORE INTO social_links (platform, url) VALUES ('facebook', ''), ('instagram', ''), ('twitter', ''), ('youtube', '');

            -- Default Categories
            INSERT IGNORE INTO word_categories (category_name_fr, description) VALUES 
            ('Nature', 'Mots liés à la nature, l\'environnement et le climat.'),
            ('Architecture', 'Termes d\'architecture et de construction.'),
            ('Anatomie', 'Parties du corps humain et biologie.'),
            ('Cuisine', 'Gastronomie, aliments et ustensiles.'),
            ('Société', 'Vie sociale, culture et traditions.'),
            ('Technique', 'Outils, métiers et techniques.');

            -- Welcome Word
            INSERT IGNORE INTO words (word_tfng, word_lat, translation_fr, definition_tfng, definition_lat, part_of_speech) VALUES 
            ('ⴰⵣⵓⵍ', 'Azul', 'Bonjour / Salut', 'ⵜⴰⵎⵙⵙⵍⵉⵡⵜ ⵏ ⵓⵙⵏⵓⴱⴱⵛ ⴷ ⵓⵙⴷⵔⴼⵉ.', 'Salutation de bienvenue et de paix.', 'interjection');
            ";

            $pdo->exec($sql);
            header('Location: install.php?step=4');
            exit;
        } catch (PDOException $e) {
            $error = "Schema error: " . $e->getMessage();
        }
    }

    if ($step === 4) {
        // Admin Setup
        $username = $_POST['admin_user'] ?? '';
        $email = $_POST['admin_email'] ?? '';
        $pass = $_POST['admin_pass'] ?? '';

        if (empty($username) || empty($email) || empty($pass)) {
            $error = "All fields are required.";
        } else {
            $dbData = $_SESSION['db_data'] ?? [];
            try {
                $pdo = new PDO("mysql:host={$dbData['DB_HOST']};dbname={$dbData['DB_NAME']}", $dbData['DB_USER'], $dbData['DB_PASS']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $hashed = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type, email_verified) VALUES (?, ?, ?, 'admin', 1)");
                $stmt->execute([$username, $email, $hashed]);

                $envData = $_SESSION['db_data'];
                $envData['APP_INSTALLED'] = 'true';
                writeEnv($envData);
                
                header('Location: install.php?step=5');
                exit;
            } catch (PDOException $e) {
                $error = "Admin setup error: " . $e->getMessage();
            }
        }
    }
}

// Requirement Check
if (!is_dir('cache')) {
    @mkdir('cache', 0755, true);
}

$requirements = [
    'PHP Version (>= 7.4)' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
    'JSON Extension' => extension_loaded('json'),
    'Writable Root Directory' => is_writable('.'),
    'Writable cache Directory' => is_writable('cache'),
];
$allMet = !in_array(false, $requirements);

?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anamek | Installation</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --success: #22c55e;
            --error: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0, transparent 50%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            font-size: 2.5rem;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%; left: 0; right: 0;
            height: 2px; background: rgba(255,255,255,0.1);
            z-index: 0; transform: translateY(-50%);
        }
        .step-dot {
            width: 32px; height: 32px;
            background: #1e293b;
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 600;
            position: relative; z-index: 1;
            transition: all 0.3s;
        }
        .step-dot.active {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }
        .step-dot.done {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        h2 { margin-bottom: 10px; font-weight: 600; text-align: center; }
        p.desc { text-align: center; color: var(--text-muted); margin-bottom: 30px; font-size: 0.9rem; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 0.85rem; color: var(--text-muted); }
        input {
            width: 100%;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 16px;
            border-radius: 12px;
            color: white;
            font-family: inherit;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .btn {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex; align-items: center; justify-content: center;
            gap: 10px;
        }
        .btn:hover { background: var(--primary-hover); transform: translateY(-2px); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid var(--error); color: #fecaca; }
        .alert-success { background: rgba(34, 197, 94, 0.1); border: 1px solid var(--success); color: #bbf7d0; }

        .req-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .req-status { font-weight: 600; font-size: 0.8rem; }
        .status-ok { color: var(--success); }
        .status-fail { color: var(--error); }

        .finish-icon {
            font-size: 4rem; text-align: center; margin-bottom: 20px; color: var(--success);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <h1>Anamek</h1>
        </div>

        <div class="step-indicator">
            <div class="step-dot <?php echo $step == 1 ? 'active' : ($step > 1 ? 'done' : ''); ?>">1</div>
            <div class="step-dot <?php echo $step == 2 ? 'active' : ($step > 2 ? 'done' : ''); ?>">2</div>
            <div class="step-dot <?php echo $step == 3 ? 'active' : ($step > 3 ? 'done' : ''); ?>">3</div>
            <div class="step-dot <?php echo $step == 4 ? 'active' : ($step > 4 ? 'done' : ''); ?>">4</div>
            <div class="step-dot <?php echo $step == 5 ? 'active' : ($step > 5 ? 'done' : ''); ?>">5</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <h2>Bienvenue</h2>
            <p class="desc">Vérifions si votre serveur est prêt pour Anamek Dictionary.</p>
            
            <div style="margin-bottom: 30px;">
                <?php foreach ($requirements as $label => $met): ?>
                    <div class="req-item">
                        <span><?php echo $label; ?></span>
                        <span class="req-status <?php echo $met ? 'status-ok' : 'status-fail'; ?>">
                            <?php echo $met ? '✓ OK' : '✗ ÉCHEC'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="btn" <?php echo !$allMet ? 'disabled' : ''; ?> onclick="location.href='?step=2'">
                Continuer ➜
            </button>
        <?php endif; ?>

        <?php if ($step === 2): ?>
            <h2>Base de données</h2>
            <p class="desc">Configurez votre connexion MySQL.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label>Hôte</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label>Nom de la base</label>
                    <input type="text" name="db_name" value="Amawal" required>
                </div>
                <div class="form-group">
                    <label>Utilisateur</label>
                    <input type="text" name="db_user" value="root" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="db_pass">
                </div>
                <button type="submit" class="btn">Tester et Créer ➜</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 3): ?>
            <h2>Initialisation</h2>
            <p class="desc">Nous préparons tout pour vous. Cliquez pour créer les tables.</p>
            
            <form method="POST">
                <button type="submit" class="btn">Installer les tables ➜</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 4): ?>
            <h2>Administrateur</h2>
            <p class="desc">Créez votre compte de super-administrateur.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="admin_user" value="admin" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="admin_email" placeholder="admin@example.com" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="admin_pass" required>
                </div>
                <button type="submit" class="btn">Terminer l'installation ➜</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 5): ?>
            <div class="finish-icon">🎉</div>
            <h2>Félicitations !</h2>
            <p class="desc">Anamek Dictionary est maintenant installé. L'environnement est prêt.</p>
            
            <button class="btn" onclick="location.href='index.php'">Accéder au Glossaire ➜</button>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.75rem; color: var(--success);">
                Note: Le fichier .env a été créé et l'installation est maintenant verrouillée pour votre sécurité.
            </p>
        <?php endif; ?>

    </div>

</body>
</html>
