<?php
/**
 * Security Logger
 * Mencatat aktivitas keamanan ke file log dengan rotasi otomatis.
 */

if (!defined('LOG_DIR')) {
    define('LOG_DIR', dirname(__DIR__) . '/logs');
}

/**
 * Catat event keamanan ke file log dalam format JSON per baris (NDJSON).
 *
 * @param string $event    Nama event (mis. 'login_failed', 'csrf_violation', 'upload_rejected')
 * @param array  $context  Data konteks tambahan
 */
if (!function_exists('log_security')) {
    function log_security(string $event, array $context = []): void {
        $logFile = rtrim(LOG_DIR, '/\\') . '/security.log';

        // Buat folder logs jika belum ada
        if (!is_dir(LOG_DIR)) {
            @mkdir(LOG_DIR, 0775, true);
        }

        // Rotasi log jika ukuran > 10MB
        if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) {
            log_rotate($logFile);
        }

        $entry = json_encode([
            'timestamp'  => date('c'),
            'event'      => $event,
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'url'        => ($_SERVER['REQUEST_URI'] ?? ''),
            'method'     => ($_SERVER['REQUEST_METHOD'] ?? ''),
            'context'    => $context,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($entry === false) {
            error_log('[logger] Gagal encode JSON untuk event: ' . $event);
            return;
        }

        $result = @file_put_contents($logFile, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            // Fallback ke PHP error log
            error_log('[security] ' . $event . ' | ' . json_encode($context));
        }
    }
}

/**
 * Rotasi file log: rename file lama, buat file baru kosong.
 * Pertahankan maksimal $maxFiles file historis.
 */
if (!function_exists('log_rotate')) {
    function log_rotate(string $logFile, int $maxSizeMB = 10, int $maxFiles = 5): void {
        if (!file_exists($logFile)) return;

        // Hapus file historis tertua jika sudah mencapai batas
        for ($i = $maxFiles; $i >= 1; $i--) {
            $old = $logFile . '.' . $i;
            if (file_exists($old)) {
                if ($i === $maxFiles) {
                    @unlink($old);
                } else {
                    @rename($old, $logFile . '.' . ($i + 1));
                }
            }
        }

        // Rename file aktif ke .1
        @rename($logFile, $logFile . '.1');

        // Buat file log baru yang kosong
        @touch($logFile);
    }
}
