<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$modules = [
  'departemen' => [
      'table' => 'departments',
      'fields' => [
          'name' => 'Nama',
          'slug' => 'Slug',
          'description' => 'Deskripsi',
      ],
      'order' => 'id DESC',
  ],
  'divisi' => [
      'table' => 'divisions',
      'fields' => [
          'department_id' => 'Department ID',
          'name' => 'Nama',
          'slug' => 'Slug',
          'description' => 'Deskripsi',
          'member_count' => 'Jumlah Anggota',
      ],
      'order' => 'id DESC',
  ],
  'kabinet' => [
      'table' => 'kabinet',
      'fields' => [
          'name' => 'Nama Kabinet',
          'period' => 'Periode',
          'description' => 'Deskripsi',
          'logo_url' => 'Logo URL',
      ],
      'order' => 'id DESC',
  ],
  'foto' => [
      'table' => 'photos',
      'fields' => [
          'album' => 'Album',
          'url' => 'URL Gambar',
          'caption' => 'Caption',
      ],
      'order' => 'id DESC',
  ],
  'materi' => [
      'table' => 'materials',
      'fields' => [
          'title' => 'Judul',
          'file_url' => 'URL File',
          'is_public' => ['label' => 'Publik?', 'type' => 'bool'],
      ],
      'order' => 'id DESC',
  ],
  'kegiatan' => [
      'table' => 'events',
      'fields' => [
          'title' => 'Judul',
          'description' => 'Deskripsi',
          'start_date' => ['label' => 'Mulai', 'type' => 'date'],
          'end_date' => ['label' => 'Selesai', 'type' => 'date'],
          'is_all_day' => ['label' => 'Sehari Penuh?', 'type' => 'bool'],
          'type' => 'Tipe (academic/holiday/event)',
          'image_url' => 'Gambar (URL opsional)',
      ],
      'order' => 'start_date DESC',
  ],
  'users' => [
      'table' => 'users',
      'fields' => [
          'username' => 'Username',
          'password' => ['label' => 'Password (kosong = tidak ganti)', 'type' => 'password'],
          'role' => 'Role (admin/user)',
          'active' => ['label' => 'Aktif?', 'type' => 'bool'],
      ],
      'order' => 'id DESC',
  ],
];

$moduleKey = $_GET['m'] ?? 'departemen';
if (!isset($modules[$moduleKey])) {
    http_response_code(404);
    echo "Modul tidak ditemukan";
    exit;
}
$mod = $modules[$moduleKey];
$table = $mod['table'];
$order = $mod['order'] ?? 'id DESC';

// Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

    $cols = [];
    $vals = [];
    foreach ($mod['fields'] as $col => $meta) {
        $type = is_array($meta) ? ($meta['type'] ?? 'text') : 'text';
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

    if ($id) {
        $sets = implode(',', array_map(fn($c) => "$c = ?", $cols));
        $sql = "UPDATE $table SET $sets WHERE id = ?";
        $stmt = db()->prepare($sql);
        $vals[] = $id;
        $stmt->execute($vals);
    } else {
        $colStr = implode(',', $cols);
        $qStr   = implode(',', array_fill(0, count($cols), '?'));
        $sql = "INSERT INTO $table ($colStr) VALUES ($qStr)";
        $stmt = db()->prepare($sql);
        $stmt->execute($vals);
    }

    header('Location: manage.php?m=' . urlencode($moduleKey));
    exit;
}

// Delete
if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    db()->prepare("DELETE FROM $table WHERE id = ?")->execute([$delId]);
    header('Location: manage.php?m=' . urlencode($moduleKey));
    exit;
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
<meta charset="utf-8">
<title>Kelola <?= htmlspecialchars(ucfirst($moduleKey)) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f5f7fb;margin:0}
header{background:#263b50;color:#fff;padding:14px 18px;display:flex;justify-content:space-between;align-items:center}
header a{color:#fff;text-decoration:none;font-size:14px}
.wrap{max-width:1100px;margin:24px auto;padding:0 16px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.card{background:#fff;border-radius:14px;box-shadow:0 16px 40px rgba(0,0,0,.06);padding:18px}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eef2f7;font-size:14px}
th{text-align:left;color:#6b7280}
.field{display:flex;flex-direction:column;margin-bottom:10px}
label{font-size:13px;color:#374151;margin-bottom:6px}
input,textarea,select{padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;font-size:14px}
.row{display:flex;gap:8px;align-items:center}
.btn{display:inline-block;background:#3e6fa0;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700}
.muted{color:#6b7280;font-size:13px}
.switch{display:flex;align-items:center;gap:8px}
@media (max-width: 900px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<header>
  <div><a href="index.php">&larr; Dashboard</a></div>
  <strong>Kelola <?= htmlspecialchars(ucfirst($moduleKey)) ?></strong>
  <div><a href="<?= BASE_URL ?>admin/logout.php">Logout</a></div>
</header>

<div class="wrap grid">
  <div class="card">
    <h3><?= $editing ? 'Edit' : 'Tambah' ?> Data</h3>
    <form method="post">
      <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
      <?php endif; ?>

      <?php foreach ($mod['fields'] as $col => $meta):
        $label = is_array($meta) ? ($meta['label'] ?? ucfirst($col)) : $meta;
        $type  = is_array($meta) ? ($meta['type'] ?? 'text') : 'text';
        $val   = $editing[$col] ?? '';
      ?>
        <div class="field">
          <label><?= htmlspecialchars($label) ?></label>
          <?php if ($type === 'bool'): ?>
            <label class="switch">
              <input type="checkbox" name="<?= $col ?>" <?= $val ? 'checked' : '' ?>> <span class="muted">Ya</span>
            </label>
          <?php elseif ($type === 'date'): ?>
            <input type="date" name="<?= $col ?>" value="<?= htmlspecialchars($val) ?>">
          <?php elseif ($type === 'password'): ?>
            <input type="password" name="<?= $col ?>" placeholder="Kosongkan jika tidak ganti">
          <?php else: ?>
            <?php if ($col === 'description'): ?>
              <textarea name="<?= $col ?>" rows="3"><?= htmlspecialchars($val) ?></textarea>
            <?php else: ?>
              <input name="<?= $col ?>" value="<?= htmlspecialchars($val) ?>">
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <button class="btn" type="submit"><?= $editing ? 'Update' : 'Simpan' ?></button>
    </form>
  </div>

  <div class="card">
    <h3>Data</h3>
    <div class="muted" style="margin-bottom:8px">Klik Edit untuk mengubah, Hapus untuk menghapus.</div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <?php foreach ($mod['fields'] as $col => $_): ?>
            <th><?= htmlspecialchars(is_array($_)?($_['label'] ?? ucfirst($col)) : $_) ?></th>
          <?php endforeach; ?>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($list as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <?php foreach ($mod['fields'] as $col => $meta):
              $type = is_array($meta) ? ($meta['type'] ?? 'text') : 'text';
              $val  = $row[$col] ?? ($col === 'password' ? '' : '');
              if ($col === 'password') $val = '••••••';
            ?>
              <td>
                <?php if ($type === 'bool'): ?>
                  <?= ($row[$col] ?? 0) ? 'Ya' : 'Tidak' ?>
                <?php else: ?>
                  <?= htmlspecialchars((string)$val) ?>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
            <td class="row">
              <a class="btn" href="?m=<?= urlencode($moduleKey) ?>&id=<?= (int)$row['id'] ?>">Edit</a>
              <a class="btn" style="background:#dc2626" href="?m=<?= urlencode($moduleKey) ?>&del=<?= (int)$row['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>