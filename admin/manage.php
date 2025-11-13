<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Batasi akses "users" hanya super admin
if (($_GET['m'] ?? '') === 'users' && !is_super_admin()) { http_response_code(403); echo 'Forbidden'; exit; }

/* Helper untuk opsi dinamis dari SQL */
function fetch_options_sql(string $sql, string $valueCol = 'id', string $labelCol = 'name'): array {
    $rows = db()->query($sql)->fetchAll();
    return array_map(fn($r)=>[
        'value' => (string)($r[$valueCol] ?? ''),
        'label' => (string)($r[$labelCol] ?? '')
    ], $rows);
}

/* Modul konfigurasi */
$modules = [
  'departemen' => [
      'title' => 'Departemen','table' => 'departments','order' => 'id DESC',
      'fields' => [
          'name' => ['label'=>'Nama','required'=>true],
          'slug' => ['label'=>'Slug','required'=>true],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
      ],
  ],
  'divisi' => [
      'title' => 'Divisi','table' => 'divisions','order' => 'id DESC',
      'fields' => [
          'department_id' => ['label'=>'Departemen','type'=>'select','required'=>true,'options_sql'=>"SELECT id, name FROM departments ORDER BY name ASC"],
          'name' => ['label'=>'Nama','required'=>true],
          'slug' => ['label'=>'Slug','required'=>true],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          'member_count' => ['label'=>'Jumlah Anggota'],
      ],
  ],
  'kabinet' => [
      'title' => 'Kabinet','table' => 'kabinet','order' => 'id DESC',
      'fields' => [
          'name' => ['label'=>'Nama Kabinet','required'=>true],
          'period' => ['label'=>'Periode (mis. 2024-2025)','required'=>true],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          'logo_upload' => ['label'=>'Upload Logo','type'=>'file','accept'=>'image/*','target'=>'logo_url','subdir'=>'kabinet','allowed'=>['jpg','jpeg','png','webp','svg'],'maxMB'=>10],
      ],
  ],
  // PENGURUS HIMPUNAN (inti organisasi)
  'pengurus' => [
      'title' => 'Pengurus Himpunan','table' => 'organization_leaders','order' => 'COALESCE(sort_order, id) ASC',
      'fields' => [
          'name' => ['label'=>'Nama','required'=>true],
          'role' => ['label'=>'Jabatan','type'=>'select','required'=>true,'options'=>[
              'Kepala Himpunan',
              'Sekretaris Umum 1','Sekretaris Umum 2',
              'Bendahara Umum 1','Bendahara Umum 2',
              'BPO','Ketua Senat'
          ]],
          'photo_file' => ['label'=>'Foto','type'=>'file','accept'=>'image/*','target'=>'photo_url','subdir'=>'org','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>5],
          'linkedin_url' => ['label'=>'LinkedIn URL'],
          'email' => ['label'=>'Email'],
          'sort_order' => ['label'=>'Urutan Tampil'],
      ],
  ],
  // Leader Departemen (Kepala & Sekretaris Departemen)
  'leaders' => [
      'title' => 'Leader Departemen','table' => 'leaders','order' => 'COALESCE(sort_order, id) ASC',
      'fields' => [
          'department_id' => ['label'=>'Departemen','type'=>'select','required'=>true,'options_sql'=>"SELECT id, name FROM departments ORDER BY name ASC"],
          'name' => ['label'=>'Nama','required'=>true],
          'role' => ['label'=>'Peran','type'=>'select','options'=>['Kepala Departemen','Sekretaris Departemen'],'required'=>true],
          'photo_file' => ['label'=>'Foto','type'=>'file','accept'=>'image/*','target'=>'photo_url','subdir'=>'leaders','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>5],
          'linkedin_url' => ['label'=>'LinkedIn URL'],
          'email' => ['label'=>'Email'],
          'sort_order' => ['label'=>'Urutan Tampil'],
      ],
  ],
  // Program Kerja Departemen
  'programs' => [
      'title' => 'Program Kerja','table' => 'programs','order' => 'COALESCE(sort_order, id) ASC',
      'fields' => [
          'department_id' => ['label'=>'Departemen','type'=>'select','required'=>true,'options_sql'=>"SELECT id, name FROM departments ORDER BY name ASC"],
          'name' => ['label'=>'Nama Program','required'=>true],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          'frequency' => ['label'=>'Frekuensi','type'=>'select','options'=>['Harian','Mingguan','Bulanan','Tahunan','Ad-hoc']],
          'sort_order' => ['label'=>'Urutan Tampil'],
      ],
  ],
  // Anggota Divisi (superadmin juga bisa mengakses)
  'anggota' => [
      'title' => 'Anggota','table' => 'members','order' => 'id DESC',
      'fields' => [
          'name' => ['label'=>'Nama Lengkap','required'=>true],
          'student_id' => ['label'=>'NIM/NPM'],
          'email' => ['label'=>'Email'],
          'phone' => ['label'=>'No. HP'],
          'photo_file' => ['label'=>'Foto','type'=>'file','accept'=>'image/*','target'=>'photo_url','subdir'=>'members','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>5],
          'kabinet_id' => ['label'=>'Kabinet','type'=>'select','options_sql'=>"SELECT id, CONCAT(name, ' (', IFNULL(period,''), ')') AS name FROM kabinet ORDER BY id DESC"],
          'department_id' => ['label'=>'Departemen','type'=>'select','options_sql'=>"SELECT id, name FROM departments ORDER BY name ASC"],
          'division_id'   => ['label'=>'Divisi','type'=>'select','options_sql'=>"SELECT id, name FROM divisions ORDER BY name ASC"],
          'role' => ['label'=>'Peran di Divisi','type'=>'select','options'=>['Kepala Divisi','Staff']],
          'active' => ['label'=>'Aktif?','type'=>'bool'],
          'joined_at' => ['label'=>'Tanggal Bergabung','type'=>'date'],
      ],
  ],
  'foto' => [
      'title' => 'Foto','table' => 'photos','order' => 'id DESC',
      'fields' => [
          'album' => ['label'=>'Album','required'=>true],
          'photo_file' => ['label'=>'Upload Gambar','type'=>'file','accept'=>'image/*','target'=>'url','subdir'=>'photos','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>20,'required'=>true],
          'caption' => ['label'=>'Caption'],
      ],
  ],
  'materi' => [
      'title' => 'Materi','table' => 'materials','order' => 'id DESC',
      'fields' => [
          'title' => ['label'=>'Judul','required'=>true],
          'file_upload' => ['label'=>'Upload File (PDF/DOC/PPT)','type'=>'file','accept'=>'.pdf,.doc,.docx,.ppt,.pptx','target'=>'file_url','subdir'=>'materials','allowed'=>['pdf','doc','docx','ppt','pptx'],'maxMB'=>128,'required'=>true],
          'is_public' => ['label'=>'Publik?','type'=>'bool'],
      ],
  ],
  'kegiatan' => [
      'title' => 'Kegiatan','table' => 'events','order' => 'start_date DESC',
      'fields' => [
          'title' => ['label'=>'Judul','required'=>true],
          'description' => ['label'=>'Deskripsi','type'=>'textarea'],
          'start_date' => ['label'=>'Mulai','type'=>'date','required'=>true],
          'end_date' => ['label'=>'Selesai','type'=>'date'],
          'is_all_day' => ['label'=>'Sehari Penuh?','type'=>'bool'],
          'type' => ['label'=>'Tipe (academic/holiday/event)','type'=>'select','options'=>['academic','holiday','event'],'required'=>true],
          'image_upload' => ['label'=>'Gambar (opsional)','type'=>'file','accept'=>'image/*','target'=>'image_url','subdir'=>'events','allowed'=>['jpg','jpeg','png','webp'],'maxMB'=>10],
      ],
  ],
  'users' => [
      'title' => 'Pengguna','table' => 'users','order' => 'id DESC',
      'fields' => [
          'username' => ['label'=>'Username','required'=>true],
          'password' => ['label'=>'Password (kosong = tidak ganti)','type'=>'password'],
          'role' => ['label'=>'Role','type'=>'select','options'=>['user','admin'],'required'=>true],
          'active' => ['label'=>'Aktif?','type'=>'bool'],
      ],
  ],
];

/* Guard over-limit POST */
function ini_to_bytes(string $val): int { $val=trim($val); $u=strtolower(substr($val,-1)); $n=(int)$val; return $u==='g'?$n*1024*1024*1024:($u==='m'?$n*1024*1024:($u==='k'?$n*1024:(int)$val)); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cl = (int)$_SERVER['CONTENT_LENGTH'] ?? 0;
    $postMax = ini_to_bytes(ini_get('post_max_size'));
    if ($cl>0 && $postMax>0 && $cl>$postMax) { $_SESSION['flash_error']='Ukuran unggahan melebihi post_max_size.'; header('Location: manage.php?m='.urlencode($_GET['m'] ?? 'departemen')); exit; }
}

$moduleKey = $_GET['m'] ?? 'departemen';
if (!isset($modules[$moduleKey])) { http_response_code(404); echo "Modul tidak ditemukan"; exit; }
$mod = $modules[$moduleKey];
$table = $mod['table']; $order = $mod['order'] ?? 'id DESC'; $title = $mod['title'] ?? ucfirst($moduleKey);

/* Create/Update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

    // Validasi required
    $errors=[];
    foreach ($mod['fields'] as $col => $meta) {
        if (!empty($meta['required'])) {
            $type = $meta['type'] ?? 'text';
            if ($type==='file') {
                if (!$id && (empty($_FILES[$col]) || $_FILES[$col]['error']===UPLOAD_ERR_NO_FILE)) $errors[] = ($meta['label'] ?? $col).' wajib diunggah.';
            } else {
                $val = trim((string)($_POST[$col] ?? ''));
                if ($val==='') $errors[] = ($meta['label'] ?? $col).' wajib diisi.';
            }
        }
    }
    if ($moduleKey==='users' && !is_super_admin() && isset($_POST['role'])) $errors[]='Hanya Super Admin yang boleh mengubah peran pengguna.';
    if ($errors){ $_SESSION['flash_error']=implode(' ', $errors); header('Location: manage.php?m='.urlencode($moduleKey).($id?'&id='.$id:'')); exit; }

    $cols=[]; $vals=[]; $fileAssignments=[];

    foreach ($mod['fields'] as $col => $meta) {
        $type = $meta['type'] ?? 'text';

        if ($type === 'file') {
            try {
                $uploaded = save_uploaded_file($col, $meta['subdir'] ?? 'uploads', (array)($meta['allowed'] ?? []), (int)($meta['maxMB'] ?? 50));
                if ($uploaded) { $fileAssignments[$meta['target']] = $uploaded; }
            } catch (Throwable $e) {
                $_SESSION['flash_error'] = 'Upload gagal: '.$e->getMessage();
                header('Location: manage.php?m='.urlencode($moduleKey).($id?'&id='.$id:'')); exit;
            }
            continue;
        }

        if ($moduleKey==='users' && $col==='password') {
            $pwd = trim($_POST['password'] ?? '');
            if ($pwd!==''){ $cols[]='password_hash'; $vals[]=password_hash($pwd, PASSWORD_DEFAULT); }
            continue;
        }

        if (($meta['type'] ?? '') === 'bool') {
            $val = isset($_POST[$col]) ? 1 : 0;
            $cols[] = $col; $vals[] = $val; continue;
        }

        // Select / text / date
        $val = $_POST[$col] ?? null;
        $cols[] = $col; $vals[] = $val;
    }

    foreach ($fileAssignments as $tcol => $url){ $cols[]=$tcol; $vals[]=$url; }

    if ($id) {
        if ($cols) {
            $sets = implode(',', array_map(fn($c)=>"$c = ?", $cols));
            $stmt = db()->prepare("UPDATE $table SET $sets WHERE id = ?");
            $vals[] = $id; $stmt->execute($vals);
        }
    } else {
        if ($cols) {
            $colStr = implode(',', $cols); $qStr = implode(',', array_fill(0, count($cols), '?'));
            $stmt = db()->prepare("INSERT INTO $table ($colStr) VALUES ($qStr)");
            $stmt->execute($vals);
        }
    }

    header('Location: manage.php?m=' . urlencode($moduleKey)); exit;
}

/* Delete */
if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    if ($moduleKey==='users' && !is_super_admin()) { $_SESSION['flash_error']='Hanya Super Admin yang boleh menghapus pengguna.'; header('Location: manage.php?m=users'); exit; }
    db()->prepare("DELETE FROM $table WHERE id = ?")->execute([$delId]);
    header('Location: manage.php?m=' . urlencode($moduleKey)); exit;
}

/* List + edit */
$list = db()->query("SELECT * FROM $table ORDER BY $order")->fetchAll();
$editing = null;
if (isset($_GET['id'])) { $stmt = db()->prepare("SELECT * FROM $table WHERE id = ? LIMIT 1"); $stmt->execute([(int)$_GET['id']]); $editing=$stmt->fetch(); }

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
    <div class="brand"><div class="brand-logo"><i class="fa-solid fa-check"></i></div><div><h1>HMTA Admin</h1><div style="opacity:.8;font-size:12px">Control Panel</div></div></div>
    <nav class="nav">
      <a href="<?= BASE_URL ?>admin/"><i class="fa-solid fa-house"></i> Dashboard</a>
      <a class="<?= $moduleKey==='pengurus'?'active':'' ?>" href="?m=pengurus"><i class="fa-solid fa-user-tie"></i> Kelola Pengurus</a>
      <a class="<?= $moduleKey==='leaders'?'active':'' ?>" href="?m=leaders"><i class="fa-solid fa-users-gear"></i> Kelola Leader</a>
      <a class="<?= $moduleKey==='programs'?'active':'' ?>" href="?m=programs"><i class="fa-solid fa-rocket"></i> Kelola Program</a>
      <a class="<?= $moduleKey==='departemen'?'active':'' ?>" href="?m=departemen"><i class="fa-solid fa-building"></i> Kelola Departemen</a>
      <a class="<?= $moduleKey==='divisi'?'active':'' ?>" href="?m=divisi"><i class="fa-solid fa-people-group"></i> Kelola Divisi</a>
      <a class="<?= $moduleKey==='kabinet'?'active':'' ?>" href="?m=kabinet"><i class="fa-solid fa-layer-group"></i> Kelola Kabinet</a>
      <a class="<?= $moduleKey==='anggota'?'active':'' ?>" href="?m=anggota"><i class="fa-solid fa-user-friends"></i> Kelola Anggota</a>
      <a class="<?= $moduleKey==='foto'?'active':'' ?>" href="?m=foto"><i class="fa-solid fa-image"></i> Kelola Foto</a>
      <a class="<?= $moduleKey==='materi'?'active':'' ?>" href="?m=materi"><i class="fa-solid fa-book"></i> Kelola Materi</a>
      <a class="<?= $moduleKey==='kegiatan'?'active':'' ?>" href="?m=kegiatan"><i class="fa-solid fa-calendar-days"></i> Kelola Kegiatan</a>
      <?php if (is_super_admin()): ?>
      <a class="<?= $moduleKey==='users'?'active':'' ?>" href="?m=users"><i class="fa-solid fa-user-shield"></i> Kelola Pengguna</a>
      <a href="tools/import_members.php"><i class="fa-solid fa-file-import"></i> Impor Anggota</a>
      <?php endif; ?>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="title"><h2><i class="fa-solid fa-gear"></i> Kelola <?= htmlspecialchars($title) ?></h2></div>
      <div class="pill"><div class="avatar"><i class="fa-solid fa-user"></i></div><div><div style="font-weight:800"><?= htmlspecialchars($_SESSION['user']['username']) ?></div><div style="font-size:12px;opacity:.8"><?= is_super_admin()?'Super Admin':'Admin' ?></div></div></div>
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
          <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>

          <?php foreach ($mod['fields'] as $col => $meta):
            $label = $meta['label'] ?? ucfirst($col);
            $type  = $meta['type'] ?? 'text';
            $valKey= $meta['target'] ?? $col;
            $val   = $editing[$valKey] ?? '';
            $accept= $meta['accept'] ?? null;
            $required = !empty($meta['required']);
            $options  = $meta['options'] ?? null;
            if (!$options && !empty($meta['options_sql'])) $options = fetch_options_sql($meta['options_sql']);
          ?>
          <div class="field">
            <label><?= htmlspecialchars($label) ?><?= $required ? ' *' : '' ?></label>

            <?php if ($type === 'bool'): ?>
              <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="<?= $col ?>" <?= $val ? 'checked' : '' ?>> <span>Ya</span></label>

            <?php elseif ($type === 'date'): ?>
              <input type="date" name="<?= $col ?>" value="<?= htmlspecialchars($val) ?>" <?= $required && !$editing ? 'required' : '' ?>>

            <?php elseif ($type === 'password'): ?>
              <input type="password" name="<?= $col ?>" placeholder="Kosongkan jika tidak ganti">

            <?php elseif ($type === 'textarea'): ?>
              <textarea name="<?= $col ?>" rows="3" <?= $required ? 'required' : '' ?>><?= htmlspecialchars($val) ?></textarea>

            <?php elseif ($type === 'select'): ?>
              <select name="<?= $col ?>" <?= $required ? 'required' : '' ?>>
                <option value="">— Pilih —</option>
                <?php foreach ((array)$options as $opt): 
                    $ov = is_array($opt) ? ($opt['value'] ?? '') : (string)$opt;
                    $ol = is_array($opt) ? ($opt['label'] ?? $ov) : ucfirst((string)$opt);
                ?>
                  <option value="<?= htmlspecialchars($ov) ?>" <?= ($val!=='' && (string)$val===(string)$ov)?'selected':'' ?>><?= htmlspecialchars($ol) ?></option>
                <?php endforeach; ?>
              </select>

            <?php elseif ($type === 'file'):
              $currentUrl = $editing[$meta['target'] ?? ''] ?? '';
            ?>
              <input type="file" name="<?= $col ?>" <?= $accept ? 'accept="'.htmlspecialchars($accept).'"' : '' ?> <?= $required && !$editing ? 'required' : '' ?>>
              <?php if ($currentUrl): ?>
                <div class="preview">
                  <?php if ($currentUrl && preg_match('~\.(jpg|jpeg|png|webp|gif|svg)$~i', $currentUrl)): ?>
                    <img class="thumb" src="<?= htmlspecialchars($currentUrl) ?>" alt="">
                  <?php elseif ($currentUrl): ?>
                    <a class="badge" href="<?= htmlspecialchars($currentUrl) ?>" target="_blank">Lihat</a>
                  <?php else: ?><span class="hint">-</span><?php endif; ?>
                </div>
                <div class="hint">Biarkan kosong jika tidak ingin mengganti.</div>
              <?php endif; ?>

            <?php else: ?>
              <input name="<?= $col ?>" value="<?= htmlspecialchars((string)$val) ?>" <?= $required ? 'required' : '' ?>>
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
                        <?php else: ?><span class="hint">-</span><?php endif; ?>
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
</body>
</html>