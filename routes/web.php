<?php
/**
 * Application Routes
 */

// Global Objects from index.php: $router, $pdo, $controller, $api, $authController, $userController, $adminController, $adminWordController, $adminProverbController, $adminUserController, $adminSettingsController

// --- Frontend Routes ---
$router->get('/sitemap.xml', function() use ($pdo) {
    $sitemap = new SitemapController($pdo);
    $sitemap->index();
});

$router->get('/', function() use ($controller) {
    $_GET['action'] = 'home';
    $controller->home();
});

$router->get('/search', function() use ($controller) {
    $_GET['action'] = 'search';
    $controller->search();
});

$router->get('/proverbs', function() use ($controller) {
    $_GET['action'] = 'proverbs';
    $controller->proverbs();
});

$router->get('/word/{id}', function($params) use ($controller) {
    $_GET['action'] = 'home';
    $controller->showWord($params);
});

$router->get('/proverb/{id}', function($params) use ($controller) {
    $_GET['action'] = 'proverbs';
    $controller->showProverb($params);
});

$router->get('/contact', function() use ($controller) {
    $_GET['action'] = 'contact';
    $controller->contact();
});

$router->get('/about', function() use ($controller) {
    $_GET['action'] = 'about';
    $controller->about();
});

// --- API Routes ---
$router->get('/api/word-variants', function() use ($api) {
    $api->getWordVariants();
});

$router->get('/api/search', function() use ($api) {
    $api->search();
});

$router->get('/api/autocomplete', function() use ($pdo) {
    require_once 'api/autocomplete.php';
});

$router->get('/api/proverb-of-day', function() use ($api) {
    $api->getProverbOfTheDay();
});

$router->get('/api/recent-searches', function() use ($api) {
    $api->recentSearches();
});

$router->post('/api/add-recent-search', function() use ($api) {
    $api->addRecentSearch();
});

// --- Auth Routes ---
$router->get('/register', function() use ($authController) {
    $authController->showRegister();
});
$router->post('/register', function() use ($authController) {
    $authController->register();
});
$router->get('/login', function() use ($authController) {
    $authController->showLogin();
});
$router->post('/login', function() use ($authController) {
    $authController->login();
});
$router->get('/logout', function() use ($authController) {
    $authController->logout();
});
$router->get('/forgot-password', function() use ($authController) {
    $authController->showForgotPassword();
});
$router->post('/forgot-password', function() use ($authController) {
    $authController->sendResetLink();
});
$router->get('/reset-password', function() use ($authController) {
    $authController->showResetPassword();
});
$router->post('/reset-password', function() use ($authController) {
    $authController->resetPassword();
});
$router->get('/verify-email', function() use ($authController) {
    $authController->verifyEmail();
});

// --- User Routes ---
$router->get('/user/dashboard', function() use ($userController) {
    $userController->dashboard();
});
$router->get('/user/profile', function() use ($userController) {
    $userController->profile();
});
$router->post('/user/profile', function() use ($userController) {
    $userController->updateProfile();
});
$router->get('/user/contributions', function() use ($pdo) {
    require_once 'app/controllers/ContributionController.php';
    $contributionController = new ContributionController($pdo);
    $contributionController->myContributions();
});

// --- Quiz Routes ---
$router->get('/quizzes', function() use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->index();
});
$router->get('/quiz/{id}', function($params) use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->show($params['id']);
});
$router->get('/quiz/play/{id}', function($params) use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->play($params['id']);
});
$router->get('/quiz/results/{id}', function($params) use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->results($params['id']);
});
$router->post('/quiz/submit/{id}', function($params) use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->submit($params['id']);
});
$router->get('/leaderboard', function() use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->leaderboard();
});
$router->get('/leaderboard/{id}', function($params) use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->leaderboard($params['id']);
});
$router->get('/quiz/daily', function() use ($pdo) {
    require_once 'app/controllers/QuizController.php';
    $quizController = new QuizController($pdo);
    $quizController->dailyChallenge();
});

