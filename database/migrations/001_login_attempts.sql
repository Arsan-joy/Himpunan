-- Migration 001: Tabel login_attempts untuk rate limiting
-- Jalankan sekali saat deployment

CREATE TABLE IF NOT EXISTS login_attempts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address   VARCHAR(45)  NOT NULL,
    username     VARCHAR(100) NOT NULL DEFAULT '',
    action       VARCHAR(50)  NOT NULL DEFAULT 'admin_login',
    attempted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_agent   VARCHAR(500) NOT NULL DEFAULT '',
    INDEX idx_ip_action_time (ip_address, action, attempted_at),
    INDEX idx_attempted_at   (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
