<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/rate_limiter.php';
require_once __DIR__ . '/../includes/password_reset.php';
require_once __DIR__ . '/../includes/mailer.php';

$sent = false;
$error = '';
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (rate_limit_is_blocked($clientIp, 'forgot_password', 5, 900)) {
        $error = 'Terlalu banyak permintaan reset. Silakan coba lagi dalam 15 menit.';
    } elseif (!csrf_verify()) {
        http_response_code(403);
        die('Permintaan tidak valid (403).');
    } else {
        rate_limit_record_attempt($clientIp, 'forgot_password', (string)($_POST['email'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = db()->prepare("SELECT id FROM users WHERE email = ? AND role IN ('admin','super_admin') AND active = 1 LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                try {
                    $token = pr_create_token_for_user((int)$user['id']);
                    $url = BASE_URL . 'admin/reset.php?token=' . urlencode($token);
                    send_reset_email($email, $url);
                } catch (Throwable $e) {
                    error_log('[admin/forgot.php] ' . $e->getMessage());
                }
            }
        }
        $sent = true;
        csrf_regenerate();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lupa Password Admin</title>
  <style>
    body{background:#eef2f7;font-family:Inter,system-ui,Segoe UI,Arial,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0}.card{background:#fff;max-width:380px;width:100%;padding:26px;border-radius:16px;box-shadow:0 20px 50px rgba(2,6,23,.08)}h1{margin:0 0 12px;color:#0b3a7c}.field{display:flex;flex-direction:column;margin-bottom:12px}label{font-size:13px;color:#334155;margin-bottom:6px}input{padding:12px;border:1px solid #e2e8f0;border-radius:10px}.btn{width:100%;padding:12px;border:0;border-radius:12px;background:#2563eb;color:#fff;font-weight:800}.ok,.error{border-radius:10px;padding:12px;margin-bottom:12px}.ok{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}.error{background:#fff1f2;color:#be123c;border:1px solid #fecdd3}.switch{text-align:center;margin-top:12px}.switch a{color:#2563eb;text-decoration:none;font-weight:700}
  </style>
</head>
<body>
<form class="card" method="post">
  <?= csrf_field() ?>
  <h1>Lupa Password Admin</h1>
  <?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
  <?php if ($sent): ?><div class="ok">Bila email terdaftar, instruksi reset telah dikirim.</div><?php endif; ?>
  <div class="field"><label>Email</label><input type="email" name="email" required autocomplete="email"></div>
  <button class="btn" type="submit">Kirim Instruksi Reset</button>
  <div class="switch"><a href="<?= BASE_URL ?>admin/login.php">Kembali ke Login</a></div>
</form>
</body>
</html>
