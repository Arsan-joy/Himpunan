<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$counts = [
  'Departemen' => (int)db()->query("SELECT COUNT(*) c FROM departments")->fetch()['c'],
  'Divisi'     => (int)db()->query("SELECT COUNT(*) c FROM divisions")->fetch()['c'],
  'Materi'     => (int)db()->query("SELECT COUNT(*) c FROM materials")->fetch()['c'],
  'Foto'       => (int)db()->query("SELECT COUNT(*) c FROM photos")->fetch()['c'],
  'Kegiatan'   => (int)db()->query("SELECT COUNT(*) c FROM events")->fetch()['c'],
  'Anggota'    => count_members(true),
  'Pengguna'   => (int)db()->query("SELECT COUNT(*) c FROM users")->fetch()['c'],
];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>Resource/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-wrap">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-logo"><i class="fa-solid fa-check"></i></div>
      <div><h1>HMTA Admin</h1><div style="opacity:.8;font-size:12px">Control Panel</div></div>
    </div>
    <nav class="nav">
      <a class="active" href="<?= BASE_URL ?>admin/"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a href="manage.php?m=departemen"><i class="fa-solid fa-building"></i> Kelola Departemen</a>
      <a href="manage.php?m=divisi"><i class="fa-solid fa-people-group"></i> Kelola Divisi</a>
      <a href="manage.php?m=kabinet"><i class="fa-solid fa-layer-group"></i> Kelola Kabinet</a>
      <a href="manage.php?m=anggota"><i class="fa-solid fa-user-friends"></i> Kelola Anggota</a>
      <a href="manage.php?m=foto"><i class="fa-solid fa-image"></i> Kelola Foto</a>
      <a href="manage.php?m=materi"><i class="fa-solid fa-book"></i> Kelola Materi</a>
      <a href="manage.php?m=kegiatan"><i class="fa-solid fa-calendar-days"></i> Kelola Kegiatan</a>
      <?php if (is_super_admin()): ?>
        <a href="manage.php?m=users"><i class="fa-solid fa-user-shield"></i> Kelola Pengguna</a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="title"><h2><i class="fa-solid fa-chart-line"></i> Dashboard</h2></div>
      <div class="pill"><div class="avatar"><i class="fa-solid fa-user"></i></div><div><div style="font-weight:800"><?= htmlspecialchars($_SESSION['user']['username']) ?></div><div style="font-size:12px;opacity:.8"><?= is_super_admin()?'Super Admin':'Admin' ?></div></div></div>
    </div>

    <div class="grid">
      <div class="card metric"><div class="ico ico-blue"><i class="fa-solid fa-building"></i></div><div><div class="num"><?= $counts['Departemen'] ?></div><div class="label">Departemen</div></div></div>
      <div class="card metric"><div class="ico ico-violet"><i class="fa-solid fa-people-group"></i></div><div><div class="num"><?= $counts['Divisi'] ?></div><div class="label">Divisi</div></div></div>
      <div class="card metric"><div class="ico ico-green"><i class="fa-solid fa-user-friends"></i></div><div><div class="num"><?= $counts['Anggota'] ?></div><div class="label">Anggota</div></div></div>
      <div class="card metric"><div class="ico ico-amber"><i class="fa-solid fa-book"></i></div><div><div class="num"><?= $counts['Materi'] ?></div><div class="label">Materi</div></div></div>
      <div class="card metric"><div class="ico ico-amber"><i class="fa-solid fa-image"></i></div><div><div class="num"><?= $counts['Foto'] ?></div><div class="label">Foto</div></div></div>
      <div class="card metric"><div class="ico ico-blue"><i class="fa-solid fa-calendar-days"></i></div><div><div class="num"><?= $counts['Kegiatan'] ?></div><div class="label">Kegiatan</div></div></div>
      <?php if (is_super_admin()): ?>
      <div class="card metric"><div class="ico ico-violet"><i class="fa-solid fa-user-shield"></i></div><div><div class="num"><?= $counts['Pengguna'] ?></div><div class="label">Pengguna</div></div></div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>