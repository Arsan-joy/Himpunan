<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/rate_limiter.php';
require_once __DIR__ . '/../includes/logger.php';

// Seed Super Admin (1 akun saja, sesuai config)
function ensure_super_admin_seed(): void {
    $stmt = db()->query("SELECT COUNT(*) c FROM users WHERE role='super_admin'");
    $has = (int)$stmt->fetch()['c'];
    if ($has === 0 && defined('SUPER_ADMIN_USERNAME') && SUPER_ADMIN_USERNAME) {
        $q = db()->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
        $q->execute([SUPER_ADMIN_USERNAME]);
        $row = $q->fetch();
        if ($row) {
            db()->prepare("UPDATE users SET role='super_admin' WHERE id=?")->execute([$row['id']]);
        } else {
            db()->prepare("INSERT INTO users (username,password_hash,role,active) VALUES (?,?, 'super_admin',1)")
              ->execute([SUPER_ADMIN_USERNAME, password_hash(SUPER_ADMIN_PASSWORD ?? '', PASSWORD_DEFAULT)]);
        }
    }
}

$error = '';
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Jeda minimum untuk memperlambat serangan otomatis
    sleep(1);

    // Cek rate limit — blokir jika terlalu banyak percobaan gagal
    if (rate_limit_is_blocked($clientIp, 'admin_login')) {
        $error = 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.';
    } else {
        // Verifikasi CSRF token
        if (!csrf_verify()) {
            http_response_code(403);
            log_security('csrf_violation', ['page' => 'admin_login']);
            die('Permintaan tidak valid (403).');
        }

        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $next     = $_POST['next'] ?? '';

        ensure_super_admin_seed();

        $stmt = db()->prepare("SELECT id, username, password_hash, role, active FROM users WHERE username=? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !$user['active']) {
            $error = 'Username atau password salah.';
            rate_limit_record_attempt($clientIp, 'admin_login', $username);
            log_security('login_failed', ['username' => $username, 'reason' => 'user_not_found_or_inactive']);
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = 'Username atau password salah.';
            rate_limit_record_attempt($clientIp, 'admin_login', $username);
            log_security('login_failed', ['username' => $username, 'reason' => 'wrong_password']);
        } elseif (!in_array($user['role'], ['admin','super_admin'], true)) {
            $error = 'Gunakan halaman login User.';
            rate_limit_record_attempt($clientIp, 'admin_login', $username);
            log_security('login_failed', ['username' => $username, 'reason' => 'wrong_role']);
        } else {
            // Login berhasil
            rate_limit_reset($clientIp, 'admin_login');
            session_regenerate_id(true); // Cegah session fixation
            csrf_regenerate();

            $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
            header('Location: ' . ($next ?: BASE_URL . 'admin/'));
            exit;
        }
    }
}

$next = $_GET['next'] ?? '';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin</title>
<link rel="stylesheet" href="<?= BASE_URL ?>Resource/css/admin.css">
<style>
body{background:#eef2f7;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0}
.card{background:#fff;max-width:380px;width:100%;padding:26px;border-radius:16px;box-shadow:0 20px 50px rgba(2,6,23,.08)}
h1{margin:0 0 8px;font-size:22px;color:#0b3a7c}
.muted{color:#64748b;margin:0 0 12px}
.field{display:flex;flex-direction:column;margin-bottom:12px}
label{font-size:13px;color:#334155;margin-bottom:6px}
input{padding:12px;border:1px solid #e2e8f0;border-radius:10px}
.btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:linear-gradient(135deg,#60a5fa,#2563eb);color:#fff;font-weight:800;cursor:pointer}
.error{background:#fff1f2;border:1px solid #fecdd3;color:#be123c;border-radius:10px;padding:10px;margin-bottom:10px}
.switch{margin-top:10px;text-align:center}
.switch a{color:#2563eb;text-decoration:none;font-weight:700}
</style>
</head>
<body>
<form class="card" method="post">
  <h1>Login Admin</h1>
  <p class="muted">Masuk sebagai Admin</p>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
  <div class="field"><label>Username</label><input name="username" required autocomplete="username"></div>
  <div class="field"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
  <button class="btn" type="submit">Masuk</button>
  <div class="switch">
    <a href="<?= BASE_URL ?>pages/login.php">Login sebagai User</a> ·
    <a href="<?= BASE_URL ?>admin/forgot.php">Lupa password?</a>
  </div>
</form>
</body>
</html>
