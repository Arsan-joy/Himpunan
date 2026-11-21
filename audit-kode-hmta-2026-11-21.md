# Audit Kode Sumber Website HMTA ITERA (hmtaitera.com)

**Tanggal:** 21 November 2026
**Repo:** https://github.com/Arsan-joy/Himpunan — "Website Himpunan Mahasiswa Teknik Pertambangan ITERA"
**Salinan lokal (read-only):** `~/workspace/your_files/audit-hmta/hmta-source/` (commit terakhir repo 2026-10-02)
**Laporan black-box yang diverifikasi:** `audit-hmta-2026-11-21.md` (39 temuan: 0 Kritis, 9 Tinggi, 17 Sedang, 13 Rendah/Info)
**Metode:** pembacaan file saja (read-only). Tidak ada clone ulang, tidak ada perubahan, tidak ada commit/push, tidak ada eksekusi kode terhadap database live, tidak ada percobaan login. Pemeriksaan riwayat git **tidak dapat dilakukan** karena perintah `git`/exec di dalam repo diblokir oleh pemeriksaan risiko eksekusi — lihat §6 Keterbatasan.

> ⚠️ **Temuan terpenting audit ini ada di §0 (Drift).** Repo ini berisi lapisan keamanan yang sudah diperkeras (CSRF, rate limiter, security headers, session aman, `display_errors=0`), tetapi **tidak satu pun** dari lapisan itu terlihat di situs live. Artinya produksi hampir pasti menjalankan kode yang **lebih lama** dari repo ini. Konsekuensinya: temuan "DITERIMA/baik di kode" di bawah ini **belum tentu berlaku di produksi**, dan temuan "buruk di kode" yang juga ada di live (mis. delete via GET, footer email, include Minerfesto) adalah yang paling bisa dipastikan. Setiap temuan diberi label **KODE** (apa yang ada di repo) dan, bila relevan, **LIVE** (apa yang terlihat di black-box).

---

## §0. Drift Repo vs Produksi — temuan payung (Tinggi)

**Bukti kode (repo) vs bukti live (black-box):**

| Fitur | Di repo (kode) | Di live (black-box) |
|---|---|---|
| CSRF di form login admin | Ada — `admin/login.php` memanggil `csrf_verify()` dan form berisi `csrf_field()` | Tidak ada field token di form login (§3.4) |
| Rate limit login | Ada — `includes/rate_limiter.php` (5 percobaan / 900 dtk), dipakai `admin/login.php` | Tidak ada (login tanpa batas percobaan, §11.2) |
| Security headers | Ada — `includes/security_headers.php` (X-Frame-Options DENY, nosniff, Referrer-Policy, CSP ketat `default-src 'self'`), dikirim `database/config.php` | Hanya `upgrade-insecure-requests`; tanpa X-Frame-Options/HSTS (§3.2) |
| Cookie sesi | `database/config.php`: `session.cookie_httponly=1`, `session.cookie_samesite='Strict'`, `session.use_strict_mode=1`, `secure` saat HTTPS | `PHPSESSID` tanpa HttpOnly/SameSite (§3.2) |
| `display_errors` | `database/config.php`: `display_errors=0` di production | Warning PHP tampil di halaman Minerfesto (§3.5) |
| CSRF di `manage.php` | Ada — handler POST memanggil `csrf_verify()` + `csrf_regenerate()` | Delete via GET tanpa token (§11.1) — konsisten dengan kode LAMA |
| Validasi upload | Ada — whitelist ekstensi, cek MIME `finfo`, batas ukuran, nama acak (`includes/functions.php: save_uploaded_file()`) | UI tidak menampilkan batas; nama file materi berupa hash 32-hex (§11.4, §12.4) |
| Versi PHP | `DATABASE_AUDIT_REPORT.md` (21 Apr 2026) menyebut PHP 8.5.5 | Live: PHP/8.3.37 (§3.2) |
| Skema DB | `database/schema.sql`: `users.role` ENUM hanya `('admin','user')` — tanpa `super_admin` | Live: ada 1 akun `super_admin` (§12) |
| Nama file upload | `save_uploaded_file()` → `bin2hex(random_bytes(16))` (32 hex acak) | File di `uploads/` repo memakai format `YYYYMMDD_HHMMSS-<6hex>-<nama-asli>`; link materi live berupa `<hash-32hex>.pdf` |

**Kesimpulan drift:** repo HEAD (2026-10-02) adalah hasil refactor hardening (terdokumentasi di `DATABASE_AUDIT_REPORT.md`, 21 April 2026) yang **belum pernah di-deploy** — atau di-deploy sebagian. Produksi menjalankan varian lama: bootstrap kemungkinan masih `config/config.php` (legacy, `display_errors=1`, `session_start()` polos) — ini menjelaskan SEMUA observasi live sekaligus (warning tampil, tanpa header keamanan, cookie tanpa HttpOnly/SameSite, tanpa CSRF, tanpa rate limit). **Rekomendasi utama: samakan produksi dengan repo (atau sebaliknya) sebelum memperbaiki apa pun — kalau tidak, perbaikan kode tidak akan pernah sampai ke pengguna.**

**Caveat penting:** karena drift ini, dua temuan Kritis di bawah (forgot.php menampilkan link reset; reset_password.php) **harus diverifikasi di live** sebelum diputuskan seberapa gawat — kalau produksi menjalankan kode lama, file-file itu mungkin belum ada/tidak berfungsi di sana. Sebaliknya, kalau produksi menjalankan kode repo, keduanya adalah celah takeover akun.

---

## 1. Rahasia di Repo (Secrets)

