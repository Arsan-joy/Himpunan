<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$counts = [
    'departments' => (int)db()->query("SELECT COUNT(*) c FROM departments")->fetch()['c'],
    'divisions'   => (int)db()->query("SELECT COUNT(*) c FROM divisions")->fetch()['c'],
    'materials'   => (int)db()->query("SELECT COUNT(*) c FROM materials")->fetch()['c'],
    'photos'      => (int)db()->query("SELECT COUNT(*) c FROM photos")->fetch()['c'],
    'events'      => (int)db()->query("SELECT COUNT(*) c FROM events")->fetch()['c'],
    'users'       => (int)db()->query("SELECT COUNT(*) c FROM users")->fetch()['c'],
];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Dashboard Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f5f7fb;margin:0}
header{background:#263b50;color:#fff;padding:14px 18px;display:flex;justify-content:space-between;align-items:center}
header a{color:#fff;text-decoration:none;font-size:14px}
.wrap{max-width:1100px;margin:24px auto;padding:0 16px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
.card{background:#fff;border-radius:14px;box-shadow:0 16px 40px rgba(0,0,0,.06);padding:18px}
.nav{display:flex;gap:10px;flex-wrap:wrap;margin:18px 0}
.btn{display:inline-block;background:#3e6fa0;color:#fff;padding:10px 14px;border-radius:9px;text-decoration:none;font-weight:700}
.small{font-size:12px;color:#cbd5e1}
</style>
</head>
<body>
<header>
  <strong>Dashboard Admin</strong>
  <div>
    <span class="small">Hai, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
    &nbsp;|&nbsp;
    <a href="<?= BASE_URL ?>">Lihat Situs</a>
    &nbsp;|&nbsp;
    <a href="<?= BASE_URL ?>admin/logout.php">Logout</a>
  </div>
</header>
<div class="wrap">
  <div class="nav">
    <a class="btn" href="manage.php?m=departemen">Kelola Departemen</a>
    <a class="btn" href="manage.php?m=divisi">Kelola Divisi</a>
    <a class="btn" href="manage.php?m=kabinet">Kelola Kabinet</a>
    <a class="btn" href="manage.php?m=foto">Kelola Foto</a>
    <a class="btn" href="manage.php?m=materi">Kelola Materi</a>
    <a class="btn" href="manage.php?m=kegiatan">Kelola Kegiatan</a>
    <a class="btn" href="manage.php?m=users">Kelola Pengguna</a>
  </div>

  <div class="grid">
    <?php foreach ($counts as $label => $val): ?>
      <div class="card">
        <h3><?= ucfirst($label) ?></h3>
        <div><strong style="font-size:28px"><?= $val ?></strong></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>