<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);

echo "OK 0: awal<br>";
require_once __DIR__ . '/includes/functions.php';
echo "OK 1: functions loaded<br>";

echo "Cek file config: ";
echo file_exists(__DIR__ . '/database/config.php') ? "ADA<br>" : "TIDAK ADA<br>";

echo "Tes koneksi DB: ";
$pdo = null;
try {
    $pdo = db();
    $pdo->query("SELECT 1");
    echo "OK DB<br>";
} catch (Throwable $e) {
    echo "GAGAL DB: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "Tes query tabel users: ";
try {
    if ($pdo) {
        $row = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
        echo $row ? "ADA<br>" : "TIDAK ADA<br>";
    } else {
        echo "SKIP (DB gagal)<br>";
    }
} catch (Throwable $e) {
    echo "ERROR: " . htmlspecialchars($e->getMessage()) . "<br>";
}
echo "Selesai.";