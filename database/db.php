<?php
/**
 * database/db.php
 *
 * Singleton PDO connection menggunakan kredensial dari .env.
 * Fungsi db() adalah entry point utama untuk semua query di aplikasi.
 */

function connect_pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    // Konstanta DB_* di-define oleh database/config.php yang membaca .env
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // lempar PDOException, bukan silent fail
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // hasil fetch selalu array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                   // gunakan prepared statement native MySQL
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Log detail error — jangan tampilkan ke browser
        error_log('[connect_pdo] Koneksi database gagal: ' . $e->getMessage());

        if (defined('APP_ENV') && APP_ENV === 'development') {
            // Development: tampilkan pesan teknis untuk debugging
            http_response_code(500);
            exit('<pre style="background:#fff3f3;padding:1rem;border:1px solid #fca5a5">'
                . 'Database connection failed: '
                . htmlspecialchars($e->getMessage())
                . '</pre>');
        }

        // Production: tampilkan halaman error generik
        http_response_code(500);
        $errorPage = dirname(__DIR__) . '/error.php';
        if (file_exists($errorPage)) {
            include $errorPage;
        } else {
            exit('<h1>503 - Layanan Tidak Tersedia</h1><p>Terjadi masalah koneksi. Silakan coba beberapa saat lagi.</p>');
        }
        exit;
    }

    return $pdo;
}

/**
 * Shortcut global untuk mendapatkan koneksi PDO.
 * Penggunaan: db()->prepare(...), db()->query(...)
 */
function db(): PDO
{
    return connect_pdo();
}