### 1.1 — TIDAK ADA kredensial DB/API key hardcoded di file konfigurasi (Info — kabar baik)
- `config/config.php` dan `database/config.php` membaca semua kredensial dari environment (`.env`): `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `SUPER_ADMIN_USERNAME`, `SUPER_ADMIN_PASSWORD`.
- `.env.example` hanya berisi placeholder kosong (`DB_HOST=127.0.0.1`, `DB_NAME=hmta`, `DB_USER`/`DB_PASS` kosong, `SUPER_ADMIN_*` kosong).
- `.gitignore` mencakup `.env`; tidak ada file `.env` di dalam repo.
- Tidak ada API key, token, atau password hardcoded yang ditemukan di file-file yang dibaca (`config/`, `database/`, `includes/`, `admin/`).
- **Keterbatasan:** riwayat commit git **tidak diperiksa** (perintah git diblokir). Kredensial yang pernah ter-commit lalu dihapus tetap bisa ada di histori. **Perbaiki:** Arsan harus menjalankan `gitleaks` atau `trufflehog` sendiri di repo lokal, dan merotasi kredensial bila ada temuan.

### 1.2 — Password superadmin DARURAT hardcoded di `reset_password.php` (Tinggi — Kritis bila file berfungsi di produksi)
- **File:** `reset_password.php` (root repo), baris 12:
  ```php
  $new = 'AdminBaru#2025'; // GANTI password darurat di sini
  ```
- File ini bisa diakses lewat web dan hanya meminta parameter GET `?confirm=YES` (baris 4–8), lalu mereset/membuat akun superadmin dengan password di atas (baris 13–20). Password ini **terbaca publik** karena repo-nya publik.
- `.htaccess` **tidak** memblokir file ini (hanya memblokir dotfile dan `debug*.php`).
- **Peredam saat ini:** baris 3 memakai path yang salah untuk lokasi file ini —
  ```php
  require_once __DIR__ . '/../includes/functions.php';
  ```
  `__DIR__` adalah root repo, sehingga path ini mengarah ke *parent* root (`/../includes/`) yang tidak ada → file akan fatal error bila dibuka dari root. Tapi file ini jelas disalin dari `scripts/emergency_reset_superadmin.php` (disebut di pesan akhirnya) — bila dipindah ke `scripts/` atau path-nya "diperbaiki", ia langsung menjadi celah takeover superadmin.
- **Tidak terkait temuan black-box** (black-box tidak memeriksa file ini — file ini luput dari daftar URL yang diuji).
- **Perbaiki (segera):** hapus `reset_password.php` dari repo DAN dari produksi; ganti password superadmin; jangan pernah menyimpan skrip reset darurat di dalam web root. Bila butuh reset darurat, lakukan via CLI/phpMyAdmin, bukan file web.

### 1.3 — Token reset password dibuat dengan aman, tapi DISALURKAN dengan tidak aman (Kritis — lihat 3.1)
- Tokennya sendiri kuat (`includes/password_reset.php: pr_create_token_for_user()` memakai `bin2hex(random_bytes(24))`, kedaluwarsa 30 menit, sekali pakai via `used_at`). Masalahnya ada di cara token itu disampaikan — lihat temuan 3.1.

---

## 2. CSRF (Cross-Site Request Forgery)

### 2.1 — Login admin & `manage.php`: CSRF SUDAH diterapkan di repo — §3.4 & §11.1 sebagian DIBANTAH (untuk repo)
- `admin/login.php` (baris ±36): `if (!csrf_verify()) { http_response_code(403); ... die('Permintaan tidak valid (403).'); }`, dan form berisi `<?= csrf_field() ?>`.
- `admin/manage.php`: handler POST memanggil `csrf_verify()` + `csrf_regenerate()`; form berisi `<?= csrf_field() ?>` (baris ±548). Helper-nya kuat: `includes/csrf.php` memakai `hash_equals` dan `csrf_regenerate()`.
- **Tapi di LIVE tidak ada token di form login (§3.4)** — ini bukti drift (§0), bukan bukti kodenya salah. Bila repo di-deploy, §3.4 untuk login admin tertutup.

### 2.2 — Delete via GET TANPA token — §11.1 DIKONFIRMASI di kode (Tinggi)
- `admin/manage.php` baris 464–480:
  ```php
  /* Delete */
  if (isset($_GET['del'])) {
      $delId = (int)$_GET['del'];
      if ($moduleKey === 'users' && !is_super_admin()) { ... }
      try {
          db()->prepare("DELETE FROM $table WHERE id = ?")->execute([$delId]);
      } ...
  ```
  Tidak ada `csrf_verify()`, tidak ada pengecekan method POST, tidak ada token sama sekali. Tautan hapus di tabel adalah `<a href="?m=...&del=...">` dengan `onclick="return confirm(...)"` — konfirmasi JavaScript **bukan** perlindungan CSRF. Admin yang sedang login dan membuka halaman jahat bisa kehilangan data (semua 10 modul).
- **Perbaiki:** jadikan hapus sebagai POST + `csrf_verify()` (seperti handler simpan), atau minimal minta token di query dan verifikasi dengan `hash_equals`.

### 2.3 — Toggle maintenance di dashboard TANPA CSRF (Sedang)
- `admin/index.php`: form POST "Aktifkan Global"/"Nonaktifkan" tidak berisi `csrf_field()` dan handler-nya tidak memanggil `csrf_verify()` — hanya mengecek `is_super_admin()`. Seorang superadmin yang login bisa dipancing mematikan/menyalakan maintenance mode lewat CSRF.
- **Terkait §12.3** (black-box: "tidak ada token CSRF yang terlihat di halaman superadmin") — **dikonfirmasi untuk toggle ini**, tapi lihat 2.4.

### 2.4 — `maintenance.php`: CSRF SUDAH diterapkan — §12.3 sebagian DIBANTAH (untuk repo)
- `admin/maintenance.php`: handler POST memanggil `csrf_verify()` dan form berisi `csrf_field()`. Jadi halaman Maintenance Mode yang lengkap aman dari CSRF; yang bocor hanya tombol cepat di dashboard (2.3).

### 2.5 — Impor anggota (`import_members.php`) TANPA CSRF (Tinggi — gabung dengan 7.2)
- `admin/tools/import_members.php`: seluruh handler POST (impor CSV massal, membuat/mengubah akun) **tidak memanggil `csrf_verify()`** dan form-nya tidak berisi `csrf_field()`. File ini tidak pernah me-require `includes/csrf.php`.
- **Perbaiki:** tambahkan `csrf_field()` di form dan `csrf_verify()` di awal handler POST, sama seperti `manage.php`.

### 2.6 — Login user & form kontak TANPA CSRF (Rendah–Sedang)
- `pages/login.php`: tidak ada `csrf_field()`/`csrf_verify()` → login CSRF dimungkinkan (dikombinasikan dengan open redirect di 7.5).
- `pages/annual/minerfesto.php` (±baris 290): form kontak `<form id="contactForm">` tanpa `method`, `action`, maupun token CSRF — **dan tanpa handler server sama sekali** (form mati; lihat 10.3). Risiko CSRF-nya nihil karena tidak ada efek server, tapi form ini menyesatkan pengguna.
- `admin/forgot.php` dan `admin/reset.php`: tanpa CSRF (reset memakai token sekali pakai sebagai gantinya — dapat diterima, kecuali masalah 3.1).

---

## 3. Password

### 3.1 — KRITIS: `admin/forgot.php` MENAMPILKAN link reset password di layar (Kritis — verifikasi live wajib)
- `admin/forgot.php` (±baris 22–27):
  ```php
  $token     = pr_create_token_for_user((int)$u['id'], 30);
  $resetLink = BASE_URL . 'admin/reset.php?token=' . urlencode($token);
  $info      = 'Link reset (berlaku 30 menit): ' . $resetLink;
  ```
  Link reset **ditampilkan langsung di halaman** (`<div class="info"><?= htmlspecialchars($info) ?></div>`) kepada siapa pun yang memasukkan username admin yang valid. **Tidak ada email yang dikirim.** Siapa pun yang tahu username admin (mis. dari halaman anggota/pengurus) bisa membuat token reset dan mengambil alih akun admin/superadmin tanpa login — cukup buka `admin/forgot.php`, ketik username, salin linknya.
- Tokennya sendiri aman (acak kriptografis, 30 menit, sekali pakai — `includes/password_reset.php`), dan `reset.php` memvalidasi token dengan benar. Yang rusak adalah **saluran pengiriman** token.
- Tambahan: `forgot.php` membocorkan enumerasi user (pesan berbeda untuk "tidak ditemukan" vs "bukan admin" vs sukses), tanpa rate limit dan tanpa CSRF.
- **Tidak terkait temuan black-box** (black-box hanya membaca target tautan "Lupa password?", tidak menguji halaman ini).
- **Perbaiki (segera):** JANGAN tampilkan link reset di layar. Kirim via email (PHPMailer/smtp) ke alamat email admin, atau hapus/nonaktifkan `forgot.php` sampai ada pengiriman email. Tambahkan rate limit + pesan generik ("bila username terdaftar, instruksi reset telah dikirim").

### 3.2 — Password disimpan dengan `password_hash`/`password_verify` — §12.1 sebagian DIBANTAH (kabar baik, dengan catatan)
- Login admin (`admin/login.php` ±baris 55): `password_verify($password, $user['password_hash'])`.
- Login user (`pages/login.php` ±baris 16): sama.
- Pembuatan/ubah password di `manage.php` (baris 413–416): `password_hash($pwd, PASSWORD_DEFAULT)` bila field diisi.
- Impor CSV (`admin/tools/import_members.php` baris 75 & 80): `$hash = password_hash($plain, PASSWORD_DEFAULT);` — **password dari CSV di-hash sebelum masuk DB**, baik untuk user baru maupun update.
- Reset password (`includes/password_reset.php`): `password_hash($newPassword, PASSWORD_BCRYPT)`.
- **Koreksi terhadap §12.1:** klaim "kemungkinan tersimpan tanpa hashing" **tidak terbukti di kode repo** — penyimpanan di DB sudah benar (bcrypt). Yang tetap bermasalah: (a) **file CSV-nya sendiri berisi password plaintext** (format `No.;NIM;Nama;Password;Username`) — siapa pun yang memegang file CSV memegang semua password; (b) impor massal tanpa CSRF dan tanpa batas superadmin (lihat 2.5, 7.2); (c) tidak ada kebijakan kekuatan password (lihat 3.3).
- **Perbaiki:** ganti alur impor — CSV tidak boleh berisi kolom password; akun baru dibuat dengan password acak sekali pakai / link aktivasi, dan pengguna wajib menggantinya saat login pertama. Hapus file CSV dari server setelah impor.

### 3.3 — Kebijakan password lemah (Sedang)
- `admin/reset.php`: `strlen($pwd) < 6` — minimal hanya 6 karakter, tanpa syarat kompleksitas. `manage.php` tidak memvalidasi panjang password sama sekali.
- **Terkait §11.2** (ketiga password admin sangat lemah): kode tidak bisa memastikan/mencegah password lemah — tidak ada aturan minimal di login, pembuatan user, maupun impor CSV.
- **Perbaiki:** minimal 10–12 karakter + cek terhadap daftar password umum; paksa ganti password bawaan/lemah; tambahkan fitur "ganti password sendiri" untuk admin (lihat 7.4).

### 3.4 — Seed superadmin: promosi otomatis & hash password kosong (Sedang)
- `admin/login.php` (±baris 7–22) `ensure_super_admin_seed()`: bila tidak ada akun `super_admin`, ia mempromosikan user bernama `SUPER_ADMIN_USERNAME` menjadi superadmin, atau membuat akun baru dengan `password_hash(SUPER_ADMIN_PASSWORD ?? '', PASSWORD_DEFAULT)` — **bila env `SUPER_ADMIN_PASSWORD` kosong, akun dibuat dengan hash dari string kosong**.
- Fungsi ini berjalan di **setiap POST login admin**. Efek samping: bila akun superadmin terhapus (bisa — lihat 7.3), akun itu akan dibuat ulang otomatis saat ada yang mencoba login admin.
- **Perbaiki:** seed hanya via CLI/migrasi sekali jalan; jangan pernah hash password kosong — tolak bila env tidak diset.

---

## 4. Delete via GET — lihat 2.2

Dikonfirmasi penuh di kode (`admin/manage.php` baris 464–480): hapus lewat `$_GET['del']`, tanpa token, tanpa cek method. **§11.1 dikonfirmasi.** Satu-satunya pengaman adalah: hapus user mensyaratkan `is_super_admin()` (baris 467). Untuk 9 modul lain, admin biasa pun bisa menghapus lewat GET. Tidak ada perlindungan hapus-diri (lihat 7.3).

---

## 5. Validasi Upload

### 5.1 — Validasi server-side SUDAH ADA dan cukup kuat di repo — §11.4 DIBANTAH sebagai risiko server (untuk repo)
- `includes/functions.php: save_uploaded_file()` (baris ±60–120): whitelist ekstensi per modul, blokir double-extension berbahaya (`php/php3/phtml/phar/pl/py/jsp/asp/sh/cgi/exe`), cek MIME aktual dengan `finfo`, batas ukuran (default 50 MB), nama file acak `bin2hex(random_bytes(16))` (128-bit, bukan dari nama asli), dan `save_uploaded_files()` yang atomik dengan rollback.
- Konfigurasi per modul di `admin/manage.php`: materi PDF saja 50 MB; logo departemen/kabinet/leaders/pengurus gambar 5–10 MB; foto 20 MB; input file memakai atribut `accept`.
- **Koreksi terhadap §11.4:** "tidak ada batasan jenis/ukuran yang terlihat" benar **di UI** (tidak ada teks batas di form), tapi validasi server-nya ada di repo. Bila produksi menjalankan kode repo, risiko upload file berbahaya sudah tertutup. Bila produksi menjalankan kode lama, risiko itu nyata — ini lagi-lagi soal drift (§0).
- **Perbaiki:** tampilkan batas jenis/ukuran di form (teks kecil di bawah input file) agar ekspektasi pengguna jelas.

### 5.2 — Celah SVG: stored XSS lewat logo (Sedang)
- Whitelist logo mengizinkan `svg`, dan cek MIME-nya `image/svg+xml`. File SVG boleh berisi `<script>` — dan SVG yang dibuka langsung akan mengeksekusi script dalam origin situs (stored XSS) bila disajikan dengan `Content-Type: image/svg+xml`.
- **Perbaiki:** hapus `svg` dari whitelist upload, atau sanitasi SVG (hapus `<script>`, event handler, `foreignObject`) sebelum disimpan, atau sajikan SVG dengan `Content-Security-Policy: sandbox` / `Content-Disposition: attachment`.

### 5.3 — Tidak ada `.htaccess` di `uploads/` (Rendah — defense in depth)
- Tidak ada penolakan eksekusi script di dalam `uploads/` (mis. `php_flag engine off` / `deny from all` untuk `*.php`). Bila suatu saat ada celah upload yang lolos, file PHP di `uploads/` bisa dieksekusi.
- **Perbaiki:** tambahkan `uploads/.htaccess` yang menolak eksekusi PHP dan membatasi akses langsung ke file non-publik.

### 5.4 — Nama file TIDAK dapat ditebak — §12.4 sebagian DIBANTAH
- `save_uploaded_file()` memakai `bin2hex(random_bytes(16))` — 128-bit acak, **bukan MD5 dari sesuatu yang bisa ditebak**. Klaim black-box "nama file hash MD5 yang dapat ditebak" **tidak didukung kode repo**. (Catatan: file-file yang ada di `uploads/` repo memakai format lain — `YYYYMMDD_HHMMSS-<6hex>-<nama-asli>` — bukti tambahan bahwa uploader produksi berbeda dari kode repo.)
- **Yang tetap benar dari §12.4:** file materi bisa diakses langsung lewat URL `uploads/materials/...` **tanpa otentikasi sama sekali** — dan flag `is_public` tidak ditegakkan (lihat 7.6). Itulah masalah sebenarnya, bukan ketertebakan nama file.

---

## 6. SQL Injection

### 6.1 — TIDAK ditemukan injeksi SQL di kode yang dibaca (Info — kabar baik)
- Semua query memakai prepared statement PDO (`database/db.php`: `ERRMODE_EXCEPTION`, `EMULATE_PREPARES=false`): login admin/user, `manage.php` (INSERT/UPDATE/DELETE/select by id), impor CSV, `materi.php` (filter kategori + `LIMIT ? OFFSET ?`), `department_helpers.php`, reset password.
- `ORDER BY` di `manage.php` melewati whitelist `validate_order_by()` — bukan input mentah.
- Satu-satunya interpolasi string ke SQL adalah nama tabel/kolom dari **konfigurasi internal** (`$table`, `$order`, `$selectCols` di `materi.php`) — bukan dari input pengguna.
- Filter kategori `materi.php` memakai whitelist `['kuliah','praktikum','laporan_kp','laporan_ta']`.
- **Perbaiki (minor):** tetap lakukan review berkala; satu pola berisiko adalah `SHOW COLUMNS`/`DESCRIBE` di `materi.php` (`materials_has_category_col()`) — aman karena tanpa input pengguna, tapi sebaiknya di-cache agar tidak menambah query di setiap load.

---

## 7. Auth & Otorisasi

### 7.1 — Setiap halaman admin dijaga `require_admin()` (kabar baik, dengan 2 celah)
- `admin/index.php`, `admin/manage.php`, `admin/maintenance.php`, `admin/tools/import_members.php` semuanya memanggil `require_admin()` (atau `require_super_admin()`) di awal. Tidak ditemukan halaman admin tanpa penjaga login.
- **Celah 1 — lihat 7.2.** **Celah 2:** `reset_password.php` (lihat 1.2) tidak memakai penjaga apa pun selain `?confirm=YES`.

### 7.2 — Impor anggota & maintenance: hanya `require_admin()`, BUKAN superadmin — menu saja yang menyembunyikan (Tinggi)
- `admin/tools/import_members.php` baris 3: `require_admin();` — **tidak ada `require_super_admin()`**. Admin biasa (admin_1/admin_2) bisa membuka URL ini langsung dan mengimpor ratusan akun, padahal menu "Impor Anggota" hanya tampil untuk superadmin. Ini eskalasi privilege nyata.
- `admin/maintenance.php`: memakai `require_admin()` saja — admin biasa bisa mengubah maintenance mode lewat URL langsung, padahal menu-nya disembunyikan dari mereka. (**Terkait §7.11**: implementasi maintenance ada, tapi kontrol aksesnya lemah.)
- **Terkait §12.6** (inkonsistensi menu): dikonfirmasi — sidebar di `import_members.php` hanya berisi 8 tautan dan berbeda dari sidebar `manage.php`/`index.php`. Menu yang berbeda-beda ini adalah gejala dari tidak adanya satu sumber otorisasi.
- **Perbaiki:** ganti `require_admin()` menjadi `require_super_admin()` di kedua file. Prinsipnya: jangan pernah mengandalkan menu untuk keamanan — setiap halaman harus memeriksa peran di server.

### 7.3 — Tidak ada perlindungan hapus-diri / turun-peran superadmin — §12.2 DIKONFIRMASI (Tinggi)
- `admin/manage.php` baris 464–480 (handler delete): satu-satunya cek adalah `$moduleKey === 'users' && !is_super_admin()` — **tidak ada cek apakah `$delId` adalah akun si penghapus sendiri**. Superadmin bisa menghapus akunnya sendiri (barisnya memang punya tombol Hapus — sesuai observasi §12.2).
- Konfigurasi modul users di `manage.php`: `'role' => ['label'=>'Role','type'=>'select','options'=>['user','admin'],'required'=>true]` — **tidak ada opsi `super_admin`** (konfirmasi §12.2). Mengedit superadmin menampilkan dropdown tanpa pilihan terpilih; bila disimpan dengan memilih `admin`, superadmin turun peran secara diam-diam. (Catatan: bila dikirim kosong, validasi `required` menolak — tapi pilihan `admin` tetap bisa dipilih.)
- Handler POST `manage.php` memang menolak admin biasa mengubah `role` ("Hanya Super Admin yang boleh mengubah peran pengguna") — bagus — tapi tidak melindungi superadmin dari dirinya sendiri.
- **Perbaiki:** tolak penghapusan bila `$delId === $_SESSION['user']['id']`; tolak perubahan role bila target adalah diri sendiri / bila role baru bukan `super_admin` untuk akun superadmin; tambahkan `super_admin` ke opsi dropdown (atau sembunyikan field role saat mengedit superadmin).

### 7.4 — Tidak ada fitur ganti password sendiri — §11.5 DIKONFIRMASI (Sedang)
- Modul users di `manage.php` punya field password (superadmin bisa mengganti password siapa pun), tetapi **tidak ada halaman "ganti password saya"** untuk admin_1/admin_2. Mereka tidak bisa mengganti passwordnya sendiri.
- **Perbaiki:** tambahkan halaman ganti password mandiri (password lama + baru, dengan `password_verify`).

### 7.5 — Open redirect lewat parameter `next` (Rendah–Sedang)
- `admin/login.php` (±baris 69): `header('Location: ' . ($next ?: BASE_URL . 'admin/'));` — `$next` dari `$_POST['next']` (yang diisi dari `$_GET['next']`) **tidak divalidasi**. Tautan `admin/login.php?next=https://situs-jahat` akan mengarahkan korban ke situs jahat setelah login (umpan phishing).
- `pages/login.php` (±baris 25): pola sama, `header('Location: ' . ($next ?: BASE_URL));` — lebih parah karena login user **tanpa CSRF** (2.6).
- **Perbaiki:** izinkan hanya path relatif internal (tolak URL absolut / yang diawali `//`), mis. `if (!str_starts_with($next, '/') || str_starts_with($next, '//')) $next = '';`.

### 7.6 — Flag `is_public` materi TIDAK ditegakkan — materi "Private" bisa diakses semua user login (Tinggi)
- `pages/materi.php`: query-nya `SELECT ... FROM materials ORDER BY id DESC LIMIT ? OFFSET ?` — **tanpa filter `WHERE is_public=1`**. Materi dengan `is_public=0` tetap tampil di daftar (hanya diberi badge "Private") dan tombol Lihat/Download-nya aktif untuk **semua** user yang login (904 akun). `Resource/js/materi.js` (`viewPDF`/`downloadPDF`) membuka URL file langsung.
- **Terkait §12.4** (tautan materi langsung): dikonfirmasi dan diperluas — bukan hanya "bisa ditebak", tapi memang tidak ada kontrol akses sama sekali, bahkan untuk materi yang ditandai Private. File PDF-nya sendiri juga bisa dibuka siapa pun tanpa login (URL langsung).
- **Perbaiki:** filter `WHERE is_public=1 OR <user berhak>` di query; sajikan file lewat skrip PHP yang memeriksa login + `is_public` (bukan tautan langsung ke `uploads/`), atau pindahkan file private ke luar web root.

### 7.7 — Label peran tidak konsisten — §11.6 & §12.6 DIKONFIRMASI (Rendah)
- `admin/index.php` (topbar): hanya menampilkan username + status maintenance — **tanpa peran**.
- `admin/manage.php`: peran tampil sebagai nilai mentah di tabel (`admin`/`super_admin`), tanpa label.
- `admin/tools/import_members.php` (topbar): `is_super_admin()?'Super Admin':'Admin'` — satu-satunya tempat yang menampilkan label peran, dengan format berbeda.
- **Perbaiki:** tampilkan peran secara konsisten di topbar semua halaman admin (mis. lencana "Super Admin"/"Admin").

---

## 8. Session

### 8.1 — Pengaturan cookie sesi di repo SUDAH aman — tapi TIDAK BERLAKU di live (drift, §0)
- `database/config.php`: `session.cookie_httponly=1`, `session.cookie_samesite='Strict'`, `session.use_strict_mode=1`, `session.cookie_secure=1` saat HTTPS, `session.cookie_lifetime=0`.
- **Tapi cookie live tanpa HttpOnly/SameSite (§3.2)** — bukti produksi tidak menjalankan file ini. Di repo, pengaturannya sudah benar; tidak ada perbaikan kode yang dibutuhkan di sini, hanya deployment.
- `admin/login.php`: `session_regenerate_id(true)` + `csrf_regenerate()` setelah login sukses — benar (anti session fixation).
- `pages/login.php`: **tidak ada** `session_regenerate_id()` setelah login user — session fixation dimungkinkan untuk akun user. **Perbaiki:** tambahkan `session_regenerate_id(true)` di `pages/login.php` seperti di login admin.
- `admin/logout.php`: `$_SESSION = []; session_destroy();` — benar secara garis besar, tapi **tidak menghapus cookie sesi** (`setcookie(session_name(), '', time()-3600, ...)`) dan tanpa CSRF (logout CSRF — risiko rendah, tapi sebaiknya logout memakai POST + token).
- **Terkait §3.2** (cookie tanpa HttpOnly/SameSite): dikonfirmasi di live, dibantah di kode repo — lagi-lagi drift.

---

## 9. Login Brute Force

### 9.1 — Rate limiting SUDAH ADA di repo untuk login admin — §11.2 sebagian DIBANTAH (untuk repo)
- `includes/rate_limiter.php`: 5 percobaan gagal / 900 detik per IP + aksi, memakai tabel `login_attempts` (migrasi `database/migrations/001_login_attempts.sql`), **fail-open** (bila DB error, login tetap diizinkan — dicatat sebagai keputusan desain).
- `admin/login.php`: `sleep(1)` di setiap POST + `rate_limit_is_blocked()` + `rate_limit_record_attempt()` + `rate_limit_reset()` saat sukses + pencatatan `log_security('login_failed', ...)`.
- **Tapi live tidak menunjukkan rate limit (§11.2)** — drift (§0). Tidak ada CAPTCHA di repo maupun live (bukan keharusan bila rate limit berjalan).
- **Perbaiki:** (a) deploy kode repo; (b) pertimbangkan fail-closed untuk login admin (atau minimal beri peringatan bila rate limiter tidak aktif); (c) pastikan migrasi 001 sudah dijalankan di produksi — tanpa tabel `login_attempts`, rate limiter tidak bisa mencatat (dan fail-open berarti tidak ada perlindungan sama sekali).

### 9.2 — Login user TANPA rate limit (Sedang)
- `pages/login.php`: tidak memanggil rate limiter sama sekali — 904 akun user bisa di-brute-force tanpa batas.
- **Perbaiki:** pakai `rate_limit_*` yang sama dengan aksi `user_login`.

---

## 10. Konfirmasi Temuan Black-Box Lainnya (dari kode)

### 10.1 — `display_errors` — §3.5 DIKONFIRMASI sebagian, dengan koreksi akar masalah
- `config/config.php` (legacy): `ini_set('display_errors', 1); error_reporting(E_ALL);` **tanpa syarat** — ini cocok dengan warning yang tampil di live.
- `database/config.php`: `display_errors=0` di production (benar).
- **Status di repo tidak jelas file mana yang dipakai**: tidak ada file repo yang me-require `config/config.php` (file ini tampak mati di repo — lihat §5 Efektivitas), sementara semua entry point memakai `includes/functions.php` → `database/config.php`. Bila produksi memuat `config/config.php` (atau `.env`-nya `APP_ENV=development`), warning akan tampil.
- **Perbaiki:** hapus `config/config.php` (legacy) dan pastikan hanya `database/config.php` yang dipakai; pastikan `.env` produksi `APP_ENV=production` dan `APP_DEBUG=false`.

### 10.2 — Path include footer Minerfesto SALAH — §3.5 DIKONFIRMASI, dengan koreksi perbaikannya
- `pages/annual/minerfesto.php` baris 356 (baris terakhir):
  ```php
  <?php include dirname(__DIR__, 2) . '/../includes/footer.php'; ?>
  ```
  `dirname(__DIR__, 2)` sudah = root repo, sehingga `'/../includes/footer.php'` menunjuk ke **parent root** — persis path di warning live (`public_html/../includes/footer.php`). **Koreksi terhadap rekomendasi black-box:** yang benar bukan mengganti menjadi `../../includes/footer.php` (itu untuk include relatif), melainkan **menghapus `/..`**: `dirname(__DIR__, 2) . '/includes/footer.php'`. (Baris 2 file yang sama sudah benar: `require_once dirname(__DIR__, 2) . '/includes/functions.php';`.)
- **Perbaiki:** hapus segmen `/..` pada baris 356.

### 10.3 — Email footer salah — §7.1 DIKONFIRMASI, dan ternyata ada TIGA alamat berbeda
- `includes/footer.php` (fallback): `$contact_email = 'hmta.balakosa.itera.com'` — tanpa `@` (salah).
- `config/config.php`: `CONTACT_EMAIL = 'hmtaitera@gmail.com'` — alamat lain, juga bukan alamat resmi.
- Alamat benar: `hmta.balakosa.itera@gmail.com`.
- Akar masalah: tidak ada satu sumber kebenaran — `database/config.php` **tidak mendefinisikan** `CONTACT_EMAIL`, sehingga fallback footer yang dipakai.
- Typo LinkedIn `pertambagan` ada di `config/config.php` (LINKEDIN_URL) **dan** di fallback footer — konsisten dengan §7.1.
- Telepon placeholder `0821-XXXX-XXXX` ada di `config/config.php` (`CONTACT_PHONE`) dan di `minerfesto.php` (kontak panitia + `wa.me/6282100000000`) — §7.4 dikonfirmasi.
- **Perbaiki:** definisikan `CONTACT_EMAIL`, `CONTACT_PHONE`, dan URL sosial **sekali** di `.env`/`database/config.php`; hapus semua fallback hardcode; perbaiki typo `pertambagan` → `pertambangan`.

### 10.4 — URL internal `http://` — §3.3/§8 DIKONFIRMASI akar masalahnya
- Kedua file config menghitung `BASE_URL` hanya dari `$_SERVER['HTTPS']`, **tanpa memeriksa `X-Forwarded-Proto`** — di Hostinger (di balik proxy), HTTPS tidak terdeteksi sehingga semua URL internal jadi `http://`.
- **Perbaiki:** hormati header `X-Forwarded-Proto: https` (atau paksa `BASE_URL` ke `https://hmtaitera.com` di `.env`).

### 10.5 — Konten kedaluwarsa hardcode — §7.4, §7.9, §7.10 DIKONFIRMASI di kode
- `pages/annual/minerfesto.php`: pendaftaran "Minerfesto 2025" (15–20 Sep 2025, pendaftaran 1 Jun–31 Agu 2025, tombol "Daftar Sekarang" `href="#"`), timeline 2022 "Minerfesto I" / 2023 "Minerfesto VI" / 2024 "Minerfesto VII" (loncatan penomoran I→VI), galeri 2022–2024, sponsor `sponsor1–6.png` (file tidak ada di repo).
- `index.php`: banner 2 hardcode "Kabinet Samagri 2024-2025"; counter hardcode `data-target="200"` (Anggota Aktif), 50, 25, 5; tanpa H1.
- `pages/struktur.php`: kabinet aktif dipilih dengan mencocokkan substring tahun berjalan di kolom `period` lewat PHP — rapuh bila ada periode kabinet duplikat (§11.9).
- **Perbaiki:** pindahkan konten tahunan (nama kabinet aktif, tanggal pendaftaran, counter) ke database/panel admin; jangan hardcode tahun di template.

### 10.6 — Aset 404: sebagian memang tidak ada di repo — §8.1 DIKONFIRMASI & diperjelas
- File yang direferensikan tapi **tidak ada di repo**: `Resource/minerfesto-gallery1–6.jpg`, `Resource/minerfesto-about.jpg`, `Resource/minerfesto-registration.jpg`, `Resource/sponsor1–6.png`, `Resource/default.jpg`, `Resource/default.png`, `Resource/default-profile.jpg`, `Resource/hmta-group.jpg`, `Resource/img/lustrum.jpg`, `Resource/img/minerfesto.jpg`.
- Artinya sebagian 404 **bukan** semata-mata kehilangan folder saat migrasi — aset-aset itu memang tidak pernah masuk repo. (Folder `uploads/` di repo berisi file 14 Nov–6 Des 2025, sedangkan file 404 di live bertanggal 12–19 Des 2025 — DB dan filesystem tidak sinkron.)
- `pages/department.php` (±baris 40): logo departemen di-hardcode ke `Resource/img/banner1.png` untuk **semua** departemen, mengabaikan kolom `logo_url` di DB — menjelaskan kenapa semua halaman departemen menampilkan gambar yang sama (§8.1).
- **Perbaiki:** commit aset yang direferensikan atau hapus referensinya; pakai `logo_url` dari DB di `department.php`; sinkronkan `uploads/` dengan DB (atau buat skrip rekonsiliasi).

### 10.7 — Maintenance mode — §7.11 DIKONFIRMASI implementasinya
- `config/maintenance.json` + `get_maintenance_status()`/`set_maintenance_status()` + overlay di `includes/header.php` — implementasi sudah ada dan rapi (termasuk dukungan wildcard).
- Daftar halaman di `admin/maintenance.php` berisi 11 entri, sedangkan live menampilkan ±19 — kemungkinan drift (daftar di produksi lebih baru) atau perbedaan cara hitung.
- **Catatan koreksi:** "Minerfesto" di daftar maintenance **bukan typo** — itu nama acara yang konsisten di seluruh kode (`minerfesto.php`, judul halaman, dsb.). Demikian pula `samagri.php` adalah nama file, bukan typo.
- Kelemahan aksesnya lihat 7.2 (admin biasa bisa mengubah via URL langsung) dan 2.3 (toggle dashboard tanpa CSRF).

### 10.8 — X-Powered-By & header keamanan — §3.2 (kode tidak menanganinya)
- Tidak ada `expose_php = Off` di `.htaccess` dan `send_security_headers()` tidak menghapus `X-Powered-By`. Header keamanan hanya dikirim dari PHP (`database/config.php`) — bila bootstrap itu tidak berjalan di produksi, tidak ada header sama sekali.
- **Perbaiki:** tambahkan `php_flag expose_php Off` (bila diizinkan host) atau `Header unset X-Powered-By` di `.htaccess` sebagai jaring pengaman.

---

## 11. XSS (Cross-Site Scripting)

### 11.1 — Output di-escape dengan konsisten di template yang dibaca (kabar baik)
- `admin/manage.php`: semua nilai sel memakai `htmlspecialchars((string)$v)`; password ditampilkan sebagai `••••••`; URL file juga di-escape.
- `pages/department.php`, `pages/materi.php`, `admin/tools/import_members.php`: memakai helper `h()` / `htmlspecialchars` untuk nama, deskripsi, judul, dan kategori.
- Tidak ditemukan echo input pengguna tanpa escape di file-file yang dibaca (login, manage, materi, department, import, forgot/reset).
- **Catatan:** `pages/materi.php` memakai `htmlspecialchars($m['file_url'], ENT_QUOTES)` di dalam atribut `onclick` — benar, tapi pola ini rapuh; lebih aman memakai `data-*` attribute + `addEventListener`.

### 11.2 — Stored XSS lewat SVG (Sedang) — lihat 5.2
- Satu-satunya jalur stored XSS yang ditemukan: upload SVG berisi script (logo departemen/kabinet/leaders/pengurus), karena SVG disajikan langsung dari `uploads/`.

---

## 12. Temuan Lainnya

### 12.1 — File debug/test ikut ter-deploy (Rendah–Info)
- `debug.php`: terkunci `APP_ENV === 'development'` (mengembalikan 404 di production) **dan** diblokir `.htaccess` (`^debug.*\.php$`) — perlindungan ganda, bagus. Tetap sebaiknya dihapus dari produksi.
- `test_view.php`: file test yang me-require `functions.php` + `header.php`/`footer.php`, bisa diakses publik tanpa guna — hapus dari produksi.
- `.phpunit.result.cache` ikut ter-commit ke repo — tambahkan ke `.gitignore`.

### 12.2 — `error.php`: halaman error generik yang baik (Info)
- Tidak menampilkan detail teknis — sudah benar. Pastikan server benar-benar mengarah ke halaman ini untuk 403/404/500 (bukan menampilkan error PHP mentah).

### 12.3 — Duplikasi sistem & skema tidak lengkap (Sedang — dampak maintainability, detail di §5)
- `database/schema.sql` (81 baris) **tidak lengkap**: tidak ada tabel `members`, `leaders`, `programs`, `organization_leaders`, `password_resets`, `login_attempts`; `users.role` ENUM hanya `('admin','user')` (baris 6) — tanpa `super_admin`, padahal seluruh kode mengasumsinya. Deploy dari nol memakai file ini akan gagal/tidak konsisten.
- Migrasi `database/migrations/002_add_indexes.sql` mereferensikan kolom `events.active` yang **tidak ada** di `schema.sql` — migrasi akan gagal di database baru.
- Dua sistem koneksi DB: class `Database` legacy di `database/config.php` ("dipertahankan untuk kompatibilitas" menurut `DATABASE_AUDIT_REPORT.md`) vs `db()` di `database/db.php`.
- **Perbaiki:** lengkapi `schema.sql` (atau buat migrasi konsolidasi), tambahkan `super_admin` ke ENUM, perbaiki 002, hapus class legacy bila tidak dipakai.

### 12.4 — Positif yang patut dipertahankan
- Prepared statements konsisten; `htmlspecialchars` konsisten; `try-catch` PDO dengan `error_log` (bukan echo ke browser); flash message; whitelist `ORDER BY`; logger keamanan (`includes/logger.php`, rotasi 10 MB, format NDJSON); file cache (`includes/cache.php`); PHPUnit tests untuk helper keamanan (`tests/CsrfTest.php`, `RateLimiterTest.php`, `UploadHandlerTest.php`, `OrderByTest.php`, `CacheTest.php`, `MultiUploadHelperTest.php`); `DATABASE_AUDIT_REPORT.md` mendokumentasikan refactor April 2026; `.htaccess` memblokir dotfile & file debug, gzip, dan cache browser 1 tahun untuk aset statis.

---

## 13. Efektivitas Kode

Penilaian ini menjawab permintaan pemilik situs: apakah kodenya **efektif** — bukan hanya aman, tapi apakah ia bekerja efisien, mudah dibaca, dan mudah dipelihara. Bukti diambil dari kode yang dibaca pada audit ini.

### 13.1 Efisiensi — query boros, N+1, tanpa pagination, aset besar

**Yang boros (bukti kode):**
- **Admin memuat SEMUA baris tanpa pagination.** `admin/manage.php` baris 483:
  ```php
  $list = db()->query("SELECT * FROM $table ORDER BY $order")->fetchAll();
  ```
  Tanpa `LIMIT` — 903 anggota / 908 pengguna dimuat sekaligus dalam satu halaman (§11.3/§12.5 dikonfirmasi). Ironisnya, halaman publik `pages/materi.php` **sudah** memakai pagination (`const MATERI_PER_PAGE = 20`, lengkap dengan nomor halaman + ellipsis) — pola yang benar sudah ada di repo, hanya tidak dipakai di admin.
- **N+1 query di halaman departemen.** `pages/department.php` (±baris 105) memanggil `get_division_members((int)$dv['id'])` **di dalam loop** `foreach ($divisions as $dv)` — satu query per divisi, ditambah 4 query awal (`get_department_by_slug`, `get_department_leaders`, `get_divisions`, `get_programs`). Departemen dengan 6 divisi = 10 query, dan semuanya tanpa cache.
- **Lapisan cache ada tapi mati.** `includes/cache.php` (cache file JSON, TTL 1 jam) **tidak pernah di-require** — `includes/functions.php` hanya me-require `database/config.php` dan `database/db.php`. Satu-satunya pemakai, `get_upcoming_events()`, membungkusnya dengan `function_exists('cache_get')`, sehingga cache diam-diam tidak aktif dan homepage meng-query tabel `events` di setiap load. Ini menjelaskan sebagian TTFB live 1,0–1,4 detik (§10).
- **Aset besar tanpa optimasi.** `Resource/img/IMG_20230226_165207.jpg` 3,2 MB; favicon `IMG_1381.png` 372 KB; `banner1.png` di-preload di homepage; tidak ada `srcset`/gambar responsif di template (ukuran `width`/`height` ditulis tetap). `.htaccess` sudah mengaktifkan gzip + cache browser 1 tahun (positif), tapi itu tidak mengecilkan file sumbernya.
- **Query defensif yang boros.** `pages/materi.php` menjalankan `SHOW COLUMNS FROM materials LIKE 'category'` (plus fallback `DESCRIBE materials`) di **setiap load** untuk mengecek keberadaan kolom — seharusnya cukup sekali saat deploy, bukan per request.

**Yang efisien (bukti kode):**
- `materi.php`: pagination + filter kategori ber-whitelist + `LIMIT ? OFFSET ?` terikat parameter — contoh pola yang benar.
- `get_upcoming_events()`: `LIMIT` terikat sebagai integer + niat cache 15 menit (sayang tidak aktif).
- `admin/tools/import_members.php`: memakai transaksi DB (`beginTransaction`/`commit`/`rollBack`) untuk impor massal — benar.
- `.htaccess`: gzip untuk teks/CSS/JS, `Expires`/`Cache-Control` 1 tahun untuk aset statis, `no-store` untuk admin.

**Nilai efisiensi: 5/10** — pola benar sudah ada di beberapa tempat, tapi halaman admin dan departemen masih boros, dan cache yang sudah ditulis tidak pernah diaktifkan.

### 13.2 Struktur & keterbacaan — organisasi, duplikasi, dead code

**Yang terorganisir baik:**
- `admin/manage.php` adalah **CRUD generik berbasis konfigurasi**: 10 modul (pengurus, leaders, programs, departemen, divisi, kabinet, anggota, foto, materi, kegiatan) didefinisikan sebagai array konfigurasi (field, tipe, validasi, upload) dan dilayani satu handler. Ini reuse yang nyata — menambah modul baru tidak butuh file baru.
- Helper terpisah per tanggung jawab: `csrf.php`, `rate_limiter.php`, `logger.php`, `cache.php`, `security_headers.php`, `password_reset.php`, `department_helpers.php`, `organization_helpers.php`.
- Ada PHPUnit test untuk helper keamanan (`tests/` — 6 file test + `bootstrap.php`) dan ada `DATABASE_AUDIT_REPORT.md` yang mendokumentasikan refactor.
- Penamaan file/fungsi konsisten berbahasa Indonesia, komentar menjelaskan alasan ("JANGAN tambah index.js", "Cegah double extension berbahaya").

**Duplikasi besar-besaran (bukti kode):**
- **Tiga sidebar admin yang berbeda**: `admin/index.php`, `admin/manage.php`, dan `admin/tools/import_members.php` masing-masing menulis ulang seluruh HTML sidebar — dengan daftar tautan yang **tidak sama** (impor hanya 8 tautan; manage/index lengkap). Mengubah satu tautan berarti mengedit 3 file, dan hasilnya sudah tidak sinkron (§12.6).
- **Helper `h()` didefinisikan ulang di tiap file**: `pages/department.php`, `admin/tools/import_members.php` (masing-masing `function h($s){ return htmlspecialchars(...); }`).
- **CSS inline ~100 baris diduplikasi** di `admin/forgot.php`, `admin/reset.php`, `pages/login.php`, `admin/login.php` (kartu tengah, tombol gradien, `.error` — sama persis polanya).
- **Dua sistem konfigurasi** (`config/config.php` vs `database/config.php`) dengan nilai yang **bertentangan** (email kontak berbeda, `display_errors` berbeda) — lihat 10.1/10.3.
- **Dua sistem koneksi DB** (class `Database` legacy vs `db()`).
- **Fallback kontak diduplikasi** di `includes/footer.php` dan `config/config.php` — dengan nilai berbeda.

**Dead code / file tak terpakai (bukti kode):**
- `config/config.php` — tidak ada satu pun file repo yang me-require-nya (semua entry point memakai `includes/functions.php`). Di repo ia mati; di produksi ia diduga justru yang berjalan (§0).
- `includes/cache.php` — tidak pernah di-require (lihat 13.1).
- `reset_password.php` — skrip darurat dengan path require rusak + password hardcoded (lihat 1.2).
- `test_view.php`, `debug.php` — file test/dev yang ikut ter-deploy.
- `database/config.php` class `Database` — legacy, "dipertahankan untuk kompatibilitas".
- `database/schema.sql` tidak lengkap (lihat 12.3); `.phpunit.result.cache` ter-commit; `vendor/` (PHPUnit, dependency dev) ikut di repo.
- Aset mati: `Resource/img/banner2.png`/`banner3.png` dan banyak file `Resource/img/WhatsApp Image*` yang tidak jelas pemakaiannya; sebaliknya aset yang **dipakai** (galeri Minerfesto, sponsor, `default.jpg`) tidak ada di repo (lihat 10.6).

**Nilai struktur/keterbacaan: 5/10** — kerangka helper dan CRUD generiknya bagus, tapi duplikasi sidebar/CSS/config dan tumpukan dead code membuat codebase sulit dibaca dan rawan tidak sinkron.

### 13.3 Maintainability — seberapa mudah diperbaiki/dikembangkan

**Penghambat utama (bukti kode):**
- **Hardcode di mana-mana.** Tahun dan nama kabinet di template (`index.php`: "Kabinet Samagri 2024-2025"; `minerfesto.php`: "Minerfesto 2025" + tanggal 15–20 Sep 2025); counter `data-target="200/50/25/5"` di `index.php`; telepon placeholder `0821-XXXX-XXXX` di 2 tempat; **tiga alamat email berbeda** untuk satu alamat kontak (10.3); logo departemen di-hardcode ke `banner1.png` di `department.php` padahal DB punya kolom `logo_url`; `medkom-init.js` dimuat di **semua** halaman departemen padahal spesifik Medkominfo.
- **Tidak ada pemisahan logika dan tampilan.** Setiap halaman adalah skrip PHP mandiri yang mencampur query DB, logika, dan HTML. Tidak ada router, template engine, atau framework — mengubah layout berarti menyentuh belasan file.
- **Tidak ada seed/migrasi yang bisa diandalkan.** `schema.sql` tidak lengkap (12.3), migrasi 002 gagal di DB baru (kolom `events.active` tidak ada), dan tidak ada runner migrasi — file migrasi hanya berkomentar "jalankan sekali saat deployment". Deploy dari nol tidak bisa direproduksi dari repo ini.
- **Konfigurasi tersebar di 3 tempat** (`.env`, `database/config.php`, `config/config.php`) plus fallback hardcode di `footer.php` — mengubah satu nilai (mis. email) harus diingat di banyak tempat, dan hasilnya sudah terbukti tidak sinkron.
- **Tidak ada pemisahan peran yang konsisten** (lihat §7): otorisasi superadmin tersebar sebagai `if (is_super_admin())` di template, bukan satu middleware/guard.

**Penolong (bukti kode):**
- CRUD generik `manage.php` membuat penambahan modul murah.
- Prepared statement + escaping konsisten → perubahan query relatif aman.
- Test PHPUnit untuk helper keamanan memberi jaring pengaman saat refactor helper.
- `DATABASE_AUDIT_REPORT.md` + komentar kode yang menjelaskan alasan keputusan.
- Flash message + `error_log` (bukan echo) membuat debugging produksi lebih tertib.

**Nilai maintainability: 4/10** — mengubah **konten** (tahun, kabinet, email) masih harus lewat edit kode; mengubah **struktur** (tambah modul) relatif mudah berkat CRUD generik; tapi deploy dari nol tidak dapat direproduksi dan konfigurasi tersebar membuat perbaikan kecil berisiko salah tempat.

### 13.4 Verdict keseluruhan: **SEBAGIAN EFEKTIF**

Kode ini **bukan** kode yang buruk — fondasinya (prepared statement, escaping, helper keamanan, CRUD generik, test, logger, cache) berada di atas rata-rata situs PHP himpunan mahasiswa. Tetapi ia **tidak efektif** dalam arti yang diminta pemilik situs, karena:

1. **Pekerjaan hardening-nya tidak sampai ke pengguna.** Lapisan keamanan terbaik di repo (CSRF, rate limit, security headers, session aman) tidak berjalan di produksi (§0). Kode yang tidak di-deploy bernilai nol.
2. **Pola boros masih ada di jalur yang paling sering dipakai** (admin tanpa pagination, N+1 di halaman departemen, cache mati) — halaman lambat bukan karena server, tapi karena query.
3. **Duplikasi membuat perubahan kecil berisiko** — email saja sudah berbeda di 3 tempat; sidebar sudah tidak sinkron di 3 file.
4. **Konten masih menempel di kode** — pergantian tahun/kabinet butuh edit template, bukan edit data.
5. **Repo tidak bisa menjadi sumber kebenaran deploy** — skema tidak lengkap, migrasi rusak, aset hilang, `uploads/` tidak sinkron dengan DB.

Singkatnya: **arsitekturnya cukup baik, eksekusi dan deployment-nya tidak.** Dengan 3–5 perbaikan di bawah, kode ini bisa naik dari "sebagian efektif" menjadi "efektif".

### 13.5 Rekomendasi paling berdampak (urut prioritas)

1. **Samakan produksi dengan repo — satu sumber kebenaran, lalu kunci.** Deploy kode repo (atau commit kode produksi ke repo), hapus `config/config.php` legacy, dan pastikan `.env` produksi `APP_ENV=production`. Satu langkah ini menutup CSRF login, rate limit, security headers, cookie aman, dan `display_errors` sekaligus — dampak keamanan terbesar dengan usaha terkecil. Setelah itu, larang deploy manual via FTP tanpa commit.
2. **Hapus `reset_password.php` dan perbaiki `forgot.php`.** Hapus skrip reset darurat dari repo + produksi; ganti password superadmin; buat reset password mengirim token via email (atau nonaktifkan sampai ada email). Ini menutup dua celah takeover akun.
3. **Perbaiki jalur yang boros: pagination admin + hapus N+1 + aktifkan cache.** Tambahkan `LIMIT/OFFSET` di `admin/manage.php` baris 483 (contoh sudah ada di `materi.php`); gabungkan `get_division_members` menjadi satu query `WHERE division_id IN (...)` di `department.php`; tambahkan `require_once includes/cache.php` di `includes/functions.php` agar cache events aktif. Dampak langsung ke kecepatan halaman admin & departemen.
4. **Satukan konfigurasi dan lengkapi skema.** Satu sumber: `.env` + `database/config.php` saja. Lengkapi `schema.sql` (tambah `members`, `leaders`, `programs`, `password_resets`, `login_attempts`; ENUM role tambah `super_admin`), perbaiki migrasi 002, dan buat satu perintah untuk menjalankan migrasi. Tanpa ini, repo tidak bisa dipakai untuk deploy ulang atau onboarding pengembang baru.
5. **Bersihkan dead code & aset, kecilkan gambar.** Hapus `test_view.php`, `debug.php`, class `Database` legacy, dan `.phpunit.result.cache` dari repo; commit aset yang direferensikan (atau hapus referensinya); kompres favicon 372 KB → <50 KB dan foto 3,2 MB → WebP/terkompres; pakai `logo_url` dari DB di `department.php`. Dampak: repo lebih kecil dan mudah dibaca, halaman lebih ringan, 404 berkurang.

---

## 14. Top 5 Temuan Kode

1. **Kritis — `admin/forgot.php` menampilkan link reset password di layar (§3.1).** Siapa pun yang tahu username admin bisa membuat token reset dan mengambil alih akun — tanpa login, tanpa email. Perlu verifikasi live karena drift (§0).
2. **Tinggi (Kritis bila berfungsi di produksi) — `reset_password.php` berisi password superadmin hardcoded `AdminBaru#2025` (§1.2).** Hanya butuh `?confirm=YES`. Saat ini path require-nya rusak sehingga fatal error di root — tetap harus dihapus segera dari repo dan produksi.
3. **Tinggi — Delete via GET tanpa CSRF di `admin/manage.php` baris 464–480 (§2.2/§4).** Mengonfirmasi §11.1 black-box. Semua 10 modul bisa dihapus lewat tautan; tidak ada perlindungan hapus-diri superadmin.
4. **Tinggi — `admin/tools/import_members.php` hanya `require_admin()` + tanpa CSRF (§2.5/§7.2).** Admin biasa bisa mengimpor akun massal lewat URL langsung; menu hanya menyembunyikan. (Koreksi: password CSV **di-hash** — §12.1 black-box terkoreksi sebagian.)
5. **Tinggi — Drift repo vs produksi (§0).** Seluruh hardening (CSRF login, rate limiter, security headers, session aman, `display_errors=0`, validasi upload) ada di repo tapi tidak ada di live. Sampai deployment disamakan, perbaikan kode apa pun tidak berdampak.

**Kabar baik terbesar dari audit kode:** tidak ada SQL injection di kode yang dibaca; password sudah `password_hash`/`password_verify` (termasuk impor CSV); output di-escape konsisten; dan fondasi helper keamanan + test-nya sudah ada — tinggal di-deploy dan dipakai konsisten.

---

## 15. Keterbatasan Audit

- **Riwayat git tidak diperiksa** (perintah `git log/diff/status` di dalam repo diblokir oleh pemeriksaan risiko eksekusi; hanya pembacaan file biasa yang diizinkan). Tidak dapat dipastikan apakah kredensial pernah ter-commit lalu dihapus. Arsan harus menjalankan `gitleaks`/`trufflehog` sendiri.
- **Tidak ada pengujian live** (subagent ini tidak dapat mengoperasikan browser). Temuan yang bergantung pada perilaku produksi (forgot.php, reset_password.php, CSRF live, rate limit live) ditandai "verifikasi live wajib".
- **Tidak semua file dibaca baris demi baris**: fokus pada file keamanan & jalur utama (`admin/*`, `includes/*`, `config/*`, `database/*`, `pages/login.php`, `pages/materi.php`, `pages/department.php`, `pages/struktur.php`, `pages/annual/minerfesto.php`, `.htaccess`, `composer.json`, `DATABASE_AUDIT_REPORT.md`). File halaman publik lain (`pages/calendar.php`, `pages/gallery.php`, `pages/annual.php`, dsb.) dan file JS/CSS hanya dibaca seperlunya (materi.js).
- **Nomor baris bertanda ± adalah perkiraan** dari pembacaan; nomor baris tanpa ± dihitung langsung dari hasil baca. Cuplikan kode dikutip apa adanya.
- **Tidak ada password admin yang dicantumkan** dalam laporan ini.
