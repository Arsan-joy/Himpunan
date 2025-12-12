<?php
require_once dirname(__DIR__) . '/database/config.php';
require_once dirname(__DIR__) . '/database/db.php';

if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', BASE_URL . 'uploads/');
if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0775, true);

function getBaseUrl(): string { return BASE_URL; }

function is_logged_in(): bool { return !empty($_SESSION['user']); }
function is_admin(): bool {
    return !empty($_SESSION['user']) && in_array(($_SESSION['user']['role'] ?? ''), ['admin','super_admin'], true);
}
function is_super_admin(): bool {
    return !empty($_SESSION['user']) && (($_SESSION['user']['role'] ?? '') === 'super_admin');
}
function require_admin(): void {
    if (!is_admin()) { header('Location: ' . BASE_URL . 'admin/login.php'); exit; }
}
function require_super_admin(): void {
    if (!is_super_admin()) { http_response_code(403); echo 'Forbidden'; exit; }
}
function require_login(): void {
    if (!is_logged_in()) { header('Location: ' . BASE_URL . 'pages/login.php?next=' . urlencode($_SERVER['REQUEST_URI'])); exit; }
}

function format_date_id(string $ymd): string {
    if(!$ymd) return '';
    $b=["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    [$y,$m,$d]=explode('-',$ymd);
    return ltrim($d,'0').' '.$b[(int)$m-1].' '.$y;
}

// Upload helper
function save_uploaded_file(string $field, string $subdir, array $allowedExt, int $maxMB = 50): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload gagal (kode '.$f['error'].')');
    $size = (int)$f['size'];
    if ($size <= 0) throw new RuntimeException('File kosong');
    if ($size > ($maxMB * 1024 * 1024)) throw new RuntimeException('Ukuran maksimal '.$maxMB.'MB');
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) throw new RuntimeException('Ekstensi tidak diizinkan: '.$ext);

    $targetDir = rtrim(UPLOAD_DIR,'/\\').'/'.trim($subdir,'/\\').'/';
    if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);

    $basename = preg_replace('~[^a-zA-Z0-9._-]+~','-', pathinfo($f['name'], PATHINFO_FILENAME));
    $fname = date('Ymd_His').'-'.bin2hex(random_bytes(3)).'-'.$basename.'.'.$ext;
    $dest = $targetDir.$fname;

    if (!move_uploaded_file($f['tmp_name'], $dest)) throw new RuntimeException('Gagal memindahkan file');

    return rtrim(UPLOAD_URL,'/').'/'.trim($subdir,'/').'/'.$fname;
}

