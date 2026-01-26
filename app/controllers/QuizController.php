<?php

/**
 * Quiz Controller
 * Manages user interactions with the quiz system
 */
class QuizController {
    private $pdo;
    private $quizModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        require_once ROOT_PATH . '/app/models/Quiz.php';
        $this->quizModel = new Quiz($pdo);
        
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
            header('Location: ' . BASE_URL . '/quizzes');
            exit;
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
            header('Location: ' . BASE_URL . '/quizzes');
            exit;
        }
        
        $questions = $this->quizModel->getQuestions($id);
        if (empty($questions)) {
            $_SESSION['error'] = "Ce quiz n'a pas encore de questions.";
            header('Location: ' . BASE_URL . '/quiz/' . $id);
            exit;
        }
        
        // Shuffle questions or options if needed (shuffle in PHP or just random in SQL)
        // For now, keep display_order
        
        $page_title = "Jouer : " . $quiz['title_fr'];
        include ROOT_PATH . '/app/views/quiz/play.php';
    }

    /**
     * Handle quiz submission (usually via AJAX)
     */
    public function submit($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $quiz = $this->quizModel->find($id);
        $questions = $this->quizModel->getQuestions($id);
        $userAnswers = $_POST['answers'] ?? [];
        $timeTaken = (int)($_POST['time_taken'] ?? 0);
        
        $score = 0;
        $correctCount = 0;
        $totalPoints = 0;
        $totalQuestions = count($questions);
        $resultsDetail = [];

        foreach ($questions as $question) {
            $totalPoints += $question['points'];
            $qId = $question['id'];
            $userAnswer = $userAnswers[$qId] ?? null;
            $isCorrect = (trim($userAnswer) == trim($question['correct_answer']));
            
            if ($isCorrect) {
                $score += $question['points'];
                $correctCount++;
            }
            
            $resultsDetail[] = [
                'question_id' => $qId,
                'user_answer' => $userAnswer,
                'correct_answer' => $question['correct_answer'],
                'is_correct' => $isCorrect
            ];
        }

        $percentage = ($totalPoints > 0) ? ($score / $totalPoints) * 100 : 0;
        $passed = $percentage >= ($quiz['passing_score'] ?? 70);

        $resultId = null;
        if (isset($_SESSION['user_id'])) {
            $isDaily = isset($_GET['daily']) || isset($_POST['is_daily']); // Support both for flexibility
            $resultData = [
                'user_id' => $_SESSION['user_id'],
                'quiz_id' => $id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctCount,
                'percentage' => $percentage,
                'time_taken_seconds' => $timeTaken,
                'answers' => $resultsDetail,
                'passed' => $passed,
                'is_daily' => $isDaily
            ];
            $resultId = $this->quizModel->saveResult($resultData);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'result_id' => $resultId,
            'results_url' => BASE_URL . '/quiz/results/' . $resultId,
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => round($percentage, 1),
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'passed' => $passed,
            'details' => $resultsDetail
        ]);
        exit;
    }

    /**
     * Show quiz results page
     */
    public function results($id) {
        $result = $this->quizModel->getResult($id);
        if (!$result) {
            header('Location: ' . BASE_URL . '/quizzes');
            exit;
        }

        // Only allow user to see their own results (or admins)
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_id'] != $result['user_id'] && $_SESSION['user_type'] != 'admin')) {
            header('Location: ' . BASE_URL . '/quizzes');
            exit;
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
            header('Location: ' . BASE_URL . '/quizzes');
            exit;
        }

        // Deterministic random selection based on date
        // Note: srand() affects subsequent rand() calls in the same request
        srand((int)date('Ymd'));
        $index = rand(0, count($quizzes) - 1);
        $dailyQuiz = $quizzes[$index];
        
        // Reset seed to avoid affecting other parts of the app if needed
        srand(); 
        
        header('Location: ' . BASE_URL . '/quiz/' . $dailyQuiz['id'] . '?daily=1');
        exit;
    }
}
