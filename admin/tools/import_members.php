<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Dropdown data
$departments = db()->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll();
$divisions   = db()->query("SELECT id, name FROM divisions ORDER BY name ASC")->fetchAll();
$kabinet     = db()->query("SELECT id, CONCAT(name, ' (', IFNULL(period,''), ')') AS name FROM kabinet ORDER BY id DESC")->fetchAll();

$log = []; $summary = ['users_new'=>0,'users_update'=>0,'members_new'=>0,'members_update'=>0,'errors'=>0];

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (empty($_FILES['csv']) || $_FILES['csv']['error']!==UPLOAD_ERR_OK) {
    $log[] = "Upload CSV gagal.";
  } else {
    $tmp = $_FILES['csv']['tmp_name'];

    $default_kabinet_id    = $_POST['default_kabinet_id'] !== '' ? (int)$_POST['default_kabinet_id'] : null;
    $default_department_id = $_POST['default_department_id'] !== '' ? (int)$_POST['default_department_id'] : null;
    $default_division_id   = $_POST['default_division_id'] !== '' ? (int)$_POST['default_division_id'] : null;

    $create_users   = isset($_POST['create_users']);
    $create_members = isset($_POST['create_members']);
    $update_pass    = isset($_POST['update_password_if_exists']);

    if (!$create_users && !$create_members) {
      $log[] = "Pilih setidaknya salah satu: buat Users atau buat Members.";
    } else {
      try {
        $fh = fopen($tmp, 'r');
        if (!$fh) throw new RuntimeException('Tidak bisa membaca file.');

        $delimiter = ';';

        // Lewati 2 baris awal non-header (sesuai file contoh)
        fgetcsv($fh, 0, $delimiter);
        fgetcsv($fh, 0, $delimiter);

        // Header
        $header = fgetcsv($fh, 0, $delimiter);
        $map = array_map('strtolower', $header ?? []);
        $idxNim  = array_search('nim', $map, true);
        $idxNama = array_search('nama', $map, true);
        $idxPass = array_search('password', $map, true);
        $idxUser = array_search('username', $map, true);

        if ($idxNim===false || $idxNama===false || $idxPass===false || $idxUser===false) {
          throw new RuntimeException('Header CSV tidak sesuai. Harus mengandung kolom: NIM;Nama;Password;Username');
        }

        db()->beginTransaction();

        while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
          if (!$row || count($row) < 5) continue;

          $nim      = trim((string)$row[$idxNim]);
          $nama     = trim((string)$row[$idxNama]);
          $plain    = (string)$row[$idxPass];
          $username = trim((string)$row[$idxUser]);

          if ($nim==='' || $nama==='' || $username==='') {
            $summary['errors']++; $log[]="Baris dilewati (data kosong) NIM=$nim Username=$username"; continue;
          }

          // USERS
          if ($create_users) {
            $u = db()->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $u->execute([$username]);
            $user = $u->fetch();

            if ($user) {
              if ($update_pass) {
                $hash = password_hash($plain, PASSWORD_DEFAULT);
                db()->prepare("UPDATE users SET password_hash = ?, active = 1 WHERE id = ?")->execute([$hash, (int)$user['id']]);
                $summary['users_update']++;
              }
            } else {
              $hash = password_hash($plain, PASSWORD_DEFAULT);
              db()->prepare("INSERT INTO users (username, password_hash, role, active) VALUES (?,?,?,1)")
                ->execute([$username, $hash, 'user']);
              $summary['users_new']++;
            }
          }

          // MEMBERS
          if ($create_members) {
            $m = db()->prepare("SELECT id FROM members WHERE student_id = ? LIMIT 1");
            $m->execute([$nim]);
            $mem = $m->fetch();

            if ($mem) {
              db()->prepare("
                UPDATE members
                   SET name = ?,
                       kabinet_id = COALESCE(?, kabinet_id),
                       department_id = COALESCE(?, department_id),
                       division_id = COALESCE(?, division_id),
                       active = 1
                 WHERE id = ?
              ")->execute([$nama, $default_kabinet_id, $default_department_id, $default_division_id, (int)$mem['id']]);
              $summary['members_update']++;
            } else {
              db()->prepare("
                INSERT INTO members (name, student_id, kabinet_id, department_id, division_id, role, active)
                VALUES (?,?,?,?,?, ?,1)
              ")->execute([$nama, $nim, $default_kabinet_id, $default_department_id, $default_division_id, null]);
              $summary['members_new']++;
            }
          }
        }

        db()->commit();
        fclose($fh);
        $log[] = "Import selesai.";
      } catch (Throwable $e) {
        if (db()->inTransaction()) db()->rollBack();
        $summary['errors']++;
        $log[] = "Error: ".$e->getMessage();
      }
    }
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Impor Anggota</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>Resource/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .content-card{background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,.08);padding:16px;margin-bottom:16px}
    .grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:720px){.grid2{grid-template-columns:1fr}}
    .log{background:#0b1023;color:#d1d5db;border-radius:8px;padding:10px;font-family:ui-monospace, SFMono-Regular, Menlo, monospace;max-height:260px;overflow:auto}
    .hint{color:#6b7280;font-size:.9rem}
  </style>
</head>
<body>
<div class="admin-wrap">
  <aside class="sidebar">
    <div class="brand"><div class="brand-logo"><i class="fa-solid fa-file-import"></i></div><div><h1>HMTA Admin</h1><div style="opacity:.8;font-size:12px">Impor Anggota</div></div></div>
    <nav class="nav">
      <a href="<?= BASE_URL ?>admin/"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a href="<?= BASE_URL ?>admin/manage.php?m=departemen"><i class="fa-solid fa-building"></i> Kelola Departemen</a>
      <a href="<?= BASE_URL ?>admin/manage.php?m=divisi"><i class="fa-solid fa-people-group"></i> Kelola Divisi</a>
      <a href="<?= BASE_URL ?>admin/manage.php?m=leaders"><i class="fa-solid fa-user-tie"></i> Kelola Leader</a>
      <a href="<?= BASE_URL ?>admin/manage.php?m=programs"><i class="fa-solid fa-rocket"></i> Kelola Program</a>
      <a class="active" href="#"><i class="fa-solid fa-file-import"></i> Impor Anggota</a>
      <a href="<?= BASE_URL ?>admin/manage.php?m=anggota"><i class="fa-solid fa-user-friends"></i> Kelola Anggota</a>
      <a href="<?= BASE_URL ?>admin/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="title"><h2><i class="fa-solid fa-file-import"></i> Impor Anggota dari CSV</h2></div>
      <div class="pill"><div class="avatar"><i class="fa-solid fa-user"></i></div><div><div style="font-weight:800"><?= h($_SESSION['user']['username']) ?></div><div style="font-size:12px;opacity:.8"><?= is_super_admin()?'Super Admin':'Admin' ?></div></div></div>
    </div>

    <div class="content-card">
      <form method="post" enctype="multipart/form-data">
        <div class="field">
          <label>File CSV (delimiter ;)</label>
          <input type="file" name="csv" accept=".csv" required>
          <div class="hint">Struktur header: No.;NIM;Nama;Password;Username. Baris pembuka non-header akan dilewati otomatis.</div>
        </div>

        <div class="grid2">
          <div class="field">
            <label>Set default Kabinet (opsional)</label>
            <select name="default_kabinet_id">
              <option value="">— Kosongkan —</option>
              <?php foreach ($kabinet as $k): ?>
                <option value="<?= (int)$k['id'] ?>"><?= h($k['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>Set default Departemen (opsional)</label>
            <select name="default_department_id">
              <option value="">— Kosongkan —</option>
              <?php foreach ($departments as $d): ?>
                <option value="<?= (int)$d['id'] ?>"><?= h($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>Set default Divisi (opsional)</label>
            <select name="default_division_id">
              <option value="">— Kosongkan —</option>
              <?php foreach ($divisions as $d): ?>
                <option value="<?= (int)$d['id'] ?>"><?= h($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="grid2">
          <div class="field">
            <label>Aksi yang dilakukan</label>
            <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="create_users" checked> Buat/Update akun Users</label>
            <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="create_members" checked> Buat/Update data Members</label>
            <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="update_password_if_exists"> Update password jika user sudah ada</label>
          </div>
        </div>

        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-upload"></i> Unggah & Impor</button>
      </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD']==='POST'): ?>
      <div class="content-card">
        <div class="section-title"><i class="fa-solid fa-circle-info"></i> Ringkasan</div>
        <div class="grid2">
          <div>
            <ul>
              <li>User baru: <?= (int)$summary['users_new'] ?></li>
              <li>User diupdate: <?= (int)$summary['users_update'] ?></li>
            </ul>
          </div>
          <div>
            <ul>
              <li>Member baru: <?= (int)$summary['members_new'] ?></li>
              <li>Member diupdate: <?= (int)$summary['members_update'] ?></li>
            </ul>
          </div>
        </div>
        <div class="hint">Errors: <?= (int)$summary['errors'] ?></div>
      </div>

      <div class="content-card">
        <div class="section-title"><i class="fa-solid fa-terminal"></i> Log</div>
        <div class="log">
          <?php foreach ($log as $line): ?>
            <?= h($line) ?><br>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </main>
</div>
</body>
</html>