// Data access ringkas lain tetap ...nya bisa ditambahkan di sini
function get_upcoming_events(int $limit = 6): array {
    $sql = "SELECT id, title, description, start_date, end_date, is_all_day, type, image_url
            FROM events
            WHERE start_date >= CURDATE()
            ORDER BY start_date ASC
            LIMIT :lim";
    $stmt = db()->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_members(bool $onlyActive = true): int {
    $sql = "SELECT COUNT(*) c FROM members" . ($onlyActive ? " WHERE active=1" : "");
    return (int) db()->query($sql)->fetch()['c'];
}

// Count anggota per kabinet
function count_members_by_kabinet(int $kabinetId, bool $onlyActive = true): int {
    $stmt = db()->prepare("SELECT COUNT(*) c FROM members WHERE kabinet_id = ? " . ($onlyActive ? "AND active=1" : ""));
    $stmt->execute([$kabinetId]);
    return (int) $stmt->fetch()['c'];
}

// Ambil daftar pilihan kabinet untuk select
function get_kabinet_options(): array {
    $rows = db()->query("SELECT id, name, period FROM kabinet ORDER BY id DESC")->fetchAll();
    return array_map(fn($r) => [
        'value' => (string)$r['id'],
        'label' => $r['name'] . ($r['period'] ? " ({$r['period']})" : "")
    ], $rows);
}

/* ============================================================
   MAINTENANCE (GLOBAL & PARSIAL)
   ============================================================ */
if (!defined('MAINTENANCE_CONFIG')) {
    define('MAINTENANCE_CONFIG', dirname(__DIR__) . '/config/maintenance.json');
}

/**
 * Normalisasi path halaman agar seragam (mis. '/pages/annual.php' atau '/pages/annual/minerfesto.php')
 */
function sanitize_page_path(string $path): string {
    $p = parse_url($path, PHP_URL_PATH) ?: $path;
    // Jika BASE_URL di awal, buang
    if (defined('BASE_URL') && BASE_URL !== '/' && str_starts_with($p, BASE_URL)) {
        $p = substr($p, strlen(BASE_URL));
    }
    // Paksa diawali slash
    if ($p === '' || $p[0] !== '/') $p = '/' . ltrim($p, '/');
    // Buang trailing slash (kecuali root)
    if (strlen($p) > 1) $p = rtrim($p, '/');
    return $p;
}

/**
 * Ambil status maintenance dari file JSON.
 * Schema:
 * {
 *   "enabled": false,                // maintenance global
 *   "pages": ["/pages/annual.php"],  // daftar halaman/wildcard untuk maintenance parsial
 *   "updated_by": "",
 *   "updated_at": ""
 * }
 */
function get_maintenance_status(): array {
    $default = ['enabled' => false, 'pages' => [], 'updated_by' => '', 'updated_at' => ''];

    if (!file_exists(MAINTENANCE_CONFIG)) {
        @mkdir(dirname(MAINTENANCE_CONFIG), 0775, true);
        file_put_contents(MAINTENANCE_CONFIG, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $default;
    }

    $raw = @file_get_contents(MAINTENANCE_CONFIG);
    $data = json_decode((string)$raw, true);
    if (!is_array($data)) $data = [];

    $data = array_merge($default, $data);
    $data['enabled'] = (bool)($data['enabled'] ?? false);

    // Pastikan pages berupa array string yang tersanitasi unik
    $pages = is_array($data['pages'] ?? null) ? $data['pages'] : [];
    $pages = array_values(array_unique(array_map(
        fn($p) => sanitize_page_path((string)$p),
        array_filter($pages, fn($p) => is_string($p) && $p !== '')
    )));
    $data['pages'] = $pages;

    return $data;
}

/**
 * Simpan status maintenance (global + parsial).
 *
 * @param bool   $enabledGlobal  Aktifkan maintenance global?
 * @param array  $pages          Daftar path halaman/wildcard untuk maintenance parsial
 * @param string $updated_by     Nama pengubah
 * @return bool
 */
function set_maintenance_status(bool $enabledGlobal, array $pages = [], string $updated_by = ''): bool {
    $pages = array_values(array_unique(array_map('sanitize_page_path', $pages)));
    $payload = [
        'enabled'    => $enabledGlobal,
        'pages'      => $pages,
        'updated_by' => $updated_by,
        'updated_at' => date('c'),
    ];
    @mkdir(dirname(MAINTENANCE_CONFIG), 0775, true);
    return (bool)file_put_contents(MAINTENANCE_CONFIG, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

/**
 * Cek apakah maintenance aktif untuk path tertentu (atau current request).
 * - True jika global maintenance aktif
 * - True jika path cocok salah satu 'pages' (mendukung wildcard '*', mis. '/pages/annual/*')
 */
function is_maintenance_active(?string $requestPath = null): bool {
    $status = get_maintenance_status();
    if (!empty($status['enabled'])) return true;

    $pages = $status['pages'] ?? [];
    if (!$pages) return false;

    $path = sanitize_page_path($requestPath ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));

    foreach ($pages as $pattern) {
        if (str_contains($pattern, '*')) {
            // Ubah wildcard '*' menjadi regex
            $quoted = preg_quote($pattern, '/');
            $quoted = str_replace('\*', '.*', $quoted);
            $regex  = '/^' . $quoted . '$/i';
            if (preg_match($regex, $path)) return true;
        } else {
            if (sanitize_page_path($pattern) === $path) return true;
        }
    }
    return false;
}