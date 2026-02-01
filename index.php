<?php
/**
 * Anamek Dictionary - Main Entry point
 */

/**
 * CRITICAL: Set security headers before any output
 */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:");

// Enable Error Logging, but hide from users for a clean UI
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

define('BASE_PATH', __DIR__);

/**
 * 1. Initialize Application Environment
 */
if (!file_exists(BASE_PATH . '/.env')) {
    header('Location: install.php');
    exit;
}

require_once BASE_PATH . '/config/init.php';
define('INSTALLED', true);

/**
 * 2. Language & Redirect Handling
 */
if (isset($_GET['lang']) && strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === false) {
    setLanguage($_GET['lang']);
    $params = $_GET; 
    unset($params['lang'], $params['action']); // Remove lang and legacy action
    $queryString = http_build_query($params);
    $redirectUrl = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    if (!empty($queryString)) $redirectUrl .= '?' . $queryString;
    header('Location: ' . $redirectUrl);
    exit;
}

/**
 * 3. App Core Execution
 */
use App\Core\Router;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Analytics Recording (Excluding API/Admin)
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === false && strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') === false) {
        $analytics = new Analytics($pdo);
        $analytics->recordVisit();
    }
    
    // Controller/Handler Instances (All classes loaded via init.php)
    $router = new Router();
    $controller = new DictionaryController($pdo);
    $api = new ApiController($pdo);
    $authController = new AuthController($pdo);
    $userController = new UserController($pdo);
    
    $adminController = new AdminController($pdo);
    $adminWordController = new AdminWordController($pdo);
    $adminProverbController = new AdminProverbController($pdo);
    $adminUserController = new AdminUserController($pdo);
    $adminSettingsController = new AdminSettingsController($pdo);

    /**
     * 4. Dispatch Routes
     */
    if (file_exists(BASE_PATH . '/routes/web.php')) {
        require_once BASE_PATH . '/routes/web.php';
        $router->dispatch();
    } else {
        throw new Exception("Application routes configuration is missing.");
    }

} catch (Exception $e) {
    // SECURITY: Log full error details server-side only, hide from users
    error_log("Bootstrap Error: " . $e->getMessage());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    error_log("Trace: " . $e->getTraceAsString());
    
    // SECURITY: Show generic error to user, no technical details
    $errorMsg = "An error occurred. Please try again later.";
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        $errorMsg = htmlspecialchars($e->getMessage());
    }
    
    die('<div style="font-family:sans-serif;padding:30px;background:#fff;color:#721c24;border-top:4px solid #cc0000;box-shadow:0 10px 30px rgba(0,0,0,0.1);max-width:600px;margin:100px auto;">
            <h2 style="margin-top:0;">System Error</h2>
            <p><strong>' . $errorMsg . '</strong></p>
            <p style="font-size:0.85em;color:#999;">Error ID: ' . bin2hex(random_bytes(4)) . '</p>
         </div>');
}
