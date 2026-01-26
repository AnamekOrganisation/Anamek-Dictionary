<?php

namespace App\Services;

use App\Core\Validator;

class ProverbService {
    private $proverbModel;

    public function __construct($pdo) {
        $this->proverbModel = new \Proverb($pdo);
    }

    public function getProverbs($search = '', $page = 1, $limit = 20) {
        if (!empty($search)) {
            $proverbs = $this->proverbModel->search($search, $page, $limit);
            $total = $this->proverbModel->countSearch($search);
        } else {
            $proverbs = $this->proverbModel->getPaginated($page, $limit);
            $total = $this->proverbModel->countAll();
        }

        return [
            'proverbs' => $proverbs,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ];
    }

    public function validateProverb(array $data) {
        $validator = new Validator($data);
        $rules = [
            'proverb_tfng' => 'required',
            'proverb_lat' => 'required',
            'translation_fr' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        return ['success' => true];
    }
}
