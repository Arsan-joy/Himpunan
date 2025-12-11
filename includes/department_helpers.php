<?php
// Pastikan file ini dipanggil setelah includes/functions.php (agar db() tersedia).

// Ambil satu departemen berdasarkan slug (case-insensitive)
function get_department_by_slug(string $slug): ?array {
    $stmt = db()->prepare("
        SELECT id, name, slug, description
        FROM departments
        WHERE LOWER(slug) = LOWER(?)
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

// Ambil daftar divisi pada departemen
function get_divisions(int $department_id): array {
    try {
        $stmt = db()->prepare("
            SELECT id, name, slug, description, member_count
            FROM divisions
            WHERE department_id = ?
            ORDER BY COALESCE(sort_order, 9999), id
        ");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        // Fallback jika kolom sort_order/member_count belum ada
        $stmt = db()->prepare("
            SELECT id, name, slug, description
            FROM divisions
            WHERE department_id = ?
            ORDER BY id
        ");
        $stmt->execute([$department_id]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            if (!isset($r['member_count'])) $r['member_count'] = null;
        }
        return $rows;
    }
}

// Ambil anggota untuk sebuah divisi
// Default memakai tabel "members" (sesuai modul Anggota). Fallback ke "division_members" bila ada.
function get_division_members(int $division_id): array {
    try {
        $stmt = db()->prepare("
            SELECT id, name, role, photo_url
            FROM members
            WHERE division_id = ? AND (active = 1 OR active IS NULL)
            ORDER BY id
        ");
        $stmt->execute([$division_id]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        try {
            $stmt = db()->prepare("
                SELECT id, name, role, photo_url
                FROM division_members
                WHERE division_id = ?
                ORDER BY id
            ");
            $stmt->execute([$division_id]);
            return $stmt->fetchAll();
        } catch (Throwable $e2) {
            return [];
        }
    }
}

// (Opsional) Pimpinan/leaders departemen jika tabelnya tersedia
function get_department_leaders(int $department_id): array {
    try {
        $stmt = db()->prepare("
            SELECT id, name, role, photo_url, linkedin_url, email
            FROM leaders
            WHERE department_id = ?
            ORDER BY COALESCE(sort_order, id), id
        ");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

// (Opsional) Program kerja departemen jika tabelnya tersedia
function get_programs(int $department_id): array {
    try {
        $stmt = db()->prepare("
            SELECT id, name, description, frequency
            FROM programs
            WHERE department_id = ?
            ORDER BY COALESCE(sort_order, id), id
        ");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}