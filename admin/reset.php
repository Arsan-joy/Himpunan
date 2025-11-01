<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/password_reset.php';

$token = $_GET['token'] ?? '';
$row   = $token ? pr_get_valid_token_row($token) : null;

$error = ''; $done = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $row   = $token ? pr_get_valid_token_row($token) : null;
    $pwd   = (string)($_POST['password'] ?? '');
    $pwd2  = (string)($_POST['password2'] ?? '');

    if (!$row) {
        $error = 'Token tidak valid atau sudah kedaluwarsa.';
    } elseif (strlen($pwd) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($pwd !== $pwd2) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        pr_set_user_password((int)$row['user_id'], $pwd);
        pr_mark_used((int)$row['id']);
        $done = true;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
  <style>
    body{  
        background:#eef2f7;
        font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;
        display:grid;
        place-items:center;
        min-height:100vh;
        margin:0;
    }
    .card{
        background:#fff;
        max-width:420px;
        width:100%;
        padding:26px;
        border-radius:16px;
        box-shadow:0 20px 50px rgba(2,6,23,.08);
    }
    h1{
        margin:0 0 8px;
        font-size:22px;
        color:#0b3a7c;
    }
    .muted{
        color:#64748b;
        margin:0 0 12px;
    }
    .field{
        display:flex;
        flex-direction:column;
        margin-bottom:12px;
    }
    label{
        font-size:13px;
        color:#334155;
        margin-bottom:6px;
    }
    input{
        padding:12px;
        border:1px solid #e2e8f0;
        border-radius:10px;
    }
    .btn{
        width:100%;
        padding:12px 14px;
        border:0;
        border-radius:12px;
        background:linear-gradient(135deg,#34d399,#059669);
        color:#fff;
        font-weight:800;
        cursor:pointer;
    }
    .error{
        background:#fff1f2;
        border:1px solid #fecdd3;
        color:#be123c;
        border-radius:10px;
        padding:10px;
        margin-bottom:10px;
    }
    .ok{
        background:#ecfeff;
        border:1px solid #bae6fd;
        color:#065f46;
        border-radius:10px;
        padding:10px;
        margin-bottom:10px;
    }
    .switch{
        margin-top:12px;
        text-align:center;
    }
    .switch a{
        color:#2563eb;
        text-decoration:none;
        font-weight:700;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Reset Password</h1>
    <?php if ($done): ?>
      <div class="ok">Password berhasil diubah. Silakan login kembali.</div>
      <div class="switch"><a href="<?= BASE_URL ?>admin/login.php">Ke Login Admin</a></div>
    <?php elseif (!$row): ?>
      <div class="error">Token reset tidak valid atau sudah kadaluarsa.</div>
      <div class="switch"><a href="<?= BASE_URL ?>admin/forgot.php">Minta token baru</a></div>
    <?php else: ?>
      <p class="muted">Ubah password untuk: <strong><?= htmlspecialchars($row['username']) ?></strong></p>
      <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="field">
          <label>Password Baru</label>
          <input type="password" name="password" required minlength="6">
        </div>
        <div class="field">
          <label>Ulangi Password Baru</label>
          <input type="password" name="password2" required minlength="6">
        </div>
        <button class="btn" type="submit">Simpan Password</button>
      </form>
      <div class="switch"><a href="<?= BASE_URL ?>admin/login.php">Batal</a></div>
    <?php endif; ?>
  </div>
</body>
</html>