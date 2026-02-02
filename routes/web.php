<?php
/**
 * Application Routes
 */

// Global Objects from index.php: $router, $pdo, $controller, $api, $authController, $userController, $adminController, $adminWordController, $adminProverbController, $adminUserController, $adminSettingsController

use App\Core\Middleware\AuthMiddleware as Middleware;
use App\Core\Middleware\RateLimitMiddleware;

// --- Frontend Routes ---
$router->get('/sitemap.xml', function() use ($pdo) {
    $sitemap = new SitemapController($pdo);
    $sitemap->index();
});

$router->get('/sitemap-main.xml', function() use ($pdo) {
    $sitemap = new SitemapController($pdo);
    $sitemap->main();
});

$router->get('/sitemap-words-{id}.xml', function($params) use ($pdo) {
    $sitemap = new SitemapController($pdo);
    $sitemap->words($params);
});

$router->get('/sitemap-proverbs.xml', function() use ($pdo) {
    $sitemap = new SitemapController($pdo);
    $sitemap->proverbs();
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
$router->post('/contact', function() use ($controller) {
    $_GET['action'] = 'contact';
    $controller->submitContact();
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
}, [RateLimitMiddleware::loginAttempts($pdo), Middleware::class . '::csrf']);

$router->get('/login', function() use ($authController) {
    $authController->showLogin();
});
$router->post('/login', function() use ($authController) {
    $authController->login();
}, [RateLimitMiddleware::loginAttempts($pdo), Middleware::class . '::csrf']);

$router->get('/logout', function() use ($authController) {
    $authController->logout();
});
$router->get('/forgot-password', function() use ($authController) {
    $authController->showForgotPassword();
});

// Google Auth Routes
$router->get('/auth/google', function() use ($authController) {
    $authController->googleRedirect();
});
$router->get('/auth/google/callback', function() use ($authController) {
    $authController->googleCallback();
});
$router->post('/forgot-password', function() use ($authController) {
    $authController->sendResetLink();
}, [Middleware::class . '::csrf']);

$router->get('/reset-password', function() use ($authController) {
    $authController->showResetPassword();
});
$router->post('/reset-password', function() use ($authController) {
    $authController->resetPassword();
}, [Middleware::class . '::csrf']);

$router->get('/verify-email', function() use ($authController) {
    $authController->verifyEmail();
});

// --- User Routes (Protected) ---
$userAuth = [Middleware::class . '::auth'];
$userPostAuth = [Middleware::class . '::auth', Middleware::class . '::csrf'];

$router->get('/user/dashboard', function() use ($userController) {
    $userController->dashboard();
}, $userAuth);
$router->get('/user/profile', function() use ($userController) {
    $userController->profile();
}, $userAuth);
$router->post('/user/profile', function() use ($userController) {
    $userController->updateProfile();
}, $userPostAuth);
$router->get('/user/contributions', function() use ($pdo) {
    $contributionController = new ContributionController($pdo);
    $contributionController->myContributions();
}, $userAuth);

// --- Quiz Routes ---
$router->get('/quizzes', function() use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->index();
});
$router->get('/quiz/{id}', function($params) use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->show($params['id']);
});
$router->get('/quiz/play/{id}', function($params) use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->play($params['id']);
});
$router->get('/quiz/results/{id}', function($params) use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->results($params['id']);
}, $userAuth);
$router->post('/quiz/submit/{id}', function($params) use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->submit($params['id']);
}); // AJAX submission, might handle its own CSRF or use global
$router->get('/leaderboard', function() use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->leaderboard();
});
$router->get('/leaderboard/{id}', function($params) use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->leaderboard($params['id']);
});
$router->get('/quiz/daily', function() use ($pdo) {
    $quizController = new QuizController($pdo);
    $quizController->dailyChallenge();
});

// --- Contribution Submission (Protected) ---
$router->get('/contribute/word', function() use ($pdo) {
    $contributionController = new ContributionController($pdo);
    $contributionController->showWordForm();
}, $userAuth);
$router->post('/contribute/word', function() use ($pdo) {
    $contributionController = new ContributionController($pdo);
    $contributionController->submitWord();
}, $userPostAuth);
$router->get('/contribute/example', function() use ($pdo) {
    $contributionController = new ContributionController($pdo);
    $contributionController->showExampleForm();
}, $userAuth);
$router->post('/contribute/example', function() use ($pdo) {
    $contributionController = new ContributionController($pdo);
    $contributionController->submitExample();
}, $userPostAuth);

// --- Admin Auth (Redirects) ---
$router->get('/admin/login', function() { header('Location: ' . BASE_URL . '/login'); exit; });
$router->post('/admin/login', function() { header('Location: ' . BASE_URL . '/login'); exit; });
$router->get('/admin/logout', function() { header('Location: ' . BASE_URL . '/logout'); exit; });

// --- Admin Features (Admin Protected) ---
$adminAuth = [Middleware::class . '::admin'];
$adminPostAuth = [Middleware::class . '::admin', Middleware::class . '::csrf'];

