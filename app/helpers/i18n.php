<?php
/**
 * Internationalization Helper (PHP Array Based)
 */

/**
 * Compatibility: setupLocale is no longer needed but kept to avoid fatal errors in views.
 */
function setupLocale($lang = 'fr_FR') {
    // Logic removed in favor of simpler array-based translations.
    return;
}

/**
 * Translation function
 */
function __($text) {
    if (empty($text)) return '';
    
    static $translations = [];
    $lang = $_SESSION['lang'] ?? 'fr_FR';
    
    if (!isset($translations[$lang])) {
        // Look for the translation file in the locale directory
        $path = ROOT_PATH . "/locale/{$lang}/translations.php";
        if (file_exists($path)) {
            $translations[$lang] = require $path;
        } else {
            $translations[$lang] = [];
        }
    }
    
    return $translations[$lang][$text] ?? $text;
}

/**
 * Set the current application language
 */
function setLanguage($lang) {
    $supported = ['fr_FR', 'ber_MA', 'ber_LAT', 'zgh_Latn']; 
    if (in_array($lang, $supported)) {
        $_SESSION['lang'] = $lang;
        
        // Handle script preferences if necessary
        if ($lang === 'ber_MA') {
            $_SESSION['script'] = 'tfng';
        } else {
            unset($_SESSION['script']);
        }
    }
}

/**
 * Get current language code (e.g., 'fr_FR')
 */
function getCurrentLanguage() {
    return $_SESSION['lang'] ?? 'fr_FR';
}

/**
 * Get current script (e.g., 'tfng', 'latn')
 */
function getCurrentScript() {
    return $_SESSION['script'] ?? 'latn';
}

/**
 * Compatibility function (deprecated)
 */
function getLanguage() { return getCurrentLanguage(); }
function getScript() { return getCurrentScript(); }

/**
 * Utility: check for mobile device
 */
function isMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $mobileAgents = [
        'Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi',
        'iPhone', 'iPod', 'iPad', 'Windows Phone', 'webOS', 'IEMobile', 'MeeGo', 'Nokia',
        'Samsung', 'HTC', 'SonyEricsson', 'PlayBook', 'BB10'
    ];
    foreach ($mobileAgents as $device) {
        if (stripos($userAgent, $device) !== false) return true;
    }
    return false;
}
