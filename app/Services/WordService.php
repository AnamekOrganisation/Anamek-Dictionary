<?php

namespace App\Services;

use App\Core\Validator;
use Exception;
use PDO;

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

    public function updateWord($id, array $data) {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE words SET 
                word_tfng = ?, word_lat = ?, translation_fr = ?, 
                definition_tfng = ?, definition_lat = ?, 
                plural_tfng = ?, plural_lat = ?, 
                feminine_tfng = ?, feminine_lat = ?, 
                annexed_tfng = ?, annexed_lat = ?, 
                root_tfng = ?, root_lat = ?, 
                part_of_speech = ?, 
                example_tfng = ?, example_lat = ? 
                WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['word_tfng'] ?? '', $data['word_lat'] ?? '', $data['translation_fr'] ?? '', 
                $data['definition_tfng'] ?? '', $data['definition_lat'] ?? '',
                $data['plural_tfng'] ?? '', $data['plural_lat'] ?? '', 
                $data['feminine_tfng'] ?? '', $data['feminine_lat'] ?? '',
                $data['annexed_tfng'] ?? '', $data['annexed_lat'] ?? '', 
                $data['root_tfng'] ?? '', $data['root_lat'] ?? '',
                $data['part_of_speech'] ?? '', 
                $data['example_tfng'] ?? '', $data['example_lat'] ?? '', 
                $id
            ]);

            // Relations
            $this->updateRelations($id, 'synonyms', $data['synonyms_tfng'] ?? [], $data['synonyms_lat'] ?? []);
            $this->updateRelations($id, 'antonyms', $data['antonyms_tfng'] ?? [], $data['antonyms_lat'] ?? []);
            $this->updateExamples($id, $data);

            $this->pdo->commit();
            return ['success' => true];

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function deleteWord($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM words WHERE id = ?");
            if ($stmt->execute([$id])) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to delete word']];
        } catch (Exception $e) {
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

    private function updateRelations($wordId, $table, $tfngData, $latData) {
        // Clear existing
        $this->pdo->prepare("DELETE FROM $table WHERE word_id = ?")->execute([$wordId]);

        $tfng = is_array($tfngData) ? $tfngData : (is_string($tfngData) ? array_filter(array_map('trim', explode(',', $tfngData))) : []);
        $lat = is_array($latData) ? $latData : (is_string($latData) ? array_filter(array_map('trim', explode(',', $latData))) : []);
        
        $max = max(count($tfng), count($lat));
        if ($max === 0) return;

        $columnPrefix = ($table === 'synonyms') ? 'synonym' : 'antonym';
        $stmt = $this->pdo->prepare("INSERT INTO $table (word_id, {$columnPrefix}_tfng, {$columnPrefix}_lat) VALUES (?, ?, ?)");
        
        for ($i = 0; $i < $max; $i++) {
            $t = !empty($tfng[$i]) ? trim($tfng[$i]) : null;
            $l = !empty($lat[$i]) ? trim($lat[$i]) : null;
            if ($t !== null || $l !== null) {
                $stmt->execute([$wordId, $t, $l]);
            }
        }
    }

    private function updateExamples($wordId, $data) {
        // IDs of examples that should be kept
        $submittedIds = array_filter($data['example_ids'] ?? []);
        
        if (!empty($submittedIds)) {
            $placeholders = str_repeat('?,', count($submittedIds) - 1) . '?';
            $stmt = $this->pdo->prepare("DELETE FROM examples WHERE word_id = ? AND id NOT IN ($placeholders)");
            $stmt->execute(array_merge([$wordId], array_values($submittedIds)));
        } else {
            $stmt = $this->pdo->prepare("DELETE FROM examples WHERE word_id = ?");
            $stmt->execute([$wordId]);
        }

        $tfngArray = $data['examples_tfng'] ?? [];
        $latArray = $data['examples_lat'] ?? [];
        $frArray = $data['examples_fr'] ?? [];
        $idArray = $data['example_ids'] ?? [];

        foreach ($tfngArray as $i => $tfng) {
            $lat = $latArray[$i] ?? '';
            $fr = $frArray[$i] ?? '';
            $exampleId = $idArray[$i] ?? '';

            if (empty(trim($tfng)) && empty(trim($lat))) continue;

            if (!empty($exampleId)) {
                $stmt = $this->pdo->prepare("UPDATE examples SET example_tfng = ?, example_lat = ?, example_fr = ? WHERE id = ? AND word_id = ?");
                $stmt->execute([$tfng, $lat, $fr, $exampleId, $wordId]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO examples (word_id, example_tfng, example_lat, example_fr) VALUES (?, ?, ?, ?)");
                $stmt->execute([$wordId, $tfng, $lat, $fr]);
            }
        }
    }
}
