<?php

namespace App\Core;

class Validator {
    private $errors = [];
    private $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function validate(array $rules) {
        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $items = explode('|', $fieldRules);

            foreach ($items as $rule) {
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $param) = explode(':', $rule);
                } else {
                    $ruleName = $rule;
                    $param = null;
                }

                $this->applyRule($field, $ruleName, $value, $param);
            }
        }

        return empty($this->errors);
    }

    private function applyRule($field, $rule, $value, $param) {
        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "The $field field is required.");
                }
                break;
            case 'min':
                if (strlen($value) < $param) {
                    $this->addError($field, "The $field must be at least $param characters.");
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The $field must be a valid email address.");
                }
                break;
            case 'match':
                if ($value !== ($this->data[$param] ?? null)) {
                    $this->addError($field, "The $field does not match $param.");
                }
                break;
            case 'regex':
                if (!preg_match($param, $value)) {
                    $this->addError($field, "The $field format is invalid.");
                }
                break;
        }
    }

    private function addError($field, $message) {
        $this->errors[$field][] = $message;
    }

    public function getErrors() {
        $flatErrors = [];
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $flatErrors[] = $error;
            }
        }
        return $flatErrors;
    }
}
