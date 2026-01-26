<?php

namespace App\Services;

use App\Core\Validator;

class AuthService {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new \User($pdo);
    }

    public function registerUser(array $data) {
        $validator = new Validator($data);
        $rules = [
            'username' => 'required|min:3|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirm' => 'required|match:password'
        ];

        if (!$validator->validate($rules)) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        $user = $this->userModel->register($data);
        if (!$user) {
            return ['success' => false, 'errors' => ['Registration failed. Username or email may already exist.']];
        }

        return ['success' => true, 'user' => $user];
    }

    public function loginUser($emailOrUsername, $password) {
        $user = $this->userModel->login($emailOrUsername, $password);
        if (!$user) {
            return ['success' => false, 'errors' => ['Invalid email/username or password']];
        }

        return ['success' => true, 'user' => $user];
    }
}
