<?php
// Halaman error generik — tidak menampilkan detail teknis
$code = http_response_code();
$messages = [
    403 => 'Akses Ditolak',
    404 => 'Halaman Tidak Ditemukan',
    500 => 'Terjadi Kesalahan Sistem',
];
$title = $messages[$code] ?? 'Terjadi Kesalahan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?> | HMTA ITERA</title>
<style>
body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafc;color:#334155}
.box{text-align:center;padding:2rem;max-width:480px}
h1{font-size:4rem;margin:0;color:#94a3b8}
h2{font-size:1.5rem;margin:.5rem 0 1rem}
p{color:#64748b}
a{color:#2563eb;text-decoration:none}
</style>
</head>
<body>
<div class="box">
  <h1><?= (int)$code ?></h1>
  <h2><?= htmlspecialchars($title) ?></h2>
  <p>
    <?php if ($code === 500): ?>
      Terjadi kesalahan sistem. Tim kami sedang menangani masalah ini.
    <?php elseif ($code === 403): ?>
      Anda tidak memiliki izin untuk mengakses halaman ini.
    <?php else: ?>
      Halaman yang Anda cari tidak ditemukan.
    <?php endif; ?>
  </p>
  <a href="/">← Kembali ke Beranda</a>
</div>
</body>
</html>
