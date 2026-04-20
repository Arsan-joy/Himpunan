<?php
/**
 * Rate Limiter
 * Membatasi percobaan login berdasarkan IP menggunakan tabel database login_attempts.
 */

require_once __DIR__ . '/logger.php';

/**
 * Cek apakah IP diblokir untuk action tertentu.
 *
 * @param string $ip            Alamat IP
 * @param string $action        Nama action (mis. 'admin_login')
 * @param int    $maxAttempts   Maksimal percobaan sebelum diblokir
 * @param int    $windowSeconds Jendela waktu dalam detik
 * @return bool True jika diblokir
 */
function rate_limit_is_blocked(string $ip, string $action = 'admin_login', int $maxAttempts = 5, int $windowSeconds = 900): bool {
    try {
        $pdo  = db();
        $since = date('Y-m-d H:i:s', time() - $windowSeconds);

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS cnt FROM login_attempts
             WHERE ip_address = ? AND action = ? AND attempted_at >= ?"
        );
        $stmt->execute([$ip, $action, $since]);
        $row = $stmt->fetch();

        return (int)($row['cnt'] ?? 0) >= $maxAttempts;
    } catch (Throwable $e) {
        error_log('[rate_limiter] Gagal cek blokir: ' . $e->getMessage());
        return false; // fail open — jangan blokir jika DB error
    }
}

/**
 * Catat percobaan login gagal ke database.
 *
 * @param string $ip       Alamat IP
 * @param string $action   Nama action
 * @param string $username Username yang dicoba (opsional)
 */
function rate_limit_record_attempt(string $ip, string $action = 'admin_login', string $username = ''): void {
    try {
        $pdo = db();
        $stmt = $pdo->prepare(
            "INSERT INTO login_attempts (ip_address, username, action, attempted_at, user_agent)
             VALUES (?, ?, ?, NOW(), ?)"
        );
        $stmt->execute([
            $ip,
            $username,
            $action,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);
    } catch (Throwable $e) {
        error_log('[rate_limiter] Gagal catat attempt: ' . $e->getMessage());
        // fail open — lanjutkan meski gagal catat
    }
}

/**
 * Reset hitungan percobaan gagal untuk IP tertentu setelah login berhasil.
 *
 * @param string $ip     Alamat IP
 * @param string $action Nama action
 */
function rate_limit_reset(string $ip, string $action = 'admin_login'): void {
    try {
        $pdo = db();
        $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND action = ?")
            ->execute([$ip, $action]);
    } catch (Throwable $e) {
        error_log('[rate_limiter] Gagal reset attempts: ' . $e->getMessage());
    }
}

/**
 * Cek dan catat attempt sekaligus. Return true jika diblokir.
 *
 * @param string $ip            Alamat IP
 * @param string $action        Nama action
 * @param int    $maxAttempts   Maksimal percobaan
 * @param int    $windowSeconds Jendela waktu dalam detik
 * @return bool True jika diblokir
 */
function rate_limit_check(string $ip, string $action = 'admin_login', int $maxAttempts = 5, int $windowSeconds = 900): bool {
    return rate_limit_is_blocked($ip, $action, $maxAttempts, $windowSeconds);
}
