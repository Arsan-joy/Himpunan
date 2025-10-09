<?php
require_once __DIR__ . '/../config/config.php';

// Jika Anda punya class Database/HMTAFunctions, sertakan di sini.
// Untuk fokus masalah header, cukup sediakan helper URL.

function getBaseUrl() {
    return BASE_URL;
}

function asset_url($path) {
    // Jika user sudah memberi path absolut (http...), langsung pakai.
    if (preg_match('~^https?://~i', $path)) return $path;

    // Jika path sudah dimulai dengan 'assets/', gabungkan dengan BASE_URL
    if (strpos($path, 'assets/') === 0) return BASE_URL . $path;

    // Jika file CSS
    if (preg_match('~\.css$~i', $path)) return CSS_URL . ltrim($path, '/');

    // Jika file JS
    if (preg_match('~\.js$~i', $path)) return JS_URL . ltrim($path, '/');

    // Jika file gambar
    if (preg_match('~\.(png|jpe?g|gif|svg|webp)$~i', $path)) return IMG_URL . ltrim($path, '/');

    // Default
    return BASE_URL . ltrim($path, '/');
}