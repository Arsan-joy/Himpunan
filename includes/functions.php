<?php
require_once dirname(__DIR__) . '/database/config.php';
require_once dirname(__DIR__) . '/database/db.php';

function getBaseUrl(): string { return BASE_URL; }
function is_logged_in(): bool { return !empty($_SESSION['user']); }
function is_admin(): bool { return !empty($_SESSION['user']) && (($_SESSION['user']['role'] ?? '') === 'admin'); }
function require_admin(): void { if (!is_admin()) { header('Location: ' . BASE_URL . 'admin/login.php'); exit; } }
function require_login(): void { if (!is_logged_in()) { header('Location: ' . BASE_URL . 'admin/login.php?next=' . urlencode($_SERVER['REQUEST_URI'])); exit; } }
function format_date_id(string $ymd): string { if(!$ymd) return ''; $b=["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"]; [$y,$m,$d]=explode('-',$ymd); return ltrim($d,'0').' '.$b[(int)$m-1].' '.$y; }

// FALLBACK jika konstanta upload belum ada (mencegah fatal error)
if (!defined('UPLOAD_DIR')) { define('UPLOAD_DIR', dirname(__DIR__) . '/uploads'); }
if (!defined('UPLOAD_URL')) { define('UPLOAD_URL', BASE_URL . 'uploads/'); }

// Pastikan folder upload ada
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0775, true);
}

// ... sisanya tetap seperti sebelumnya (helper save_uploaded_file, dll)
// Helper upload umum
function save_uploaded_file(string $field, string $subdir, array $allowedExt, int $maxMB = 50): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload gagal (kode '.$f['error'].')');
    $size = (int)$f['size'];
    if ($size <= 0) throw new RuntimeException('File kosong');
    if ($size > ($maxMB * 1024 * 1024)) throw new RuntimeException('Ukuran maksimal '.$maxMB.'MB');

    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) throw new RuntimeException('Ekstensi tidak diizinkan: '.$ext);

    $targetDir = rtrim(UPLOAD_DIR, '/\\') . '/' . trim($subdir, '/\\') . '/';
    if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);

    $basename = preg_replace('~[^a-zA-Z0-9._-]+~','-', pathinfo($f['name'], PATHINFO_FILENAME));
    $fname = date('Ymd_His') . '-' . bin2hex(random_bytes(3)) . '-' . $basename . '.' . $ext;

    $dest = $targetDir . $fname;
    if (!move_uploaded_file($f['tmp_name'], $dest)) throw new RuntimeException('Gagal memindahkan file');

    $public = rtrim(UPLOAD_URL, '/').'/'.trim($subdir,'/').'/'.$fname;
    return $public;
}

// Data access (tetap)
function get_upcoming_events(int $limit = 9): array {
    $stmt = db()->prepare("SELECT id,title,description,start_date,end_date,is_all_day,type,image_url FROM events WHERE start_date >= CURDATE() ORDER BY start_date ASC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute(); return $stmt->fetchAll();
}
function get_department_by_slug(string $slug) { $s=db()->prepare("SELECT * FROM departments WHERE slug=? LIMIT 1"); $s->execute([$slug]); return $s->fetch(); }
function get_department_leaders(int $department_id) { $s=db()->prepare("SELECT id,name,role,photo_url,linkedin_url,email FROM leaders WHERE department_id=? ORDER BY sort_order,id"); $s->execute([$department_id]); return $s->fetchAll(); }
function get_divisions(int $department_id) { $s=db()->prepare("SELECT id,name,slug,description,member_count FROM divisions WHERE department_id=? ORDER BY sort_order,id"); $s->execute([$department_id]); return $s->fetchAll(); }
function get_division_members(int $division_id) { $s=db()->prepare("SELECT id,name,role,photo_url FROM division_members WHERE division_id=? ORDER BY sort_order,id"); $s->execute([$division_id]); return $s->fetchAll(); }
function get_programs(int $department_id) { $s=db()->prepare("SELECT id,name,description,frequency FROM programs WHERE department_id=? ORDER BY sort_order,id"); $s->execute([$department_id]); return $s->fetchAll(); }