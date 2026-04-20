<?php
/**
 * File-Based Cache
 * Cache sederhana berbasis file JSON untuk menyimpan hasil query database.
 */

if (!defined('CACHE_DIR')) {
    define('CACHE_DIR', dirname(__DIR__) . '/cache');
}

/**
 * Sanitasi cache key menjadi nama file yang aman.
 */
function _cache_key_to_file(string $key): string {
    $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
    return rtrim(CACHE_DIR, '/\\') . '/' . $safe . '.json';
}

/**
 * Ambil data dari cache. Return null jika miss, expired, atau corrupt.
 *
 * @param string $key Cache key
 * @return mixed Data yang di-cache, atau null jika tidak ada
 */
function cache_get(string $key): mixed {
    $file = _cache_key_to_file($key);

    if (!file_exists($file)) return null;

    $raw = @file_get_contents($file);
    if ($raw === false) return null;

    $payload = json_decode($raw, true);
    if (!is_array($payload) || !isset($payload['expires_at'], $payload['data'])) return null;

    // Cek apakah sudah expired
    if (time() > $payload['expires_at']) {
        @unlink($file);
        return null;
    }

    return $payload['data'];
}

/**
 * Simpan data ke cache.
 *
 * @param string $key  Cache key
 * @param mixed  $data Data yang akan di-cache
 * @param int    $ttl  Time-to-live dalam detik (default 1 jam)
 * @return bool True jika berhasil
 */
function cache_set(string $key, mixed $data, int $ttl = 3600): bool {
    // Buat folder cache jika belum ada
    if (!is_dir(CACHE_DIR)) {
        @mkdir(CACHE_DIR, 0775, true);
    }

    $file    = _cache_key_to_file($key);
    $payload = json_encode([
        'expires_at' => time() + $ttl,
        'created_at' => time(),
        'key'        => $key,
        'data'       => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($payload === false) {
        error_log('[cache] Gagal encode JSON untuk key: ' . $key);
        return false;
    }

    $result = @file_put_contents($file, $payload, LOCK_EX);
    if ($result === false) {
        error_log('[cache] Gagal menulis cache untuk key: ' . $key);
        return false;
    }

    return true;
}

/**
 * Hapus satu entri cache.
 *
 * @param string $key Cache key
 * @return bool True jika berhasil dihapus
 */
function cache_delete(string $key): bool {
    $file = _cache_key_to_file($key);
    if (file_exists($file)) {
        return (bool)@unlink($file);
    }
    return true;
}

/**
 * Hapus semua cache yang key-nya cocok dengan prefix/pattern.
 *
 * @param string $pattern Prefix atau pattern (mis. 'departments_', 'members_')
 * @return int Jumlah file yang dihapus
 */
function cache_delete_pattern(string $pattern): int {
    if (!is_dir(CACHE_DIR)) return 0;

    $safePattern = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $pattern);
    $files       = glob(rtrim(CACHE_DIR, '/\\') . '/' . $safePattern . '*.json') ?: [];
    $count       = 0;

    foreach ($files as $file) {
        if (@unlink($file)) $count++;
    }

    return $count;
}

/**
 * Hapus semua file cache.
 */
function cache_flush(): void {
    if (!is_dir(CACHE_DIR)) return;

    $files = glob(rtrim(CACHE_DIR, '/\\') . '/*.json') ?: [];
    foreach ($files as $file) {
        @unlink($file);
    }
}
