<?php
/**
 * Security Headers
 * Mengirimkan HTTP security headers untuk melindungi dari serangan umum.
 */

/**
 * Kirim security headers ke browser.
 *
 * @param bool $isAdmin  Jika true, tambahkan header no-store untuk halaman admin
 */
function send_security_headers(bool $isAdmin = false): void {
    // Jangan kirim header jika sudah dikirim
    if (headers_sent()) return;

    header_remove('X-Powered-By');

    // Cegah clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Cegah MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

    // XSS Protection (untuk browser lama)
    header('X-XSS-Protection: 1; mode=block');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Content Security Policy
    // Izinkan: self, cdnjs (Font Awesome), data: untuk gambar inline
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline'",   // unsafe-inline diperlukan untuk inline JS sementara
        "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
        "font-src 'self' https://cdnjs.cloudflare.com",
        "img-src 'self' data: blob: https:",
        "connect-src 'self'",
        "frame-ancestors 'self'",
        "base-uri 'self'",
        "form-action 'self'",
    ]);
    header('Content-Security-Policy: ' . $csp);

    // Cache control untuk halaman admin
    if ($isAdmin) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }
}
