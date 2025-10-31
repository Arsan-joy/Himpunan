<?php
define('APP_DEBUG', true);
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

if (!defined('BASE_URL')) {
    // Sesuaikan dengan path Anda (dari screenshot   http://localhost/root/)
    define('BASE_URL', 'http://localhost/root/');
}

if (!defined('CSS_URL')) define('CSS_URL', BASE_URL . 'Resource/css/');
if (!defined('JS_URL'))  define('JS_URL',  BASE_URL . 'Resource/js/');
if (!defined('IMG_URL')) define('IMG_URL', BASE_URL . 'Resource/img/');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hmta');
define('DB_USER', 'root');   // XAMPP default
define('DB_PASS', '');       // XAMPP default: kosong
define('DB_CHARSET', 'utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}