// --- Contribution Submission ---
$router->get('/contribute/word', function() use ($pdo) {
    require_once 'app/controllers/ContributionController.php';
    $contributionController = new ContributionController($pdo);
    $contributionController->showWordForm();
});
$router->post('/contribute/word', function() use ($pdo) {
    require_once 'app/controllers/ContributionController.php';
    $contributionController = new ContributionController($pdo);
    $contributionController->submitWord();
});
$router->get('/contribute/example', function() use ($pdo) {
    require_once 'app/controllers/ContributionController.php';
    $contributionController = new ContributionController($pdo);
    $contributionController->showExampleForm();
});
$router->post('/contribute/example', function() use ($pdo) {
    require_once 'app/controllers/ContributionController.php';
    $contributionController = new ContributionController($pdo);
    $contributionController->submitExample();
});

// --- Admin Auth (Redirects) ---
$router->get('/admin/login', function() { header('Location: ' . BASE_URL . '/login'); exit; });
$router->post('/admin/login', function() { header('Location: ' . BASE_URL . '/login'); exit; });
$router->get('/admin/logout', function() { header('Location: ' . BASE_URL . '/logout'); exit; });

// --- Admin Features ---
$router->get('/admin/reviews', function() use ($adminController) {
    $adminController->pendingReviews();
});
$router->post('/admin/reviews', function() use ($adminController) {
    $adminController->pendingReviews();
});
$router->get('/admin/users', function() use ($adminUserController) {
    $adminUserController->users();
});
$router->post('/admin/users', function() use ($adminUserController) {
    $adminUserController->users();
});
$router->get('/admin/words', function() use ($adminWordController) {
    $adminWordController->words(); 
});
$router->post('/admin/words', function() use ($adminWordController) {
    $adminWordController->editWord(); 
});
$router->get('/admin/proverbs', function() use ($adminProverbController) {
    $adminProverbController->proverbs();
});
$router->post('/admin/proverbs', function() use ($adminProverbController) {
    $adminProverbController->proverbs();
});
$router->post('/admin/delete-proverb', function() use ($adminProverbController) {
    $adminProverbController->deleteProverb();
});
$router->get('/admin/analytics', function() use ($adminController) {
    $adminController->analytics();
});
$router->get('/admin/settings', function() use ($adminSettingsController) {
    $adminSettingsController->settings();
});
$router->post('/admin/settings', function() use ($adminSettingsController) {
    $adminSettingsController->settings();
});

// --- Admin Management Pages ---
$router->get('/dashboard', function() use ($adminController) {
    $_GET['action'] = 'dashboard';
    $adminController->dashboard();
});
$router->post('/dashboard', function() use ($adminController) {
    $_GET['action'] = 'dashboard';
    $adminController->dashboard();
});
$router->get('/admin', function() use ($adminWordController) {
    $_GET['action'] = 'add-word';
    $adminWordController->addWordPage();
});
$router->get('/admin/add-word', function() use ($adminWordController) {
    $_GET['action'] = 'add-word';
    $adminWordController->addWordPage();
});
$router->post('/admin/add-word', function() use ($adminWordController) {
    $_GET['action'] = 'add-word';
    $adminWordController->addWordPage();
});
$router->get('/admin/add-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'add-proverb';
    $adminProverbController->addProverbPage();
});
$router->post('/admin/add-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'add-proverb';
    $adminProverbController->addProverbPage();
});
$router->get('/admin/edit-word', function() use ($adminWordController) {
    $_GET['action'] = 'edit-word';
    $adminWordController->editWord();
});
$router->post('/admin/edit-word', function() use ($adminWordController) {
    $_GET['action'] = 'edit-word';
    $adminWordController->editWord();
});
$router->post('/admin/delete-word', function() use ($adminWordController) {
    $adminWordController->deleteWord();
});
$router->get('/admin/edit-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'edit-proverb';
    $adminProverbController->editProverbPage();
});
$router->post('/admin/edit-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'edit-proverb';
    $adminProverbController->editProverbPage();
});

// --- 404 Handler ---
$router->setNotFound(function() use ($controller) {
    http_response_code(404);
    include 'app/views/404.php';
});
