<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/password_reset.php';

$token = (string)($_GET['token'] ?? ($_POST['token'] ?? ''));
$row = $token !== '' ? pr_get_valid_token_row($token) : null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $row = $token !== '' ? pr_get_valid_token_row($token) : null;
    $password = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');
    if (!$row) {
        $error = 'Token tidak valid atau sudah kedaluwarsa.';
    } elseif (!csrf_verify()) {
        http_response_code(403);
        die('Permintaan tidak valid (403).');
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $password2) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            pr_set_user_password((int)$row['user_id'], $password);
            pr_mark_used((int)$row['id']);
            csrf_regenerate();
            header('Location: ' . BASE_URL . 'pages/login.php?reset=success');
            exit;
        } catch (Throwable $e) {
            error_log('[pages/reset.php] ' . $e->getMessage());
            $error = 'Gagal menyimpan password baru. Silakan coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
  <style>
    body{background:#eef2f7;font-family:Inter,system-ui,Segoe UI,Arial,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0}.card{background:#fff;max-width:420px;width:100%;padding:26px;border-radius:16px;box-shadow:0 20px 50px rgba(2,6,23,.08)}h1{margin:0 0 12px;color:#0b3a7c}.field{display:flex;flex-direction:column;margin-bottom:12px}label{font-size:13px;color:#334155;margin-bottom:6px}input{padding:12px;border:1px solid #e2e8f0;border-radius:10px}.btn{width:100%;padding:12px;border:0;border-radius:12px;background:#0ea5e9;color:#fff;font-weight:800}.error{background:#fff1f2;border:1px solid #fecdd3;color:#be123c;border-radius:10px;padding:10px;margin-bottom:10px}
  </style>
</head>
<body>
<form class="card" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= h($token) ?>">
  <h1>Reset Password</h1>
  <?php if (!$row): ?><div class="error">Token reset tidak valid atau sudah kedaluwarsa.</div>
  <?php else: ?>
    <?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
    <div class="field"><label>Password Baru</label><input type="password" name="password" required minlength="8" autocomplete="new-password"></div>
    <div class="field"><label>Ulangi Password Baru</label><input type="password" name="password2" required minlength="8" autocomplete="new-password"></div>
    <button class="btn" type="submit">Simpan Password</button>
  <?php endif; ?>
</form>
</body>
</html>
