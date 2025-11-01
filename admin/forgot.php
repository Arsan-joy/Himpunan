<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/password_reset.php';

// Jika sudah login user biasa, tolak
if (is_logged_in() && !is_admin()) { header('Location: ' . BASE_URL); exit; }

$info = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if ($username === '') {
        $error = 'Masukkan username.';
    } else {
        $stmt = db()->prepare("SELECT id, role, active FROM users WHERE username=? LIMIT 1");
        $stmt->execute([$username]);
        $u = $stmt->fetch();
        if (!$u || !$u['active']) {
            $error = 'User tidak ditemukan / tidak aktif.';
        } elseif (!in_array($u['role'], ['admin','super_admin'], true)) {
            $error = 'Gunakan halaman Lupa Password User untuk akun non-admin.';
        } else {
            // Buat token 30 menit
            $token = pr_create_token_for_user((int)$u['id'], 30);
            $resetLink = BASE_URL . 'admin/reset.php?token=' . urlencode($token);
            $info = 'Link reset (berlaku 30 menit): ' . $resetLink;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lupa Password Admin</title>
  <style>
    body{background:#eef2f7;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0}
    .card{background:#fff;max-width:420px;width:100%;padding:26px;border-radius:16px;box-shadow:0 20px 50px rgba(2,6,23,.08)}
    h1{margin:0 0 8px;font-size:22px;color:#0b3a7c}
    .muted{color:#64748b;margin:0 0 12px}
    .field{display:flex;flex-direction:column;margin-bottom:12px}
    label{font-size:13px;color:#334155;margin-bottom:6px}
    input{padding:12px;border:1px solid #e2e8f0;border-radius:10px}
    .btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(135deg,#60a5fa,#2563eb);color:#fff;font-weight:800;cursor:pointer}
    .error{background:#fff1f2;border:1px solid #fecdd3;color:#be123c;border-radius:10px;padding:10px;margin-bottom:10px}
    .info{background:#ecfeff;border:1px solid #bae6fd;color:#075985;border-radius:10px;padding:10px;margin-top:10px;word-break:break-all}
    .switch{margin-top:12px;text-align:center}
    .switch a{color:#2563eb;text-decoration:none;font-weight:700}
  </style>
</head>
<body>
  <form class="card" method="post">
    <h1>Lupa Password (Admin)</h1>
    <p class="muted">Masukkan username admin/super admin Anda.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="field">
      <label>Username</label>
      <input name="username" required autocomplete="username">
    </div>
    <button class="btn" type="submit">Buat Link Reset</button>
    <?php if ($info): ?><div class="info"><?= htmlspecialchars($info) ?></div><?php endif; ?>
    <div class="switch"><a href="<?= BASE_URL ?>admin/login.php">Kembali ke Login Admin</a></div>
  </form>
</body>
</html>