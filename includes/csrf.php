<?php
/**
 * CSRF Protection
 * Menghasilkan dan memverifikasi CSRF token untuk melindungi form dari serangan CSRF.
 */

require_once __DIR__ . '/logger.php';

/**
 * Dapatkan atau generate CSRF token.
 * Token disimpan di $_SESSION['csrf_token'].
 *
 * @return string Token 32 karakter hexadecimal
 */
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Return HTML hidden input field dengan CSRF token.
 *
 * @return string HTML input hidden
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verifikasi CSRF token dari POST request.
 * Menggunakan hash_equals() untuk mencegah timing attack.
 *
 * @return bool True jika token valid, false jika tidak
 */
function csrf_verify(): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $submitted = (string)($_POST['csrf_token'] ?? '');
    $stored    = (string)($_SESSION['csrf_token'] ?? '');

    if ($submitted === '' || $stored === '') {
        log_security('csrf_violation', [
            'reason' => 'token_missing',
            'url'    => $_SERVER['REQUEST_URI'] ?? '',
        ]);
        return false;
    }

    if (!hash_equals($stored, $submitted)) {
        log_security('csrf_violation', [
            'reason' => 'token_mismatch',
            'url'    => $_SERVER['REQUEST_URI'] ?? '',
        ]);
        return false;
    }

    return true;
}

/**
 * Regenerasi CSRF token setelah POST berhasil diproses.
 * Mencegah token reuse.
 */
function csrf_regenerate(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
