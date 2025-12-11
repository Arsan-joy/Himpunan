<?php
if (!function_exists('get_organization_leaders')) {
    /**
     * Ambil semua pengurus himpunan, bisa difilter per role.
     * @param array|null $roles contoh: ['Kepala Himpunan','Sekretaris Umum 1']
     * @return array
     */
    function get_organization_leaders(?array $roles = null): array {
        if ($roles && count($roles)) {
            $in  = implode(',', array_fill(0, count($roles), '?'));
            $sql = "SELECT * FROM organization_leaders WHERE role IN ($in) ORDER BY COALESCE(sort_order,id)";
            $stmt = db()->prepare($sql);
            $stmt->execute($roles);
            return $stmt->fetchAll() ?: [];
        }
        $stmt = db()->query("SELECT * FROM organization_leaders ORDER BY COALESCE(sort_order,id)");
        return $stmt->fetchAll() ?: [];
    }
}