$router->get('/admin/reviews', function() use ($adminController) {
    $adminController->pendingReviews();
}, $adminAuth);
$router->post('/admin/reviews', function() use ($adminController) {
    $adminController->pendingReviews();
}, $adminPostAuth);
$router->get('/admin/users', function() use ($adminUserController) {
    $adminUserController->users();
}, $adminAuth);
$router->post('/admin/users', function() use ($adminUserController) {
    $adminUserController->users();
}, $adminPostAuth);
$router->get('/admin/user/{id}', function($params) use ($adminUserController) {
    $adminUserController->userDetails($params['id']);
}, $adminAuth);
$router->get('/admin/words', function() use ($adminWordController) {
    $adminWordController->words(); 
}, $adminAuth);
$router->post('/admin/words', function() use ($adminWordController) {
    $adminWordController->editWord(); 
}, $adminPostAuth);
$router->get('/admin/proverbs', function() use ($adminProverbController) {
    $adminProverbController->proverbs();
}, $adminAuth);
$router->post('/admin/proverbs', function() use ($adminProverbController) {
    $adminProverbController->proverbs();
}, $adminPostAuth);
$router->post('/admin/delete-proverb', function() use ($adminProverbController) {
    $adminProverbController->deleteProverb();
}, $adminPostAuth);
$router->get('/admin/analytics', function() use ($adminController) {
    $adminController->analytics();
}, $adminAuth);
$router->get('/admin/settings', function() use ($adminSettingsController) {
    $adminSettingsController->settings();
}, $adminAuth);
$router->post('/admin/settings', function() use ($adminSettingsController) {
    $adminSettingsController->settings();
}, $adminPostAuth);

// --- Admin Management Pages ---
$router->get('/admin/dashboard', function() use ($adminController) {
    $_GET['action'] = 'dashboard';
    $adminController->dashboard();
}, $adminAuth);
$router->post('/admin/dashboard', function() use ($adminController) {
    $_GET['action'] = 'dashboard';
    $adminController->dashboard();
}, $adminPostAuth);
$router->get('/admin', function() use ($adminWordController) {
    $_GET['action'] = 'add-word';
    $adminWordController->addWordPage();
}, $adminAuth);
$router->get('/admin/add-word', function() use ($adminWordController) {
    $_GET['action'] = 'add-word';
    $adminWordController->addWordPage();
}, $adminAuth);
$router->post('/admin/add-word', function() use ($adminWordController) {
    $_GET['action'] = 'add-word';
    $adminWordController->addWordPage();
}, $adminPostAuth);
$router->get('/admin/add-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'add-proverb';
    $adminProverbController->addProverbPage();
}, $adminAuth);
$router->post('/admin/add-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'add-proverb';
    $adminProverbController->addProverbPage();
}, $adminPostAuth);
$router->get('/admin/edit-word', function() use ($adminWordController) {
    $_GET['action'] = 'edit-word';
    $adminWordController->editWord();
}, $adminAuth);
$router->post('/admin/edit-word', function() use ($adminWordController) {
    $_GET['action'] = 'edit-word';
    $adminWordController->editWord();
}, $adminPostAuth);
$router->post('/admin/delete-word', function() use ($adminWordController) {
    $adminWordController->deleteWord();
}, $adminPostAuth);
$router->get('/admin/edit-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'edit-proverb';
    $adminProverbController->editProverbPage();
}, $adminAuth);
$router->post('/admin/edit-proverb', function() use ($adminProverbController) {
    $_GET['action'] = 'edit-proverb';
    $adminProverbController->editProverbPage();
}, $adminPostAuth);

// --- Admin Quiz Management ---
$router->get('/admin/quizzes', function() use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->index();
}, $adminAuth);
$router->get('/admin/quiz/add', function() use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->add();
}, $adminAuth);
$router->post('/admin/quiz/add', function() use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->add();
}, $adminPostAuth);
$router->get('/admin/quiz/edit/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->edit($params['id']);
}, $adminAuth);
$router->post('/admin/quiz/edit/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->edit($params['id']);
}, $adminPostAuth);
$router->post('/admin/quiz/delete', function() use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->delete();
}, $adminPostAuth);

$router->get('/admin/quiz/questions/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->manageQuestions($params['id']);
}, $adminAuth);
$router->get('/admin/quiz/question/add/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->addQuestion($params['id']);
}, $adminAuth);
$router->post('/admin/quiz/question/add/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->addQuestion($params['id']);
}, $adminPostAuth);
$router->get('/admin/quiz/question/edit/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->editQuestion($params['id']);
}, $adminAuth);
$router->post('/admin/quiz/question/edit/{id}', function($params) use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->editQuestion($params['id']);
}, $adminPostAuth);
$router->post('/admin/quiz/question/delete', function() use ($pdo) {
    $quizAdmin = new AdminQuizController($pdo);
    $quizAdmin->deleteQuestion();
}, $adminPostAuth);

// --- Admin Contact Messages ---
$router->get('/admin/messages', function() use ($pdo) {
    $adminContact = new AdminContactController($pdo);
    $adminContact->index();
}, $adminAuth);
$router->get('/admin/message/view/{id}', function($params) use ($pdo) {
    $adminContact = new AdminContactController($pdo);
    $adminContact->view($params['id']);
}, $adminAuth);
$router->post('/admin/message/delete', function() use ($pdo) {
    $adminContact = new AdminContactController($pdo);
    $adminContact->delete();
}, $adminPostAuth);

// --- 404 Handler ---
$router->setNotFound(function() use ($controller) {
    http_response_code(404);
    include 'app/views/404.php';
});
