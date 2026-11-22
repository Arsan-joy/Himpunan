<?php
if (!function_exists('get_organization_leaders')) {
    /**
     * Ambil semua pengurus himpunan, bisa difilter per role.
     * @param array|null $roles contoh: ['Kepala Himpunan','Sekretaris Umum 1']
     * @return array
     */
    function get_organization_leaders(?array $roles = null, ?int $kabinet_id = null): array {
        if ($roles && count($roles)) {
            $in  = implode(',', array_fill(0, count($roles), '?'));
            $params = $roles;
            $sql = "SELECT * FROM organization_leaders WHERE role IN ($in)";
            if ($kabinet_id !== null) {
                $sql .= " AND (kabinet_id = ? OR kabinet_id IS NULL)";
                $params[] = $kabinet_id;
            }
            $sql .= " ORDER BY COALESCE(sort_order,id)";
            $stmt = db()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll() ?: [];
        }
        if ($kabinet_id !== null) {
            $stmt = db()->prepare("SELECT * FROM organization_leaders WHERE kabinet_id = ? OR kabinet_id IS NULL ORDER BY COALESCE(sort_order,id)");
            $stmt->execute([$kabinet_id]);
        } else {
            $stmt = db()->query("SELECT * FROM organization_leaders ORDER BY COALESCE(sort_order,id)");
        }
        return $stmt->fetchAll() ?: [];
    }
}