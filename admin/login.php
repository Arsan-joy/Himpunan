<?php
require_once __DIR__ . '/../includes/functions.php';

// Seed admin bila tabel users kosong dan input = admin/admin0105
function ensure_admin_seed(string $username, string $password): void {
    $row = db()->query("SELECT COUNT(*) c FROM users")->fetch();
    $has = (int)($row['c'] ?? 0);
    if ($has === 0 && $username === 'admin' && $password === 'admin0105') {
        $stmt = db()->prepare("INSERT INTO users (username, password_hash, role, active) VALUES (?, ?, 'admin', 1)");
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $next     = $_POST['next'] ?? '';

    ensure_admin_seed($username, $password);

    $stmt = db()->prepare("SELECT id, username, password_hash, role, active FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !$user['active']) {
        $error = 'User tidak ditemukan / tidak aktif.';
    } elseif (!password_verify($password, $user['password_hash'])) {
        $error = 'Password salah.';
    } else {
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
        header('Location: ' . ($next ?: BASE_URL . 'admin/'));
        exit;
    }
}
$next = $_GET['next'] ?? '';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Login Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f5f7fb;min-height:100vh;display:grid;place-items:center;margin:0}
.card{background:#fff;border-radius:16px;box-shadow:0 20px 50px rgba(0,0,0,.08);padding:32px;max-width:360px;width:100%}
h1{margin:0 0 12px;font-size:22px}
.muted{color:#6b7280;font-size:14px;margin:0 0 16px}
.field{display:flex;flex-direction:column;margin-bottom:12px}
label{font-size:13px;color:#374151;margin-bottom:6px}
input{padding:12px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:15px}
.btn{width:100%;padding:12px 14px;border:0;border-radius:10px;background:#3e6fa0;color:#fff;font-weight:700;cursor:pointer}
.error{background:#fee2e2;color:#991b1b;padding:10px 12px;border-radius:8px;font-size:14px;margin-bottom:10px}
.hint{font-size:12px;color:#6b7280;margin-top:8px}
</style>
</head>
<body>
<form class="card" method="post">
    <h1>Login Admin</h1>
    <p class="muted">Masuk untuk mengelola konten.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
    <div class="field">
        <label>Username</label>
        <input name="username" required autocomplete="username">
    </div>
    <div class="field">
        <label>Password</label>
        <input name="password" type="password" required autocomplete="current-password">
    </div>
    <button class="btn" type="submit">Masuk</button>
    <div class="hint">Default: admin / admin0105 (dibuat otomatis jika users masih kosong)</div>
</form>
</body>
</html>