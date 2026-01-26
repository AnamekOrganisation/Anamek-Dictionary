<?php

namespace App\Services;

use App\Core\Validator;
use Exception;

class WordService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function validateWord(array $data) {
        $validator = new Validator($data);
        $rules = [
            'word_tfng' => 'required',
            'word_lat' => 'required',
            'translation_fr' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        return ['success' => true];
    }

    public function createWord(array $data) {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO words (
                word_tfng, word_lat, translation_fr,
                definition_tfng, definition_lat,
                plural_tfng, plural_lat,
                feminine_tfng, feminine_lat,
                annexed_tfng, annexed_lat,
                root_tfng, root_lat,
                part_of_speech,
                example_tfng, example_lat
            ) VALUES (
                :word_tfng, :word_lat, :translation_fr,
                :definition_tfng, :definition_lat,
                :plural_tfng, :plural_lat,
                :feminine_tfng, :feminine_lat,
                :annexed_tfng, :annexed_lat,
                :root_tfng, :root_lat,
                :part_of_speech,
                :example_tfng, :example_lat
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'word_tfng' => $data['word_tfng'],
                'word_lat' => $data['word_lat'],
                'translation_fr' => $data['translation_fr'],
                'definition_tfng' => $data['definition_tfng'] ?? null,
                'definition_lat' => $data['definition_lat'] ?? null,
                'plural_tfng' => $data['plural_tfng'] ?? null,
                'plural_lat' => $data['plural_lat'] ?? null,
                'feminine_tfng' => $data['feminine_tfng'] ?? null,
                'feminine_lat' => $data['feminine_lat'] ?? null,
                'annexed_tfng' => $data['annexed_tfng'] ?? null,
                'annexed_lat' => $data['annexed_lat'] ?? null,
                'root_tfng' => $data['root_tfng'] ?? null,
                'root_lat' => $data['root_lat'] ?? null,
                'part_of_speech' => $data['part_of_speech'] ?? null,
                'example_tfng' => $data['example_tfng'] ?? null,
                'example_lat' => $data['example_lat'] ?? null,
            ]);

            $wordId = $this->pdo->lastInsertId();

            if (!empty($data['synonyms']) || !empty($data['synonyms_lat'])) {
                $this->saveRelations($wordId, 'synonyms', $data['synonyms'] ?? '', $data['synonyms_lat'] ?? '');
            }

            if (!empty($data['antonyms']) || !empty($data['antonyms_lat'])) {
                $this->saveRelations($wordId, 'antonyms', $data['antonyms'] ?? '', $data['antonyms_lat'] ?? '');
            }

            $this->pdo->commit();
            return ['success' => true, 'id' => $wordId];

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    private function saveRelations($wordId, $table, $tfngStr, $latStr) {
        $tfng = array_filter(array_map('trim', explode(',', $tfngStr)));
        $lat = array_filter(array_map('trim', explode(',', $latStr)));
        $max = max(count($tfng), count($lat));

        $columnPrefix = ($table === 'synonyms') ? 'synonym' : 'antonym';
        $sql = "INSERT INTO $table (word_id, {$columnPrefix}_tfng, {$columnPrefix}_lat) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        for ($i = 0; $i < $max; $i++) {
            $stmt->execute([$wordId, $tfng[$i] ?? null, $lat[$i] ?? null]);
        }
    }
}
