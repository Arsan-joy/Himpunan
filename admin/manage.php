<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

/* Konfigurasi modul + field
   - type: text|textarea|number|date|bool|password|select|file
   - untuk file, gunakan key tambahan:
     'target' => nama kolom tujuan di DB,
     'subdir' => sub folder upload,
     'allowed'=> ['jpg','png',...],
     'maxMB'  => batas ukuran MB,
     'accept' => atribut input accept
*/
$modules = [
  'departemen' => [
      'title' => 'Departemen',
      'table' => 'departments',
      'fields' => [
          'name' => ['label'=>'Nama'],
          'slug' => ['label'=>'Slug'],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
      ],
      'order' => 'id DESC',
  ],
  'divisi' => [
      'title' => 'Divisi',
      'table' => 'divisions',
      'fields' => [
          'department_id' => ['label'=>'Department ID'],
          'name' => ['label'=>'Nama'],
          'slug' => ['label'=>'Slug'],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          'member_count' => ['label'=>'Jumlah Anggota'],
      ],
      'order' => 'id DESC',
  ],
  'kabinet' => [
      'title' => 'Kabinet',
      'table' => 'kabinet',
      'fields' => [
          'name' => ['label'=>'Nama Kabinet'],
          'period' => ['label'=>'Periode'],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          // Upload logo langsung
          'logo_upload' => ['label'=>'Upload Logo','type'=>'file','accept'=>'image/*','target'=>'logo_url','subdir'=>'kabinet','allowed'=>['jpg','jpeg','png','webp','svg'],'maxMB'=>8],
      ],
      'order' => 'id DESC',
  ],
  'foto' => [
      'title' => 'Foto',
      'table' => 'photos',
      'fields' => [
          'album' => ['label'=>'Album'],
          // Upload gambar -> simpan ke kolom url
          'photo_file' => ['label'=>'Upload Gambar','type'=>'file','accept'=>'image/*','target'=>'url','subdir'=>'photos','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>10],
          'caption' => ['label'=>'Caption'],
      ],
      'order' => 'id DESC',
  ],
  'materi' => [
      'title' => 'Materi',
      'table' => 'materials',
      'fields' => [
          'title' => ['label'=>'Judul'],
          // Upload file -> simpan ke kolom file_url
          'file_upload' => ['label'=>'Upload File (PDF/DOC/PPT)','type'=>'file','accept'=>'.pdf,.doc,.docx,.ppt,.pptx','target'=>'file_url','subdir'=>'materials','allowed'=>['pdf','doc','docx','ppt','pptx'],'maxMB'=>50],
          'is_public' => ['label'=>'Publik?','type'=>'bool'],
      ],
      'order' => 'id DESC',
  ],
  'kegiatan' => [
      'title' => 'Kegiatan',
      'table' => 'events',
      'fields' => [
          'title' => ['label'=>'Judul'],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          'start_date' => ['label'=>'Mulai','type'=>'date'],
          'end_date' => ['label'=>'Selesai','type'=>'date'],
          'is_all_day' => ['label'=>'Sehari Penuh?','type'=>'bool'],
          'type' => ['label'=>'Tipe (academic/holiday/event)'],
          // Gambar opsional untuk kartu event
          'image_upload' => ['label'=>'Gambar (opsional)','type'=>'file','accept'=>'image/*','target'=>'image_url','subdir'=>'events','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>6],
      ],
      'order' => 'start_date DESC',
  ],
  'users' => [
      'title' => 'Pengguna',
      'table' => 'users',
      'fields' => [
          'username' => ['label'=>'Username'],
          'password' => ['label'=>'Password (kosong = tidak ganti)','type'=>'password'],
          'role' => ['label'=>'Role (admin/user)'],
          'active' => ['label'=>'Aktif?','type'=>'bool'],
      ],
      'order' => 'id DESC',
  ],
];

$moduleKey = $_GET['m'] ?? 'departemen';
if (!isset($modules[$moduleKey])) { http_response_code(404); echo "Modul tidak ditemukan"; exit; }
$mod = $modules[$moduleKey];
$table = $mod['table'];
$order = $mod['order'] ?? 'id DESC';
$title = $mod['title'] ?? ucfirst($moduleKey);

// Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

    $cols = [];
    $vals = [];
    $fileAssignments = []; // [target_col => uploaded_url]

    // Tangani fields
    foreach ($mod['fields'] as $col => $meta) {
        $type = $meta['type'] ?? 'text';

        if ($type === 'file') {
            // Upload file; jika user tidak upload saat edit, biarkan kolom lama
            try {
                $uploaded = save_uploaded_file($col, $meta['subdir'], $meta['allowed'], (int)($meta['maxMB'] ?? 50));
                if ($uploaded) {
                    $targetCol = $meta['target']; // kolom tujuan di DB
                    $fileAssignments[$targetCol] = $uploaded;
                }
            } catch (Throwable $e) {
                // Bisa tampilkan pesan error sederhana
                $_SESSION['flash_error'] = 'Upload gagal: ' . $e->getMessage();
            }
            continue;
        }

        if ($moduleKey === 'users' && $col === 'password') {
            $pwd = trim($_POST['password'] ?? '');
            if ($pwd !== '') {
                $cols[] = 'password_hash';
                $vals[] = password_hash($pwd, PASSWORD_DEFAULT);
            }
            continue;
        }

        $val = $_POST[$col] ?? null;
        if ($type === 'bool') $val = isset($_POST[$col]) ? 1 : 0;

        $cols[] = $col;
        $vals[] = $val;
    }

    // Gabungkan fileAssignments (hanya jika ada upload)
    foreach ($fileAssignments as $tcol => $url) {
        $cols[] = $tcol;
        $vals[] = $url;
    }

    if ($id) {
        if (!empty($cols)) {
            $sets = implode(',', array_map(fn($c) => "$c = ?", $cols));
            $sql = "UPDATE $table SET $sets WHERE id = ?";
            $stmt = db()->prepare($sql);
            $vals[] = $id;
            $stmt->execute($vals);
        }
    } else {
        if (!empty($cols)) {
            $colStr = implode(',', $cols);
            $qStr   = implode(',', array_fill(0, count($cols), '?'));
            $sql = "INSERT INTO $table ($colStr) VALUES ($qStr)";
            $stmt = db()->prepare($sql);
            $stmt->execute($vals);
        }
    }

    header('Location: manage.php?m=' . urlencode($moduleKey));
    exit;
}

// Delete
if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    db()->prepare("DELETE FROM $table WHERE id = ?")->execute([$delId]);
    header('Location: manage.php?m=' . urlencode($moduleKey)); exit;
}

