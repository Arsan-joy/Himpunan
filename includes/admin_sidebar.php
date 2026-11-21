<?php
$activeModule = $activeModule ?? '';
$activeSidebar = $activeSidebar ?? '';
?>
<aside class="sidebar">
  <div class="brand">
    <div class="brand-logo"><i class="fa-solid fa-check"></i></div>
    <div><h1>HMTA Admin</h1><div style="opacity:.8;font-size:12px">Control Panel</div></div>
  </div>
  <nav class="nav">
    <a class="<?= $activeSidebar === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a class="<?= $activeModule === 'pengurus' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=pengurus"><i class="fa-solid fa-user-tie"></i> Kelola Pengurus</a>
    <a class="<?= $activeModule === 'leaders' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=leaders"><i class="fa-solid fa-users-gear"></i> Kelola Leader Departemen</a>
    <a class="<?= $activeModule === 'programs' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=programs"><i class="fa-solid fa-rocket"></i> Kelola Program</a>
    <a class="<?= $activeModule === 'departemen' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=departemen"><i class="fa-solid fa-building"></i> Kelola Departemen</a>
    <a class="<?= $activeModule === 'divisi' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=divisi"><i class="fa-solid fa-people-group"></i> Kelola Divisi</a>
    <a class="<?= $activeModule === 'kabinet' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=kabinet"><i class="fa-solid fa-layer-group"></i> Kelola Kabinet</a>
    <a class="<?= $activeModule === 'anggota' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=anggota"><i class="fa-solid fa-user-friends"></i> Kelola Anggota</a>
    <a class="<?= $activeModule === 'foto' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=foto"><i class="fa-solid fa-image"></i> Kelola Foto</a>
    <a class="<?= $activeModule === 'materi' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=materi"><i class="fa-solid fa-book"></i> Kelola Materi</a>
    <a class="<?= $activeModule === 'kegiatan' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=kegiatan"><i class="fa-solid fa-calendar-days"></i> Kelola Kegiatan</a>
    <a class="<?= $activeModule === 'pengaturan' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=pengaturan"><i class="fa-solid fa-sliders"></i> Kelola Konten</a>
    <?php if (is_super_admin()): ?>
    <a class="<?= $activeModule === 'users' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/manage.php?m=users"><i class="fa-solid fa-user-shield"></i> Kelola Pengguna</a>
    <a class="<?= $activeSidebar === 'import' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/tools/import_members.php"><i class="fa-solid fa-file-import"></i> Impor Anggota</a>
    <a href="<?= BASE_URL ?>admin/maintenance.php"><i class="fa-solid fa-screwdriver-wrench"></i> Maintenance Mode</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website</a>
    <a href="<?= BASE_URL ?>admin/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </nav>
</aside>
