<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/logger.php';

// Pastikan session aktif hanya jika belum
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Izinkan admin & super_admin
require_admin();

$current = get_maintenance_status();
$message = '';

// Daftar halaman umum (opsional, sesuaikan)
$availablePages = [
    '/pages/profile.php'             => 'Profile',
    '/pages/struktur.php'            => 'Struktur',
    '/pages/upcoming.php'            => 'Upcoming',
    '/pages/past.php'                => 'Past',
    '/pages/annual.php'              => 'Annual (daftar)',
    '/pages/annual/minerfesto.php'   => 'Annual: Minerfesto',
    '/pages/samagri.php'             => 'Kabinet',
    '/pages/gallery.php'             => 'Galeri',
    '/pages/materi.php'              => 'Materi',
    '/pages/calendar.php'            => 'Kalender',
    '/pages/department.php'          => 'Department (template)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifikasi CSRF token
    if (!csrf_verify()) {
        http_response_code(403);
        log_security('csrf_violation', ['page' => 'maintenance']);
        die('Permintaan tidak valid (403).');
    }
    csrf_regenerate();

    $enableGlobal = isset($_POST['enabled']) && $_POST['enabled'] === '1';
    $pages = array_map('sanitize_page_path', (array)($_POST['pages'] ?? []));
    $custom = trim((string)($_POST['custom_page'] ?? ''));
    if ($custom !== '') $pages[] = sanitize_page_path($custom);

    $updatedBy = $_SESSION['user']['username'] ?? 'admin';
    if (set_maintenance_status($enableGlobal, $pages, $updatedBy)) {
        $current = get_maintenance_status();
        $message = 'Status maintenance berhasil diperbarui.';
    } else {
        $message = 'Gagal memperbarui status maintenance.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maintenance Mode</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>Resource/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .card { max-width: 860px; margin: 24px auto; background: #fff; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.08); padding: 1.5rem; }
    .row { display:flex; gap:1rem; align-items:center; flex-wrap:wrap; }
    .switch { position:relative; display:inline-block; width:64px; height:32px; }
    .switch input { display:none; }
    .slider { position:absolute; inset:0; background:#ccc; border-radius:999px; transition:.3s; cursor:pointer; }
    .slider:before { content:""; position:absolute; width:26px; height:26px; left:3px; top:3px; background:#fff; border-radius:50%; transition:.3s; box-shadow:0 2px 8px rgba(0,0,0,0.2); }
    input:checked + .slider { background:#3498db; }
    input:checked + .slider:before { transform: translateX(32px); }
    .grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px; margin-top:.75rem; }
    .check { display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; border:1px solid #e5e7eb; border-radius:8px; }
    .hint { font-size:.9rem; color:#64748b; }
    input[type="text"] { padding:.5rem .75rem; border:1px solid #dfe6e9; border-radius:8px; width:100%; }
    .msg { margin-top:.75rem; color:#27ae60; }
    .status { margin:.5rem 0 1rem; padding:.75rem 1rem; border-radius:8px; background:#ecf9ff; color:#0c5460; border:1px solid #bee5eb; }
  </style>
</head>
<body>
  <div class="card">
    <h1 style="margin:0 0 .75rem"><i class="fa-solid fa-screwdriver-wrench"></i> Maintenance Mode</h1>
    <?php if (!empty($message)): ?><div class="msg"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <div class="status">
      Status saat ini:
      <strong>
        <?php
          $count = count($current['pages'] ?? []);
          echo $current['enabled'] ? 'GLOBAL AKTIF' : ($count > 0 ? "PARSIAL ($count halaman)" : 'NONAKTIF');
        ?>
      </strong><br>
      Diperbarui: <?= htmlspecialchars($current['updated_at'] ?: '-') ?>
      <?= !empty($current['updated_by']) ? ' oleh '.htmlspecialchars($current['updated_by']) : '' ?>
    </div>

    <form method="post">
      <?= csrf_field() ?>
      <div class="row">
        <span>Aktifkan Maintenance Global</span>
        <label class="switch">
          <input type="checkbox" name="enabled" value="1" <?= $current['enabled'] ? 'checked' : '' ?>>
          <span class="slider"></span>
        </label>
      </div>

      <div style="margin-top:1rem">
        <div style="font-weight:600">Pilih Halaman untuk Maintenance Parsial</div>
        <div class="hint">Contoh: hanya Annual (minerfesto) atau direktori Annual: <code>/pages/annual/*</code>.</div>
        <div class="grid">
          <?php foreach ($availablePages as $path => $label): ?>
            <label class="check">
              <input type="checkbox" name="pages[]" value="<?= htmlspecialchars($path) ?>"
                <?= in_array($path, $current['pages'] ?? [], true) ? 'checked' : '' ?>>
              <span><?= htmlspecialchars($label) ?> <code style="opacity:.7"><?= htmlspecialchars($path) ?></code></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div style="margin-top:1rem">
        <div style="font-weight:600">Halaman/Wildcard Kustom</div>
        <div class="hint">Tambahkan path khusus, mis. <code>/pages/annual/*.php</code> atau <code>/pages/annual/minerfesto.php</code>.</div>
        <input type="text" name="custom_page" placeholder="/pages/annual/*.php">
      </div>

      <div class="row" style="margin-top:1rem">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
        <a class="btn btn-secondary" href="<?= BASE_URL ?>admin/"><i class="fa-solid fa-house"></i> Kembali</a>
      </div>
    </form>
  </div>
</body>
</html>