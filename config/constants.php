<?php
// Base paths
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}
if (!defined('APP_DIR')) {
    define('APP_DIR', ROOT_DIR . '/app');
}
if (!defined('CONFIG_DIR')) {
    define('CONFIG_DIR', ROOT_DIR . '/config');
}
if (!defined('PUBLIC_DIR')) {
    define('PUBLIC_DIR', ROOT_DIR . '/public');
}

// Web paths
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
if (!defined('PUBLIC_URL')) {
    define('PUBLIC_URL', BASE_URL . '/public');
}
if (!defined('CSS_URL')) {
    define('CSS_URL', PUBLIC_URL . '/css');
}
if (!defined('JS_URL')) {
    define('JS_URL', PUBLIC_URL . '/js');
}
if (!defined('IMG_URL')) {
    define('IMG_URL', PUBLIC_URL . '/img');
}

// Locale settings
if (!defined('DEFAULT_LOCALE')) {
define('DEFAULT_LOCALE', 'fr_FR');}
if (!defined('SUPPORTED_LANGUAGES')) {
define('SUPPORTED_LANGUAGES', ['fr_FR', 'zgh_Latn', 'ber_MA']);
}
