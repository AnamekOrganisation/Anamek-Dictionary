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

    public function createProverb(array $data) {
        $validation = $this->validateProverb($data);
        if (!$validation['success']) return $validation;

        try {
            $success = $this->proverbModel->create([
                'proverb_tfng' => $data['proverb_tfng'],
                'proverb_lat' => $data['proverb_lat'],
                'translation_fr' => $data['translation_fr'],
                'explanation' => $data['explanation'] ?? ''
            ]);
            return ['success' => $success];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function updateProverb($id, array $data) {
        $validation = $this->validateProverb($data);
        if (!$validation['success']) return $validation;

        try {
            $success = $this->proverbModel->update($id, [
                'proverb_tfng' => $data['proverb_tfng'],
                'proverb_lat' => $data['proverb_lat'],
                'translation_fr' => $data['translation_fr'],
                'explanation' => $data['explanation'] ?? ''
            ]);
            return ['success' => $success];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function deleteProverb($id) {
        try {
            $success = $this->proverbModel->delete($id);
            return ['success' => $success];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
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
