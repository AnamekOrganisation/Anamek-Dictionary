<?php
/**
 * Security Helpers
 */

/**
 * Escape HTML output
 * 
 * @param mixed $value
 * @return string
 */
function e($value): string {
    if ($value === null) {
        return '';
    }
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Validate CSRF token
 * 
 * @param string|null $token
 * @return bool
 */
function verify_csrf(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token field
 * 
 * @return string
 */
function csrf_field(): string {
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}
