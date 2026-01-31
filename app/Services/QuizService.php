<?php

namespace App\Services;

use Quiz;
use Exception;

class QuizService {
    private $pdo;
    private $quizModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        // In this project, models are not namespaced yet
        if (!class_exists('Quiz')) {
            require_once ROOT_PATH . '/app/models/Quiz.php';
        }
        $this->quizModel = new Quiz($pdo);
    }

    /**
     * Submit a quiz attempt and calculate score
     */
    public function submitQuiz($quizId, $userId, array $userAnswers, $timeTaken, $isDaily = false) {
        $quiz = $this->quizModel->find($quizId);
        if (!$quiz) {
            throw new Exception("Quiz not found");
        }

        $questions = $this->quizModel->getQuestions($quizId);
        
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
        if ($userId) {
            $resultData = [
                'user_id' => $userId,
                'quiz_id' => $quizId,
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

        return [
            'success' => true,
            'result_id' => $resultId,
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => round($percentage, 1),
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'passed' => $passed,
            'details' => $resultsDetail
        ];
    }
}
