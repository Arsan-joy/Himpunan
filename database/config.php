<?php
/**
 * Bootstrap konfigurasi aplikasi.
 * - Load .env
 * - Konfigurasi error reporting berdasarkan APP_ENV
 * - Konfigurasi session yang aman
 * - Kirim security headers
 */

// Load environment loader
require_once __DIR__ . '/../includes/env.php';

// Muat file .env dari root proyek
$envFile = __DIR__ . '/../.env';
load_env($envFile);

// ============================================================
// APP_ENV & Error Reporting
// ============================================================
$appEnv   = env('APP_ENV', 'production');
$appDebug = filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);

if (!defined('APP_ENV'))   define('APP_ENV',   $appEnv);
if (!defined('APP_DEBUG')) define('APP_DEBUG', $appDebug);

if ($appEnv === 'development' || $appDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');

    // Tampilkan halaman error generik untuk fatal error di production
    register_shutdown_function(function () {
        $e = error_get_last();
        if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            http_response_code(500);
            $errorPage = __DIR__ . '/../error.php';
            if (file_exists($errorPage)) {
                include $errorPage;
            } else {
                echo '<h1>500 - Terjadi Kesalahan</h1><p>Terjadi kesalahan sistem. Tim kami sedang menangani masalah ini.</p>';
            }
        }
    });
}

// ============================================================
// URL & Path Constants
// ============================================================
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/root/');
if (!defined('CSS_URL'))  define('CSS_URL',  BASE_URL . 'Resource/css/');
if (!defined('JS_URL'))   define('JS_URL',   BASE_URL . 'Resource/js/');
if (!defined('IMG_URL'))  define('IMG_URL',  BASE_URL . 'Resource/img/');

if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../uploads');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', BASE_URL . 'uploads/');

// ============================================================
// Database Constants (dari .env)
// ============================================================
if (!defined('DB_HOST'))    define('DB_HOST',    env('DB_HOST',    '127.0.0.1'));
if (!defined('DB_NAME'))    define('DB_NAME',    env('DB_NAME',    'hmta'));
if (!defined('DB_USER'))    define('DB_USER',    env('DB_USER',    'root'));
if (!defined('DB_PASS'))    define('DB_PASS',    env('DB_PASS',    ''));
if (!defined('DB_CHARSET')) define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// ============================================================
// Super Admin (dari .env)
// ============================================================
if (!defined('SUPER_ADMIN_USERNAME')) define('SUPER_ADMIN_USERNAME', env('SUPER_ADMIN_USERNAME', ''));
if (!defined('SUPER_ADMIN_PASSWORD')) define('SUPER_ADMIN_PASSWORD', env('SUPER_ADMIN_PASSWORD', ''));

// ============================================================
// Session Configuration (aman)
// ============================================================
$sessionLifetime = (int)env('SESSION_LIFETIME', 7200);
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', (string)$sessionLifetime);
ini_set('session.cookie_lifetime', '0'); // Session cookie (hilang saat browser ditutup)

if ($isHttps) {
    ini_set('session.cookie_secure', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Security Headers
// ============================================================
require_once __DIR__ . '/../includes/security_headers.php';
$isAdminPage = str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/');
send_security_headers($isAdminPage);
