<?php
// Debug saat development
define('APP_DEBUG', true);
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    // Tampilkan fatal error agar tidak blank
    register_shutdown_function(function () {
        $e = error_get_last();
        if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            http_response_code(500);
            echo "<pre style='white-space:pre-wrap;background:#fff3f3;border:1px solid #fca5a5;padding:12px'>";
            echo "FATAL: {$e['message']}\nFile: {$e['file']}\nLine: {$e['line']}";
            echo "</pre>";
        }
    });
}

// Sesuaikan dengan path root project Anda
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/root/');

// URL aset umum
if (!defined('CSS_URL')) define('CSS_URL', BASE_URL . 'Resource/css/');
if (!defined('JS_URL'))  define('JS_URL',  BASE_URL . 'Resource/js/');
if (!defined('IMG_URL')) define('IMG_URL', BASE_URL . 'Resource/img/');

// KONFIGURASI UPLOAD (penting untuk fitur upload langsung)
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../uploads'); // folder fisik
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', BASE_URL . 'uploads/');    // URL publik

// Kredensial MySQL (ubah jika perlu)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hmta');
define('DB_USER', 'root');     // atau 'hmta_user'
define('DB_PASS', '');         // sesuaikan
define('DB_CHARSET', 'utf8mb4');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Atur identitas Super Admin (hanya 1 akun ini yang boleh role 'super_admin')
if (!defined('SUPER_ADMIN_USERNAME')) define('SUPER_ADMIN_USERNAME', 'superadmin'); // ganti sesuai keinginan
if (!defined('SUPER_ADMIN_PASSWORD')) define('SUPER_ADMIN_PASSWORD', 'admin0105');  // ganti sesuai keinginan
