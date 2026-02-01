<?php

class AdminQuizController extends BaseController {
    private $quizModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        require_once ROOT_PATH . '/app/models/Quiz.php';
        $this->quizModel = new Quiz($pdo);
    }

    /**
     * List all quizzes for management
     */
    public function index() {
        $quizzes = $this->quizModel->getAll(null, null); // Get all, active or not (getAll filter is for active only currently, let's check)
        // Wait, getAll only returns active: WHERE q.is_active = 1
        // I should probably add an adminGetAll to the model or modify getAll
        
        $sql = "SELECT q.*, u.username as creator_name, c.category_name_fr as category_name 
                FROM quizzes q
                LEFT JOIN users u ON q.created_by = u.id
                LEFT JOIN word_categories c ON q.category_id = c.id
                ORDER BY q.created_at DESC";
        $stmt = $this->pdo->query($sql);
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = 'Gestion des Quizz';
        $current_page = 'quizzes';
        require_once ROOT_PATH . '/app/views/admin/quizzes/index.php';
    }

    /**
     * Show add quiz form / handle submission
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title_fr' => $_POST['title_fr'],
                'description_fr' => $_POST['description_fr'],
                'category_id' => $_POST['category_id'],
                'difficulty_level' => $_POST['difficulty_level'],
                'estimated_time' => $_POST['estimated_time'],
                'passing_score' => $_POST['passing_score'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'created_by' => $_SESSION['user_id']
            ];
            
            $id = $this->quizModel->create($data);
            if ($id) {
                $this->redirectWith('/admin/quiz/questions/' . $id, 'Quiz créé avec succès. Ajoutez maintenant des questions.');
            } else {
                $this->redirectWithError('/admin/quizzes', 'Erreur lors de la création du quiz.');
            }
        }
        
        $categories = $this->quizModel->getAllCategories();
        $page_title = 'Ajouter un Quiz';
        $current_page = 'quizzes';
        require_once ROOT_PATH . '/app/views/admin/quizzes/form.php';
    }

    /**
     * Edit quiz / handle submission
     */
    public function edit($id) {
        $quiz = $this->quizModel->find($id);
        if (!$quiz) {
            $this->redirectWithError('/admin/quizzes', 'Quiz introuvable.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title_fr' => $_POST['title_fr'],
                'description_fr' => $_POST['description_fr'],
                'category_id' => $_POST['category_id'],
                'difficulty_level' => $_POST['difficulty_level'],
                'estimated_time' => $_POST['estimated_time'],
                'passing_score' => $_POST['passing_score'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0
            ];
            
            if ($this->quizModel->update($id, $data)) {
                $this->redirectWith('/admin/quizzes', 'Quiz mis à jour.');
            } else {
                $this->redirectWithError('/admin/quizzes', 'Erreur lors de la mise à jour.');
            }
        }
        
        $categories = $this->quizModel->getAllCategories();
        $page_title = 'Modifier le Quiz';
        $current_page = 'quizzes';
        require_once ROOT_PATH . '/app/views/admin/quizzes/form.php';
    }

    /**
     * Delete a quiz
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            if ($this->quizModel->delete($_POST['id'])) {
                $this->redirectWith('/admin/quizzes', 'Quiz supprimé.');
            } else {
                $this->redirectWithError('/admin/quizzes', 'Erreur lors de la suppression.');
            }
        }
        $this->redirectWithError('/admin/quizzes', 'Action non autorisée.');
    }

    /**
     * Manage questions for a quiz
     */
    public function manageQuestions($quizId) {
        $quiz = $this->quizModel->find($quizId);
        if (!$quiz) {
            $this->redirectWithError('/admin/quizzes', 'Quiz introuvable.');
        }

        $questions = $this->quizModel->getQuestions($quizId);
        $page_title = 'Questions : ' . $quiz['title_fr'];
        $current_page = 'quizzes';
        require_once ROOT_PATH . '/app/views/admin/quizzes/questions.php';
    }

    /**
     * Add a question
     */
    public function addQuestion($quizId) {
        $quiz = $this->quizModel->find($quizId);
        if (!$quiz) {
            $this->redirectWithError('/admin/quizzes', 'Quiz introuvable.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $options = array_values($_POST['options']);
            $correctIndex = (int)($_POST['correct_answer_index'] ?? 0);
            
            $data = [
                'quiz_id' => $quizId,
                'question_text_fr' => $_POST['question_text_fr'],
                'question_text_tfng' => $_POST['question_text_tfng'],
                'options' => $options,
                'correct_answer' => $options[$correctIndex] ?? '',
                'points' => $_POST['points'],
                'display_order' => $_POST['display_order']
            ];
            
            if ($this->quizModel->addQuestion($data)) {
                $this->redirectWith('/admin/quiz/questions/' . $quizId, 'Question ajoutée.');
            } else {
                $this->redirectWithError('/admin/quiz/questions/' . $quizId, 'Erreur lors de l\'ajout.');
            }
        }

        $page_title = 'Ajouter une Question';
        $current_page = 'quizzes';
        require_once ROOT_PATH . '/app/views/admin/quizzes/question-form.php';
    }

    /**
     * Edit a question
     */
    public function editQuestion($questionId) {
        // We need a findQuestion method in the model
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_questions WHERE id = ?");
        $stmt->execute([$questionId]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$question) {
            $this->redirectWithError('/admin/quizzes', 'Question introuvable.');
        }

        if ($question['options']) {
            $question['options'] = json_decode($question['options'], true);
        }

        $quizId = $question['quiz_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $options = array_values($_POST['options']);
            $correctIndex = (int)($_POST['correct_answer_index'] ?? 0);

            $data = [
                'question_text_fr' => $_POST['question_text_fr'],
                'question_text_tfng' => $_POST['question_text_tfng'],
                'options' => $options,
                'correct_answer' => $options[$correctIndex] ?? '',
                'points' => $_POST['points'],
                'display_order' => $_POST['display_order']
            ];
            
            if ($this->quizModel->updateQuestion($questionId, $data)) {
                $this->redirectWith('/admin/quiz/questions/' . $quizId, 'Question mise à jour.');
            } else {
                $this->redirectWithError('/admin/quiz/questions/' . $quizId, 'Erreur lors de la mise à jour.');
            }
        }

        $page_title = 'Modifier la Question';
        $current_page = 'quizzes';
        require_once ROOT_PATH . '/app/views/admin/quizzes/question-form.php';
    }

    /**
     * Delete a question
     */
    public function deleteQuestion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $stmt = $this->pdo->prepare("SELECT quiz_id FROM quiz_questions WHERE id = ?");
            $stmt->execute([$id]);
            $quizId = $stmt->fetchColumn();
            
            if ($this->quizModel->deleteQuestion($id)) {
                $this->redirectWith('/admin/quiz/questions/' . $quizId, 'Question supprimée.');
            } else {
                $this->redirectWithError('/admin/quiz/questions/' . $quizId, 'Erreur lors de la suppression.');
            }
        }
        $this->redirectWithError('/admin/quizzes', 'Action non autorisée.');
    }
}
