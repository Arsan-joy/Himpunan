<?php
/**
 * config/database.php
 *
 * Kelas Database legacy — dipertahankan untuk kompatibilitas mundur.
 * Untuk kode baru, gunakan fungsi db() dari database/db.php.
 *
 * Kredensial dibaca dari konstanta yang sudah di-define oleh
 * database/config.php (yang membaca dari file .env).
 */
class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $charset;
    public  ?PDO   $conn = null;

    public function __construct()
    {
        // Baca dari konstanta .env — tidak ada hardcode di sini
        $this->host     = defined('DB_HOST')    ? DB_HOST    : '127.0.0.1';
        $this->db_name  = defined('DB_NAME')    ? DB_NAME    : '';
        $this->username = defined('DB_USER')    ? DB_USER    : '';
        $this->password = defined('DB_PASS')    ? DB_PASS    : '';
        $this->charset  = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
    }

    public function getConnection(): ?PDO
    {
        if ($this->conn instanceof PDO) return $this->conn;

        // DSN sudah menyertakan charset — tidak perlu "SET NAMES" terpisah
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $this->host,
            $this->db_name,
            $this->charset
        );

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Jangan ekspos detail koneksi ke browser
            error_log('[Database::getConnection] ' . $e->getMessage());

            if (defined('APP_ENV') && APP_ENV === 'development') {
                throw $e; // re-throw agar terlihat saat development
            }

            // Production: kembalikan null, biarkan pemanggil menangani
            $this->conn = null;
        }

        return $this->conn;
    }
}
