<?php
if (session_status() === PHP_SESSION_NONE) {
    // Security: Explicit session timeout (30 minutes)
    $sessionTimeout = 1800;
    
    // Secure session cookie settings
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => !empty($_SERVER['HTTPS']) || $_SERVER['HTTP_HOST'] !== 'localhost',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Security: Check for session timeout
if (isset($_SESSION['user_id'])) {
    $sessionTimeout = 1800; // 30 minutes
    $lastActivity = $_SESSION['last_activity'] ?? time();
    
    if (time() - $lastActivity > $sessionTimeout) {
        session_destroy();
        $_SESSION = [];
        header('Location: ' . BASE_URL . '/login?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security: Force HTTPS in production
if (PHP_SAPI !== 'cli' && getenv('APP_ENV') === 'production') {
    if (empty($_SERVER['HTTPS'])) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}

define('ROOT_PATH', dirname(__FILE__, 2));
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', $scriptName === '/' ? '' : $scriptName);

// Load Composer Autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load Environment Variables
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

require_once ROOT_PATH . '/config/constants.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/Core/Validator.php';
require_once ROOT_PATH . '/app/Core/Middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/app/Repositories/WordRepository.php';
require_once ROOT_PATH . '/app/Services/AuthService.php';
require_once ROOT_PATH . '/app/Services/WordService.php';
require_once ROOT_PATH . '/app/Services/ProverbService.php';
require_once ROOT_PATH . '/app/Services/QuizService.php';
require_once ROOT_PATH . '/app/models/Word.php';
require_once ROOT_PATH . '/app/models/Proverb.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Contribution.php';
require_once ROOT_PATH . '/app/models/Notification.php';
require_once ROOT_PATH . '/app/models/Analytics.php';

require_once ROOT_PATH . '/app/helpers/security.php';
require_once ROOT_PATH . '/app/helpers/Email.php';
require_once ROOT_PATH . '/app/helpers/i18n.php';

require_once ROOT_PATH . '/app/controllers/BaseController.php';
require_once ROOT_PATH . '/app/controllers/DictionaryController.php';
require_once ROOT_PATH . '/app/controllers/AuthController.php';
require_once ROOT_PATH . '/app/controllers/UserController.php';
require_once ROOT_PATH . '/app/controllers/AdminController.php';
require_once ROOT_PATH . '/app/controllers/AdminWordController.php';
require_once ROOT_PATH . '/app/controllers/AdminProverbController.php';
require_once ROOT_PATH . '/app/controllers/AdminUserController.php';
require_once ROOT_PATH . '/app/controllers/AdminSettingsController.php';
require_once ROOT_PATH . '/app/controllers/ApiController.php';
require_once ROOT_PATH . '/app/controllers/SitemapController.php';
require_once ROOT_PATH . '/app/controllers/QuizController.php';
require_once ROOT_PATH . '/app/controllers/ContributionController.php';
require_once ROOT_PATH . '/app/controllers/AdminQuizController.php';
require_once ROOT_PATH . '/app/controllers/AdminContactController.php';