// List + edit
$list = db()->query("SELECT * FROM $table ORDER BY $order")->fetchAll();
$editing = null;
if (isset($_GET['id'])) {
    $stmt = db()->prepare("SELECT * FROM $table WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$_GET['id']]);
    $editing = $stmt->fetch();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola <?= htmlspecialchars($title) ?></title>
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
      <a href="<?= BASE_URL ?>admin/"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a class="<?= $moduleKey==='departemen'?'active':'' ?>" href="?m=departemen"><i class="fa-solid fa-building"></i> Kelola Departemen</a>
      <a class="<?= $moduleKey==='divisi'?'active':'' ?>" href="?m=divisi"><i class="fa-solid fa-people-group"></i> Kelola Divisi</a>
      <a class="<?= $moduleKey==='kabinet'?'active':'' ?>" href="?m=kabinet"><i class="fa-solid fa-layer-group"></i> Kelola Kabinet</a>
      <a class="<?= $moduleKey==='foto'?'active':'' ?>" href="?m=foto"><i class="fa-solid fa-image"></i> Kelola Foto</a>
      <a class="<?= $moduleKey==='materi'?'active':'' ?>" href="?m=materi"><i class="fa-solid fa-book"></i> Kelola Materi</a>
      <a class="<?= $moduleKey==='kegiatan'?'active':'' ?>" href="?m=kegiatan"><i class="fa-solid fa-calendar-days"></i> Kelola Kegiatan</a>
      <a class="<?= $moduleKey==='users'?'active':'' ?>" href="?m=users"><i class="fa-solid fa-user-shield"></i> Kelola Pengguna</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="title"><button id="sidebarToggle" class="btn btn-primary" style="display:none;"><i class="fa-solid fa-bars"></i></button><h2><i class="fa-solid fa-gear"></i> Kelola <?= htmlspecialchars($title) ?></h2></div>
      <div class="pill"><div class="avatar"><i class="fa-solid fa-user"></i></div><div><div style="font-weight:800"><?= htmlspecialchars($_SESSION['user']['username']) ?></div><div style="font-size:12px;opacity:.8">Administrator</div></div></div>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="content-card" style="border-left:4px solid #ef4444;color:#b91c1c;background:#fff7f7;margin-bottom:12px">
        <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
      </div>
    <?php endif; ?>

    <div class="manage-wrap">
      <div class="content-card form">
        <div class="section-title"><i class="fa-solid fa-plus"></i> <?= $editing?'Edit':'Tambah' ?> Data</div>
        <form method="post" enctype="multipart/form-data">
          <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
          <?php endif; ?>

          <?php foreach ($mod['fields'] as $col => $meta):
              $label = $meta['label'] ?? ucfirst($col);
              $type  = $meta['type'] ?? 'text';
              $val   = $editing[$meta['target'] ?? $col] ?? '';
              $accept= $meta['accept'] ?? null;
          ?>
          <div class="field">
            <label><?= htmlspecialchars($label) ?></label>

            <?php if ($type === 'bool'): ?>
              <label style="display:flex;align-items:center;gap:8px">
                <input type="checkbox" name="<?= $col ?>" <?= $val ? 'checked' : '' ?>> <span>Ya</span>
              </label>

            <?php elseif ($type === 'date'): ?>
              <input type="date" name="<?= $col ?>" value="<?= htmlspecialchars($val) ?>">

            <?php elseif ($type === 'password'): ?>
              <input type="password" name="<?= $col ?>" placeholder="Kosongkan jika tidak ganti">

            <?php elseif ($type === 'textarea'): ?>
              <textarea name="<?= $col ?>" rows="3"><?= htmlspecialchars($val) ?></textarea>

            <?php elseif ($type === 'file'): 
              $currentUrl = $editing[$meta['target'] ?? ''] ?? '';
            ?>
              <input type="file" name="<?= $col ?>" <?= $accept ? 'accept="'.htmlspecialchars($accept).'"' : '' ?>>
              <?php if ($currentUrl): ?>
                <div class="preview">
                  <?php if (preg_match('~\.(jpg|jpeg|png|webp|gif|svg)$~i', $currentUrl)): ?>
                    <img class="thumb" src="<?= htmlspecialchars($currentUrl) ?>" alt="">
                  <?php else: ?>
                    <a class="badge" href="<?= htmlspecialchars($currentUrl) ?>" target="_blank">File saat ini</a>
                  <?php endif; ?>
                </div>
                <div class="hint">Biarkan kosong jika tidak ingin mengganti.</div>
              <?php endif; ?>

            <?php else: ?>
              <input name="<?= $col ?>" value="<?= htmlspecialchars($val) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>

          <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $editing ? 'Update' : 'Simpan' ?></button>
        </form>
      </div>

      <div class="content-card">
        <div class="section-title"><i class="fa-solid fa-list"></i> Data</div>
        <div style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <?php foreach ($mod['fields'] as $col => $meta): ?>
                <th><?= htmlspecialchars($meta['label'] ?? ucfirst($col)) ?></th>
              <?php endforeach; ?>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($list as $row): ?>
              <tr>
                <td><?= (int)$row['id'] ?></td>
                <?php foreach ($mod['fields'] as $col => $meta):
                    $type = $meta['type'] ?? 'text';
                    $showKey = $meta['target'] ?? $col;
                    $v = $row[$showKey] ?? '';
                ?>
                  <td>
                    <?php if ($type === 'bool'): ?>
                      <?= ($row[$col] ?? 0) ? 'Ya' : 'Tidak' ?>
                    <?php elseif ($type === 'password'): ?>
                      ••••••
                    <?php elseif ($type === 'file'): ?>
                      <?php if ($v && preg_match('~\.(jpg|jpeg|png|webp|gif|svg)$~i', $v)): ?>
                        <img class="thumb" src="<?= htmlspecialchars($v) ?>" alt="">
                      <?php elseif ($v): ?>
                        <a class="badge" href="<?= htmlspecialchars($v) ?>" target="_blank">Lihat</a>
                      <?php else: ?>
                        <span class="hint">-</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <?= htmlspecialchars((string)$v) ?>
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
                <td>
                  <a class="btn btn-primary" href="?m=<?= urlencode($moduleKey) ?>&id=<?= (int)$row['id'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                  <a class="btn btn-danger" href="?m=<?= urlencode($moduleKey) ?>&del=<?= (int)$row['id'] ?>" onclick="return confirm('Hapus data ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="<?= BASE_URL ?>Resource/js/admin.js"></script>
</body>
</html>