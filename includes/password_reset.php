<?php
// File ini diasumsikan dipanggil setelah includes/functions.php
// sehingga db(), BASE_URL, dll sudah tersedia.

function pr_create_token_for_user(int $userId, int $ttlMinutes = 30): string {
    $token = bin2hex(random_bytes(24)); // 48 hex chars
    $expires = (new DateTimeImmutable('+'.$ttlMinutes.' minutes'))->format('Y-m-d H:i:s');
    $stmt = db()->prepare("INSERT INTO password_resets (user_id, token, expires_at, ip, ua) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $token,
        $expires,
        $_SERVER['REMOTE_ADDR'] ?? null,
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250)
    ]);
    return $token;
}

function pr_get_valid_token_row(string $token): ?array {
    $stmt = db()->prepare("SELECT pr.*, u.username, u.role FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = ? LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) return null;
    if ($row['used_at'] !== null) return null;
    if (strtotime($row['expires_at']) < time()) return null;
    return $row;
}

function pr_mark_used(int $id): void {
    db()->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?")->execute([$id]);
}

function pr_set_user_password(int $userId, string $newPassword): void {
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    db()->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash, $userId]);
}