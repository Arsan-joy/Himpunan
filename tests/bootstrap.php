<?php
/**
 * PHPUnit Bootstrap
 * Setup minimal untuk menjalankan tests tanpa full app bootstrap.
 */

// Konstanta dasar yang dibutuhkan tests
if (!defined('BASE_URL'))   define('BASE_URL',   'http://localhost/root/');
if (!defined('LOG_DIR'))    define('LOG_DIR',    __DIR__ . '/../logs');
if (!defined('CACHE_DIR'))  define('CACHE_DIR',  sys_get_temp_dir() . '/hmta_test_cache');
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', sys_get_temp_dir() . '/hmta_test_uploads');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', BASE_URL . 'uploads/');

// Mock fungsi yang bergantung pada database/session untuk testing unit
if (!function_exists('log_security')) {
    function log_security(string $event, array $context = []): void { /* no-op in tests */ }
}

// Pastikan folder test ada
foreach ([LOG_DIR, CACHE_DIR, UPLOAD_DIR] as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
}
