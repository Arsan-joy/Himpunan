<?php
require_once __DIR__ . '/../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $next     = $_POST['next'] ?? '';

    $stmt = db()->prepare("SELECT id, username, password_hash, role, active FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !$user['active']) {
        $error = 'User tidak ditemukan / tidak aktif.';
    } elseif (!password_verify($password, $user['password_hash'])) {
        $error = 'Password salah.';
    } elseif ($user['role'] !== 'user') {
        $error = 'Akun ini bukan User. Gunakan halaman Login Admin.';
    } else {
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
        header('Location: ' . ($next ?: BASE_URL));
        exit;
    }
}
$next = $_GET['next'] ?? '';
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
</style>
</head>
<body>
<form class="card" method="post">
  <h1>Login User</h1>
  <p class="muted">Masuk untuk mengakses materi dan fitur anggota</p>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
  <div class="field"><label>Username</label><input name="username" required autocomplete="username"></div>
  <div class="field"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
  <button class="btn" type="submit">Masuk</button>
  <div class="switch"><a href="<?= BASE_URL ?>admin/login.php">Login sebagai Admin</a></div>
</form>
</body>
</html>