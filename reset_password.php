<?php
// AMANKAN: ganti NEW_PASSWORD sebelum pakai. Hapus file setelah selesai.
require_once __DIR__ . '/../includes/functions.php';
if (php_sapi_name() !== 'cli' && ($_GET['confirm'] ?? '') !== 'YES') {
    http_response_code(403);
    echo "Tambahkan ?confirm=YES pada URL untuk menjalankan.";
    exit;
}
if (!defined('SUPER_ADMIN_USERNAME') || !SUPER_ADMIN_USERNAME) {
    echo "SUPER_ADMIN_USERNAME belum diset di database/config.php\n"; exit(1);
}
$new = 'AdminBaru#2025'; // GANTI password darurat di sini
$stmt = db()->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
$stmt->execute([SUPER_ADMIN_USERNAME]);
$u = $stmt->fetch();
if (!$u) {
    db()->prepare("INSERT INTO users (username,password_hash,role,active) VALUES (?,?, 'super_admin',1)")
      ->execute([SUPER_ADMIN_USERNAME, password_hash($new, PASSWORD_DEFAULT)]);
    echo "Super Admin dibuat: ".SUPER_ADMIN_USERNAME." dengan password baru.\n";
} else {
    db()->prepare("UPDATE users SET password_hash=?, role='super_admin', active=1 WHERE id=?")
      ->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]);
    echo "Password Super Admin direset.\n";
}
echo "Segera hapus file ini (scripts/emergency_reset_superadmin.php) setelah digunakan.\n";