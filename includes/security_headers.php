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

    // Cegah clickjacking
    header('X-Frame-Options: DENY');

    // Cegah MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // XSS Protection (untuk browser lama)
    header('X-XSS-Protection: 1; mode=block');

    // Content Security Policy
    // Izinkan: self, cdnjs (Font Awesome), data: untuk gambar inline
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline'",   // unsafe-inline diperlukan untuk inline JS sementara
        "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
        "font-src 'self' https://cdnjs.cloudflare.com",
        "img-src 'self' data: blob: https:",
        "connect-src 'self'",
        "frame-ancestors 'none'",
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
