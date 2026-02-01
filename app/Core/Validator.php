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
            case 'max':
                if (strlen($value) > $param) {
                    $this->addError($field, "The $field must not exceed $param characters.");
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
            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, "The $field must be numeric.");
                }
                break;
            case 'alpha':
                if (!ctype_alpha($value)) {
                    $this->addError($field, "The $field must contain only letters.");
                }
                break;
            case 'alphanumeric':
                if (!ctype_alnum($value)) {
                    $this->addError($field, "The $field must contain only alphanumeric characters.");
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "The $field must be a valid URL.");
                }
                break;
            case 'unique':
                // SECURITY: Validate against whitelist to prevent SQL injection
                $allowedTables = ['users', 'words', 'proverbs'];
                $parts = explode(':', $param);
                if (count($parts) === 2 && in_array($parts[0], $allowedTables, true)) {
                    $table = $parts[0];
                    $column = $parts[1];
                    // Only allow alphanumeric + underscore for column names
                    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                        $this->addError($field, "Invalid validation configuration.");
                        break;
                    }
                    // Note: Actual DB check should be done in service layer
                    // This is a schema validation only
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
