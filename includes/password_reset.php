<?php
/**
 * includes/password_reset.php
 *
 * Helper fungsi untuk alur reset password.
 * Dipanggil setelah includes/functions.php sehingga db() sudah tersedia.
 */

/**
 * Buat token reset password untuk user tertentu.
 * Token disimpan di tabel password_resets.
 *
 * @throws RuntimeException jika operasi DB gagal
 */
function pr_create_token_for_user(int $userId, int $ttlMinutes = 30): string
{
    $token   = bin2hex(random_bytes(24)); // 48 hex chars, kriptografis aman
    $expires = (new DateTimeImmutable('+' . $ttlMinutes . ' minutes'))->format('Y-m-d H:i:s');

    try {
        $stmt = db()->prepare(
            "INSERT INTO password_resets (user_id, token, expires_at, ip, ua)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $userId,
            $token,
            $expires,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
        ]);
    } catch (PDOException $e) {
        error_log('[pr_create_token_for_user] ' . $e->getMessage());
        throw new RuntimeException('Gagal membuat token reset. Silakan coba lagi.');
    }

    return $token;
}

/**
 * Ambil baris token yang masih valid (belum dipakai, belum expired).
 * Return null jika token tidak valid.
 */
function pr_get_valid_token_row(string $token): ?array
{
    try {
        $stmt = db()->prepare(
            "SELECT pr.*, u.username, u.role
             FROM password_resets pr
             JOIN users u ON u.id = pr.user_id
             WHERE pr.token = ?
             LIMIT 1"
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('[pr_get_valid_token_row] ' . $e->getMessage());
        return null;
    }

    if (!$row)                                    return null; // token tidak ada
    if ($row['used_at'] !== null)                 return null; // sudah dipakai
    if (strtotime($row['expires_at']) < time())   return null; // sudah expired

    return $row;
}

/**
 * Tandai token sebagai sudah digunakan.
 */
function pr_mark_used(int $id): void
{
    try {
        db()->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?")
            ->execute([$id]);
    } catch (PDOException $e) {
        error_log('[pr_mark_used] ' . $e->getMessage());
        // Non-fatal: token mungkin bisa dipakai ulang, tapi tidak kritis
    }
}

/**
 * Update password hash user.
 *
 * @throws RuntimeException jika operasi DB gagal
 */
function pr_set_user_password(int $userId, string $newPassword): void
{
    // PASSWORD_BCRYPT dengan cost default (12 di PHP 8.x) sudah aman
    $hash = password_hash($newPassword, PASSWORD_BCRYPT);

    try {
        db()->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
            ->execute([$hash, $userId]);
    } catch (PDOException $e) {
        error_log('[pr_set_user_password] ' . $e->getMessage());
        throw new RuntimeException('Gagal menyimpan password baru. Silakan coba lagi.');
    }
}
