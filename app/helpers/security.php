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
/**
 * Generate an absolute URL
 * 
 * @param string $path
 * @return string
 */
function absolute_url(string $path = ''): string {
    $baseUrl = getenv('APP_URL');
    
    if (!$baseUrl) {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $subfolder = rtrim(BASE_URL, '/');
        $baseUrl = "{$protocol}://{$host}{$subfolder}";
    }
    
    $baseUrl = rtrim($baseUrl, '/');
    $path = ltrim($path, '/');
    
    // If the path already starts with the subfolder part of the BASE_URL, 
    // and we are auto-generating based on host (not explicit APP_URL), 
    // we need to be careful not to duplicate it.
    // However, if we use APP_URL, we just append.
    
    return "{$baseUrl}/{$path}";
}
