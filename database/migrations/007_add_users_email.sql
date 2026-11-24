-- Migration 007: tambah kolom email untuk alur forgot password.
ALTER TABLE users ADD COLUMN email VARCHAR(255) DEFAULT NULL AFTER username;
ALTER TABLE users ADD UNIQUE KEY uq_users_email (email);
