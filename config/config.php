<?php
// Hitung BASE_URL absolut yang stabil untuk semua halaman, termasuk yang berada di subfolder.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];

// Path proyek relatif terhadap DOCUMENT_ROOT
$docRoot  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$rootDir  = str_replace('\\', '/', realpath(__DIR__ . '/..')); // folder root proyek (satu level di atas config)
$basePath = rtrim(str_replace($docRoot, '', $rootDir), '/') . '/';

// Contoh hasil lokal: http://localhost/root/
define('BASE_URL', $protocol . '://' . $host . $basePath);

// Tambahkan ROOT_PATH agar mudah include file dari subfolder terdalam
define('ROOT_PATH', str_replace('\\', '/', $rootDir));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');

// Shortcut URL untuk aset
define('ASSETS_URL', BASE_URL . 'Resource/'); // jika aset Anda memang di /Resource
define('CSS_URL',    ASSETS_URL . 'css/');
define('JS_URL',     ASSETS_URL . 'js/');
define('IMG_URL',    ASSETS_URL . 'img/');
define('departemensamagri_URL', BASE_URL . 'pages/departemensamagri/');

// Info situs
define('SITE_NAME', 'HMTA ITERA');
define('SITE_DESCRIPTION', 'Himpunan Mahasiswa Teknik Pertambangan Institut Teknologi Sumatera');

// Kontak
define('CONTACT_EMAIL', 'hmtaitera@gmail.com');
define('CONTACT_PHONE', '0821-XXXX-XXXX');
define('CONTACT_ADDRESS', 'ITERA, Lampung');

// Sosial
define('LINKEDIN_URL', 'https://www.linkedin.com/company/himpunan-mahasiswa-teknik-pertambagan-itera-hmta-itera/');
define('INSTAGRAM_URL', 'https://www.instagram.com/@hmta_itera');
define('YOUTUBE_URL', 'https://www.youtube.com/@hmtaitera');
define('TIKTOK_URL', 'https://www.tiktok.com/@hmta.balakosa.itera');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Firebase Database configuration
require_once __DIR__ . '/database.php';

// Helper untuk URL aset yang aman (opsional tapi praktis)
if (!function_exists('asset_url')) {
    function asset_url(string $path): string {
        // Jika sudah absolute URL, langsung kembalikan
        if (preg_match('~^https?://~i', $path)) return $path;

        // Routing sederhana berdasarkan ekstensi
        if (preg_match('~\.css$~i', $path)) return CSS_URL . ltrim($path, '/');
        if (preg_match('~\.js$~i', $path))  return JS_URL  . ltrim($path, '/');
        if (preg_match('~\.(png|jpe?g|gif|svg|webp)$~i', $path)) return IMG_URL . ltrim($path, '/');

        // Default relatif ke BASE_URL
        return BASE_URL . ltrim($path, '/');
    }
}

// Autoloader untuk helper functions
function autoloadHelpers() {
    $helpersDir = __DIR__ . '/../includes/';

    // Hanya muat file helper yang aman. Hindari header.php / footer.php / template.
    $files = [];

    // Muat khusus functions.php jika ada
    $fn = $helpersDir . 'functions.php';
    if (is_file($fn)) $files[] = $fn;

    // Tambahan: file yang berakhiran .helper.php akan dimuat otomatis
    foreach (glob($helpersDir . '*.helper.php') ?: [] as $file) {
        $files[] = $file;
    }

    // Jika sebelumnya Anda menaruh helper lain di includes/, Anda bisa whitelist di sini:
    // $whitelist = ['some-helper.php', 'another-helper.php'];
    // foreach ($whitelist as $w) { if (is_file($helpersDir.$w)) $files[] = $helpersDir.$w; }

    foreach ($files as $file) {
        require_once $file;
    }
}

// Load helpers
autoloadHelpers();

// Timezone setting
date_default_timezone_set('Asia/Jakarta');

// Global error handler untuk Firebase operations
function handleFirebaseError($error) {
    error_log("Firebase Error: " . $error->getMessage());

    if (defined('DEBUG') && DEBUG) {
        die("Firebase Error: " . $error->getMessage());
    }

    // Redirect ke halaman error atau tampilkan pesan error yang user-friendly
    $_SESSION['error'] = 'Terjadi kesalahan sistem. Silakan coba lagi nanti.';
}