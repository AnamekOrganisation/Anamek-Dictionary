<?php
/**
 * Anamek Dictionary - Main Entry point
 */

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
    error_log("Bootstrap Error: " . $e->getMessage());
    die('<div style="font-family:sans-serif;padding:30px;background:#fff;color:#721c24;border-top:4px solid #cc0000;box-shadow:0 10px 30px rgba(0,0,0,0.1);max-width:600px;margin:100px auto;">
            <h2 style="margin-top:0;">System Error</h2>
            <p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>
            <p style="font-size:0.85em;color:#999;border-top:1px solid #eee;padding-top:15px;">' . $e->getFile() . ':' . $e->getLine() . '</p>
         </div>');
}
