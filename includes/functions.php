<?php
// PENTING: gunakan dirname(__DIR__) agar aman di semua OS/struktur
require_once dirname(__DIR__) . '/database/config.php';
require_once dirname(__DIR__) . '/database/db.php';

// Helper minimal supaya tidak undefined saat dipanggil
function getBaseUrl(): string { return BASE_URL; }

function is_logged_in(): bool { return !empty($_SESSION['user']); }
function is_admin(): bool { return !empty($_SESSION['user']) && (($_SESSION['user']['role'] ?? '') === 'admin'); }
function require_admin(): void { if (!is_admin()) { header('Location: ' . BASE_URL . 'admin/login.php'); exit; } }
function require_login(): void { if (!is_logged_in()) { header('Location: ' . BASE_URL . 'admin/login.php?next=' . urlencode($_SERVER['REQUEST_URI'])); exit; } }

function format_date_id(string $ymd): string {
    if (!$ymd) return '';
    $bulan = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    [$y,$m,$d] = explode('-', $ymd);
    return ltrim($d,'0') . ' ' . $bulan[(int)$m-1] . ' ' . $y;
}

// Data access yang sebelumnya sudah Anda punya
function get_department_by_slug(string $slug) {
    $stmt = db()->prepare("SELECT * FROM departments WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}
function get_department_leaders(int $department_id) {
    $stmt = db()->prepare("SELECT id, name, role, photo_url, linkedin_url, email FROM leaders WHERE department_id = ? ORDER BY sort_order, id");
    $stmt->execute([$department_id]);
    return $stmt->fetchAll();
}
function get_divisions(int $department_id) {
    $stmt = db()->prepare("SELECT id, name, slug, description, member_count FROM divisions WHERE department_id = ? ORDER BY sort_order, id");
    $stmt->execute([$department_id]);
    return $stmt->fetchAll();
}
function get_division_members(int $division_id) {
    $stmt = db()->prepare("SELECT id, name, role, photo_url FROM division_members WHERE division_id = ? ORDER BY sort_order, id");
    $stmt->execute([$division_id]);
    return $stmt->fetchAll();
}
function get_programs(int $department_id) {
    $stmt = db()->prepare("SELECT id, name, description, frequency FROM programs WHERE department_id = ? ORDER BY sort_order, id");
    $stmt->execute([$department_id]);
    return $stmt->fetchAll();
}

// Tambahan untuk homepage
function get_upcoming_events(int $limit = 9): array {
    $sql = "SELECT id, title, description, start_date, end_date, is_all_day, type, image_url
            FROM events
            WHERE start_date >= CURDATE()
            ORDER BY start_date ASC
            LIMIT ?";
    $stmt = db()->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}