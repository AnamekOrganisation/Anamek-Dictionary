<?php

/**
 * Quiz Controller
 * Manages user interactions with the quiz system
 */
class QuizController extends BaseController {
    private $quizModel;
    private $quizService;

    public function __construct($pdo) {
        parent::__construct($pdo);
        require_once ROOT_PATH . '/app/models/Quiz.php';
        $this->quizModel = new Quiz($pdo);
        $this->quizService = new \App\Services\QuizService($pdo);
    }

    /**
     * Display list of available quizzes
     */
    public function index() {
        $difficulty = $_GET['difficulty'] ?? null;
        $categoryId = $_GET['category_id'] ?? null;
        
        $quizzes = $this->quizModel->getAll($difficulty, $categoryId);
        $categories = $this->quizModel->getCategories();
        
        $page_title = "Quiz & Jeux - Amawal";
        include ROOT_PATH . '/app/views/quiz/index.php';
    }

    /**
     * Show quiz landing page/intro
     */
    public function show($id) {
        $quiz = $this->quizModel->find($id);
        if (!$quiz) {
            $this->redirectWithError('/quizzes', 'Quiz introuvable.');
        }
        
        $questionsCount = count($this->quizModel->getQuestions($id));
        $bestResult = isset($_SESSION['user_id']) ? $this->quizModel->getUserBestResult($_SESSION['user_id'], $id) : null;
        
        $page_title = $quiz['title_fr'] . " - Amawal";
        include ROOT_PATH . '/app/views/quiz/show.php';
    }

    /**
     * Start playing the quiz
     */
    public function play($id) {
        $quiz = $this->quizModel->find($id);
        if (!$quiz) {
            $this->redirectWithError('/quizzes', 'Quiz introuvable.');
        }
        
        $questions = $this->quizModel->getQuestions($id);
        if (empty($questions)) {
            $this->redirectWithError('/quiz/' . $id, "Ce quiz n'a pas encore de questions.");
        }
        
        $page_title = "Jouer : " . $quiz['title_fr'];
        include ROOT_PATH . '/app/views/quiz/play.php';
    }

    /**
     * Handle quiz submission (usually via AJAX)
     */
    public function submit($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $userAnswers = $_POST['answers'] ?? [];
        $timeTaken = (int)($_POST['time_taken'] ?? 0);
        $isDaily = isset($_GET['daily']) || isset($_POST['is_daily']);
        $userId = $_SESSION['user_id'] ?? null;

        try {
            $result = $this->quizService->submitQuiz($id, $userId, $userAnswers, $timeTaken, $isDaily);
            
            if ($result['success'] && $result['result_id']) {
                $result['results_url'] = BASE_URL . '/quiz/results/' . $result['result_id'];
            }

            $this->jsonResponse($result);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show quiz results page
     */
    public function results($id) {
        $result = $this->quizModel->getResult($id);
        if (!$result) {
            $this->redirectWithError('/quizzes', 'Résultat introuvable.');
        }

        // Only allow user to see their own results (or admins)
        if ($_SESSION['user_id'] != $result['user_id'] && $_SESSION['user_type'] != 'admin') {
            $this->redirectWithError('/quizzes', 'Accès non autorisé.');
        }

        $quiz = $this->quizModel->find($result['quiz_id']);
        $questions = $this->quizModel->getQuestions($result['quiz_id']);
        
        $page_title = "Résultats : " . $result['quiz_title'];
        include ROOT_PATH . '/app/views/quiz/results.php';
    }

    /**
     * Display leaderboard for a quiz
     */
    public function leaderboard($id = null) {
        $quiz = $id ? $this->quizModel->find($id) : null;
        $leaderboard = $this->quizModel->getLeaderboard($id);
        
        $page_title = "Classement" . ($quiz ? " : " . $quiz['title_fr'] : "");
        include ROOT_PATH . '/app/views/quiz/leaderboard.php';
    }

    /**
     * Redirect to the daily challenge quiz
     */
    public function dailyChallenge() {
        $quizzes = $this->quizModel->getAll();
        if (empty($quizzes)) {
            $this->redirectWithError('/quizzes', 'Aucun quiz disponible.');
        }

        // Deterministic random selection based on date
        srand((int)date('Ymd'));
        $index = rand(0, count($quizzes) - 1);
        $dailyQuiz = $quizzes[$index];
        srand(); 
        
        header('Location: ' . BASE_URL . '/quiz/' . $dailyQuiz['id'] . '?daily=1'); // Keep header for complex URLs if needed, but could use redirectWith
        exit;
    }
}
