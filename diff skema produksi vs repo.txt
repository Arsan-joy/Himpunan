# Diff Skema: Produksi vs Repo — HMTA

**Tanggal:** 2026-11-21
**Produksi:** database `u782889506_oeacrre` (Hostinger), export struktur saja dari phpMyAdmin — **terverifikasi 0 baris INSERT, 14 CREATE TABLE, 13.182 byte**.
**Repo:** `database/schema.sql` di github.com/Arsan-joy/Himpunan (commit master e74249ce), 8 tabel.

**Kesimpulan: TIDAK SAMA.** Repo tertinggal 6 tabel + 3 perbedaan kolom. Fresh deploy dari `schema.sql` tidak akan jalan dengan kode produksi.

## 1. Tabel yang hanya ada di produksi (6)

| Tabel | Kolom kunci | Dipakai oleh |
|---|---|---|
| `members` | id, name, student_id, email, phone, photo_url, kabinet_id, department_id, division_id, role, active, joined_at, created_at | Halaman Anggota (903 anggota) |
| `leaders` | id, department_id, name, role, photo_url, linkedin_url, email, sort_order, created_at | Leader departemen (12 Leader Dept di dashboard) |
| `organization_leaders` | id, name, role, photo_url, linkedin_url, email, sort_order, created_at | Pimpinan organisasi (tanpa department_id — beda dengan `leaders`) |
| `programs` | id, department_id, name, description, frequency ENUM(Harian/Mingguan/Bulanan/Tahunan/Ad-hoc), sort_order, created_at | 30 Program di dashboard |
| `login_attempts` | id, ip_address, username, action (default 'admin_login'), attempted_at, user_agent | Rate limiter login admin (repo punya migrasinya: `database/migrations/001_login_attempts.sql`, tapi tidak ada di `schema.sql`) |
| `password_resets` | id, user_id, token, expires_at, used_at, ip, ua, created_at | Alur lupa password admin (`admin/forgot.php` — sudah dihapus dari live & harus dihapus dari repo, tapi tabelnya tetap dipakai alur reset yang aman) |

## 2. Kolom yang hanya ada di produksi (tabel yang sama di kedua tempat)

- **`departments.logo_url`** `varchar(255) DEFAULT NULL` — repo `schema.sql` tidak punya kolom ini. Ini yang dipakai `pages/samagri.php:112` (`$deptLogo = !empty($d['logo_url']) ...`). `pages/department.php` malah hardcode logo ke `banner1.png` — lihat §16 laporan audit kode.
- **`materials.category`** `enum('praktikum','laporan_kp','laporan_ta','Semester_3','Semester_4','Semester_5','Semester_6','Semester_7') NOT NULL DEFAULT 'Semester_3'` — repo tidak punya kolom ini.
- **`users.role`** — produksi: `enum('super_admin','admin','user')`; repo: `enum('admin','user')` (tidak ada `super_admin`). Akun superadmin live tidak bisa dibuat dari skema repo.

## 3. Bukan perbedaan (hanya format penulisan)

Yang berikut ini muncul di diff mentah tapi **bukan** perbedaan nyata — hanya cara mysqldump/phpMyAdmin menulis ulang definisi:

- `PRIMARY KEY` / `UNIQUE` ditulis sebagai baris terpisah di dump produksi, inline di `schema.sql` repo.
- `int(11)` vs `INT`, `timestamp NOT NULL DEFAULT current_timestamp()` vs `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`, `text DEFAULT NULL` vs `TEXT` — ekuivalen.
- `AUTO_INCREMENT` value di dump (opsional, dimatikan saat export) — hanya angka counter.

## 4. Tidak ada di produksi

- Tidak ada VIEW, TRIGGER, PROCEDURE, atau FUNCTION.
- Tidak ada tabel yang hanya ada di repo (`schema.sql` adalah subset dari produksi).

## 5. Yang harus dilakukan (untuk agent GitHub)

1. Tulis ulang `database/schema.sql` agar memuat **ke-14 tabel produksi** dengan definisi kolom persis seperti di atas (termasuk `departments.logo_url`, `materials.category`, `users.role` dengan `super_admin`).
2. Sinkronkan migrasi: `001_login_attempts.sql` sudah ada — pastikan isinya cocok dengan definisi produksi; tambahkan migrasi untuk 5 tabel lain yang hilang (`members`, `leaders`, `organization_leaders`, `programs`, `password_resets`) + kolom yang hilang, agar instalasi lama bisa di-upgrade tanpa fresh deploy.
3. Perbaiki `database/migrations/002_add_indexes.sql` yang gagal di DB baru (kolom `events.active` tidak ada) — lihat §13 laporan audit kode.
4. Setelah itu, verifikasi: fresh deploy dari `schema.sql` yang baru harus menghasilkan 14 tabel yang sama persis dengan produksi (bandingkan dengan file ini: `workspace/user/files/u782889506_oeacrre__1.sql`).

**Catatan keamanan:** file dump produksi yang dipakai di sini adalah export **struktur saja** (0 INSERT). Dump lengkap `hmta.sql` (berisi data ~900 anggota + hash password) TIDAK BOLEH diunggah ke AI/repo mana pun.
