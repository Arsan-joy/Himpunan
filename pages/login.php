<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/rate_limiter.php';

$error = '';
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sleep(1);
    if (rate_limit_is_blocked($clientIp, 'user_login')) {
        $error = 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.';
    } elseif (!csrf_verify()) {
        http_response_code(403);
        die('Permintaan tidak valid (403).');
    } else {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $next     = safe_internal_redirect((string)($_POST['next'] ?? ''), BASE_URL);

    $stmt = db()->prepare("SELECT id, username, password_hash, role, active FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !$user['active']) {
        $error = 'User tidak ditemukan / tidak aktif.';
        rate_limit_record_attempt($clientIp, 'user_login', $username);
    } elseif (!password_verify($password, $user['password_hash'])) {
        $error = 'Password salah.';
        rate_limit_record_attempt($clientIp, 'user_login', $username);
    } elseif ($user['role'] !== 'user') {
        $error = 'Akun ini bukan User. Gunakan halaman Login Admin.';
        rate_limit_record_attempt($clientIp, 'user_login', $username);
    } else {
        rate_limit_reset($clientIp, 'user_login');
        session_regenerate_id(true);
        csrf_regenerate();
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
        header('Location: ' . ($next ?: BASE_URL));
        exit;
    }
    }
}
$next = safe_internal_redirect((string)($_GET['next'] ?? ''), '');
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login User</title>
<link rel="stylesheet" href="<?= BASE_URL ?>Resource/css/admin.css">
<style>
body{
    background:linear-gradient(120deg,#0b5ed7,#22c1c3);
    min-height:100vh;
    margin:0;display:grid;
    place-items:center;
    font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;
}
.card{
    background:#fff;
    max-width:420px;
    width:100%;
    padding:28px;
    border-radius:16px;
    box-shadow:0 30px 70px rgba(2,6,23,.12);
}
h1{
    margin:0 0 8px;
    font-size:22px;
    color:#0b3a7c;text-align:center
}
.muted{
    text-align:center;
    color:#64748b;
    margin:0 0 16px
}
.field{
    display:flex;
    flex-direction:column;
    margin-bottom:12px
}
label{
    font-size:13px;
    color:#334155;
    margin-bottom:6px
}
input{
    padding:12px;
    border:1px solid #e2e8f0;
    border-radius:10px
}
.btn{
    width:100%;
    padding:12px 14px;
    border:0;
    border-radius:12px;
    background:linear-gradient(135deg,#22c1c3,#0ea5e9);
    color:#fff;
    font-weight:800;
    cursor:pointer
}
.error{
    background:#fff1f2;
    border:1px solid #fecdd3;
    color:#be123c;
    border-radius:10px;
    padding:10px;
    margin-bottom:10px
}
.switch{
    margin-top:12px;
    text-align:center
}
.switch a{
    color:#0ea5e9;
    text-decoration:none;
    font-weight:700
}
.ok{
    background:#ecfdf5;
    color:#065f46;
    border:1px solid #a7f3d0;
    border-radius:10px;
    padding:10px;
    margin-bottom:12px
}
</style>
</head>
<body>
<form class="card" method="post">
  <?= csrf_field() ?>
  <h1>Login User</h1>
  <p class="muted">Masuk untuk mengakses materi dan fitur anggota</p>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
  <div class="field"><label>Username</label><input name="username" required autocomplete="username"></div>
  <div class="field"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
  <button class="btn" type="submit">Masuk</button>
  <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
    <div class="ok">Password berhasil diubah. Silakan login kembali.</div>
  <?php endif; ?>
  <div class="switch">
    <a href="<?= BASE_URL ?>pages/forgot.php">Lupa password?</a> ·
    <a href="<?= BASE_URL ?>admin/login.php">Login sebagai Admin</a>
  </div>
</form>
</body>
</html>