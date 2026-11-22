<?php
// Pastikan file ini dipanggil setelah includes/functions.php (agar db() tersedia).

// Ambil satu departemen berdasarkan slug (case-insensitive)
function get_department_by_slug(string $slug): ?array {
    $stmt = db()->prepare("
        SELECT id, name, slug, description, logo_url
        FROM departments
        WHERE LOWER(slug) = LOWER(?)
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

// Ambil daftar divisi pada departemen
function get_divisions(int $department_id, ?int $kabinet_id = null): array {
    $params = [$department_id];
    $cabinetClause = '';
    if ($kabinet_id !== null) {
        $cabinetClause = ' AND (kabinet_id = ? OR kabinet_id IS NULL)';
        $params[] = $kabinet_id;
    }
    try {
        $stmt = db()->prepare("
            SELECT id, name, slug, description, member_count
            FROM divisions
            WHERE department_id = ?" . $cabinetClause . "
            ORDER BY COALESCE(sort_order, 9999), id
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        // Fallback jika kolom sort_order/member_count belum ada
        $stmt = db()->prepare("
            SELECT id, name, slug, description
            FROM divisions
            WHERE department_id = ?" . $cabinetClause . "
            ORDER BY id
        ");
        $stmt->execute($params);
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

// Ambil anggota semua divisi dalam satu query.
function get_division_members_batch(array $division_ids): array {
    $division_ids = array_values(array_unique(array_filter(array_map('intval', $division_ids))));
    if (!$division_ids) return [];

    $placeholders = implode(',', array_fill(0, count($division_ids), '?'));
    try {
        $stmt = db()->prepare("
            SELECT id, division_id, name, role, photo_url
            FROM members
            WHERE division_id IN ($placeholders) AND (active = 1 OR active IS NULL)
            ORDER BY id
        ");
        $stmt->execute($division_ids);
        $rows = $stmt->fetchAll();
    } catch (Throwable $e) {
        try {
            $stmt = db()->prepare("
                SELECT id, division_id, name, role, photo_url
                FROM division_members
                WHERE division_id IN ($placeholders)
                ORDER BY id
            ");
            $stmt->execute($division_ids);
            $rows = $stmt->fetchAll();
        } catch (Throwable $e2) {
            return [];
        }
    }

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[(int)$row['division_id']][] = $row;
    }
    foreach ($grouped as &$members) {
        usort($members, function (array $a, array $b): int {
            $aHead = strtolower(trim((string)($a['role'] ?? ''))) === 'kepala divisi';
            $bHead = strtolower(trim((string)($b['role'] ?? ''))) === 'kepala divisi';
            return $aHead === $bHead ? ((int)$a['id'] <=> (int)$b['id']) : ($aHead ? -1 : 1);
        });
    }
    unset($members);
    return $grouped;
}

// (Opsional) Pimpinan/leaders departemen jika tabelnya tersedia
function get_department_leaders(int $department_id, ?int $kabinet_id = null): array {
    $params = [$department_id];
    $cabinetClause = '';
    if ($kabinet_id !== null) {
        $cabinetClause = ' AND (kabinet_id = ? OR kabinet_id IS NULL)';
        $params[] = $kabinet_id;
    }
    try {
        $stmt = db()->prepare("
            SELECT id, name, role, photo_url, linkedin_url, email
            FROM leaders
            WHERE department_id = ?" . $cabinetClause . "
            ORDER BY COALESCE(sort_order, id), id
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

// (Opsional) Program kerja departemen jika tabelnya tersedia
function get_programs(int $department_id, ?int $kabinet_id = null): array {
    $params = [$department_id];
    $cabinetClause = '';
    if ($kabinet_id !== null) {
        $cabinetClause = ' AND (kabinet_id = ? OR kabinet_id IS NULL)';
        $params[] = $kabinet_id;
    }
    try {
        $stmt = db()->prepare("
            SELECT id, name, description, frequency
            FROM programs
            WHERE department_id = ?" . $cabinetClause . "
            ORDER BY COALESCE(sort_order, id), id
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}