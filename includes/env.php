<?php
/**
 * Environment Loader
 * Memuat variabel dari file .env dan menyediakan fungsi env() untuk akses.
 */

/**
 * Parse dan muat file .env ke $_ENV dan putenv().
 * Jika file tidak ditemukan, log error kritis dan hentikan eksekusi.
 */
function load_env(string $envPath): void {
    if (!file_exists($envPath)) {
        error_log('[CRITICAL] File .env tidak ditemukan: ' . $envPath);
        http_response_code(500);
        // Tampilkan halaman error generik jika ada
        $errorPage = dirname(__DIR__) . '/error.php';
        if (file_exists($errorPage)) {
            include $errorPage;
        } else {
            echo '<h1>500 - Kesalahan Konfigurasi Server</h1>';
            echo '<p>Terjadi kesalahan konfigurasi. Silakan hubungi administrator.</p>';
        }
        exit;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        error_log('[CRITICAL] Gagal membaca file .env: ' . $envPath);
        http_response_code(500);
        exit;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        // Lewati komentar dan baris kosong
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Pisahkan key=value (hanya pada = pertama)
        $eqPos = strpos($line, '=');
        if ($eqPos === false) continue;

        $key   = trim(substr($line, 0, $eqPos));
        $value = trim(substr($line, $eqPos + 1));

        // Hapus kutip di sekitar value jika ada
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[-1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Set ke $_ENV dan putenv() jika belum ada
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Ambil nilai environment variable dengan fallback default.
 */
function env(string $key, mixed $default = null): mixed {
    // Cek $_ENV terlebih dahulu, lalu getenv()
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    $val = getenv($key);
    if ($val !== false) {
        return $val;
    }

    return $default;
}
