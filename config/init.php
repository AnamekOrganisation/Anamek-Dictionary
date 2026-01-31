<?php
if (session_status() === PHP_SESSION_NONE) {
    // Secure session cookie settings
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Regenerate session ID periodically or on login to prevent fixation
if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
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