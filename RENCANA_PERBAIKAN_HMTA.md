# Rencana Perbaikan Website HMTA ITERA (hmtaitera.com)

> **Untuk:** AI coding agent (GitHub Agentic / Copilot agent) yang mengerjakan repo `Arsan-joy/Himpunan`.
> **Sumber:** hasil audit black-box + audit kode, 2026-11-21:
> - `audit-hmta-2026-11-21.md` — audit publik + panel admin (39 temuan)
> - `audit-kode-hmta-2026-11-21.md` — audit source code + efektivitas kode (termasuk §16 diagnosis Departemen & Kabinet)
> **Bahasa:** kerjakan dan laporkan dalam Bahasa Indonesia.
> **Prinsip:** satu fase selesai + terverifikasi sebelum lanjut ke fase berikutnya. Jangan menebak isi yang hilang — kalau data/file tidak ada, tulis apa yang dibutuhkan dan berhenti di tugas itu.

---

## 0. Konteks yang wajib dipahami dulu

### 0.1 Drift repo vs produksi (BACA INI DULU)

Repo ini **sudah berisi banyak hardening** (CSRF, rate limiter, security headers, session aman, `display_errors=0`, validasi upload) yang **tidak ada di produksi**. Produksi menjalankan kode lama. Akibatnya:

- Banyak temuan audit "sudah benar di repo" tapi tetap berlaku di live.
- Sebelum mengerjakan fase apa pun, cek apakah tugas itu sudah selesai di repo (cek kodenya, jangan asumsi). Kalau sudah ada di repo, tugasnya berubah menjadi: **pastikan file itu ikut ter-deploy ke produksi**.
- Jangan membuat duplikat perbaikan yang sudah ada di repo dengan nama berbeda.

### 0.2 Yang SUDAH dikerjakan pemilik situs (JANGAN diulang / JANGAN dibuat ulang)

- `reset_password.php` (root) — **dihapus** dari repo DAN produksi (2026-11-21). Jangan dibuat ulang.
- `admin/forgot.php` — **dihapus** dari produksi (2026-11-21). Jangan dibuat ulang. Fitur "Lupa Password Admin" dihapus, bukan diperbaiki (lihat §1.1).
- `hmta.sql` (dump DB di `public_html`) — **dihapus** dari produksi. Jangan menaruh file `.sql` di web root.
- `debug.php` — **dihapus** dari produksi. Jangan dibuat ulang.
- Password superadmin **wajib diganti** oleh pemilik situs sendiri (nilainya pernah ada di repo publik + riwayat git). Ini tugas manual, bukan tugas agent.

### 0.3 Guardrails

- Jangan commit kredensial, password, `.env`, atau dump database ke repo. Semua config lewat `.env` (lihat §5.4).
- Jangan mengubah data isi (nama anggota, prestasi, dsb.) — hanya struktur/kode. Perbaikan DATA (slug, periode kabinet) dilakukan pemilik situs via panel admin / SQL, dan dicantumkan sebagai tugas manual di tiap fase.
- Jangan menghapus fitur yang sedang dipakai (jadwal, materi, galeri) tanpa konfirmasi.
- Setiap perubahan kode harus lolos: `php -l` per file yang diubah + test PHPUnit yang ada tetap hijau.
- File-file yang dihapus dari repo harus juga dihapus dari produksi, dan sebaliknya.

---

## Fase 1 — Keamanan (prioritas tertinggi)

### 1.1 Reset password: hapus file darurat dari repo, ganti dengan reset via email

- **Status:** file sudah dihapus dari live (lihat §0.2). Sisa tugas: hapus `reset_password.php` dan `admin/forgot.php` dari repo (jika masih ada), dan hapus dari riwayat git bila memungkinkan (`git filter-repo` / BFG) — atau minimal pastikan password superadmin sudah diganti (tugas manual pemilik situs).
- **Jangan** membuat file reset darurat baru di web root. Reset darurat dilakukan via phpMyAdmin/CLI, bukan file web.
- **Kriteria selesai:** `git log --all --oneline -- reset_password.php admin/forgot.php` tidak menunjukkan file aktif di HEAD; tidak ada file reset darurat di web root.

### 1.2 Delete via GET → POST + CSRF (`admin/manage.php` baris ±464–480)

- Semua aksi hapus saat ini lewat `$_GET['del']` tanpa token dan tanpa cek method — di semua 10 modul.
- **Kerjakan:** ubah semua hapus menjadi POST dengan token CSRF (pakai helper CSRF yang sudah ada di `includes/csrf.php`), dan tambahkan konfirmasi di UI. Pastikan token diverifikasi di server, bukan hanya di JavaScript `confirm()`.
- **Kriteria selesai:** membuka URL `manage.php?m=<modul>&del=<id>` lewat GET tidak menghapus apa pun (tampilkan error / redirect); hapus hanya berhasil lewat form POST bertoken.

### 1.3 Otorisasi: `require_admin()` vs `require_superadmin()`

- `admin/tools/import_members.php` dan `admin/maintenance.php` saat ini hanya memakai `require_admin()` — admin biasa bisa mengaksesnya lewat URL langsung, menu hanya menyembunyikan.
- **Kerjakan:** buat helper `require_superadmin()` (cek `role === 'super_admin'` di session) dan pakai di kedua file itu. Audit semua halaman `admin/` dan pastikan setiap halaman memakai guard yang sesuai dengan perannya.
- **Kriteria selesai:** login sebagai admin biasa lalu buka langsung URL impor anggota dan maintenance → ditolak (403/redirect), bukan terbuka.

### 1.4 Dropdown peran + perlindungan hapus-diri superadmin (`admin/manage.php`, modul users)

- Dropdown Role di form pengguna hanya berisi User dan Admin (tanpa `super_admin`); saat mengedit superadmin, dropdown tampil "— Pilih —" sehingga peran superadmin bisa terhapus/tert Downgrade tanpa sengaja.
- Baris superadmin sendiri punya tombol Hapus, padahal dia satu-satunya `super_admin`.
- **Kerjakan:** tambahkan opsi `super_admin` di dropdown (hanya boleh dipilih/diubah oleh superadmin); cegah pengguna menghapus akunnya sendiri; cegah penurunan peran satu-satunya superadmin (tampilkan error, jangan simpan).
- **Kriteria selesai:** edit superadmin tanpa mengubah role → role tetap `super_admin`; hapus diri sendiri → ditolak; hanya tersisa satu superadmin dan tidak bisa dihapus/diturunkan lewat UI.

### 1.5 Impor anggota: CSRF + validasi (`admin/tools/import_members.php`)

- Tambahkan token CSRF pada form impor (saat ini tanpa CSRF).
- Password CSV sudah di-hash (`password_hash`) — pertahankan itu. Tambahkan: tolak file CSV yang diunggah lewat jalur lain, batasi ukuran file, dan hapus file CSV sementara setelah impor selesai (jangan biarkan file berisi password plaintext tersimpan di server).
- **Kriteria selesai:** impor tanpa token CSRF ditolak; tidak ada file CSV impor yang tersisa di server setelah proses selesai.

### 1.6 Flag `is_public` materi tidak ditegakkan (`pages/materi.php`)

- File di `uploads/materials/` bisa diakses langsung tanpa login, dan materi berflag Private tetap bisa dilihat semua user login.
- **Kerjakan:** sajikan file materi lewat skrip PHP (bukan tautan langsung ke `uploads/`), cek login + cek flag `is_public`/kepemilikan sebelum streaming file; tambahkan `.htaccess` di `uploads/` yang menolak akses langsung (`deny from all`) untuk folder yang sensitif.
- **Kriteria selesai:** buka URL langsung `uploads/materials/<file>.pdf` tanpa login → ditolak; materi Private tidak bisa dibuka user biasa yang login.

### 1.7 Celah lain dari audit kode

- **Stored XSS via upload SVG:** logo departemen/kabinet/leaders/pengurus menerima SVG yang disajikan langsung dari `uploads/`. **Kerjakan:** tolak SVG, atau sajikan SVG dengan `Content-Security-Policy: script-src 'none'` + `Content-Disposition: attachment`, atau sanitasi SVG saat upload.
- **Open redirect via parameter `next`:** validasi `next` hanya boleh mengarah ke path internal (awali `/`, tolak `http://`, `https://`, `//`).
- **Login user tanpa rate limit & tanpa `session_regenerate_id`:** samakan dengan login admin (rate limiter sudah ada di `includes/rate_limiter.php`) + regenerate session ID setiap login berhasil.
- **Toggle maintenance di dashboard tanpa CSRF:** tambahkan token CSRF (form maintenance-nya sendiri sudah pakai CSRF — samakan).
- **Kriteria selesai:** masing-masing poin di atas diverifikasi dengan skenario serangannya (coba SVG berisi script, coba `?next=https://evil.com`, coba brute-force login user, coba toggle maintenance via GET).

### 1.8 Produksi: matikan `display_errors`, pasang security headers, amankan cookie

- **Status di repo:** sudah ada (`display_errors=0`, `includes/security_headers.php`, cookie HttpOnly/SameSite/Secure). **Tugas:** pastikan semuanya aktif di produksi (drift §0.1). Verifikasi dengan `curl -I https://hmtaitera.com/` — harus ada `Strict-Transport-Security`, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, dan cookie `PHPSESSID` berflag `HttpOnly; SameSite=Lax; Secure`; tidak ada `X-Powered-By: PHP/...`.
- **Kriteria selesai:** hasil `curl -I` menunjukkan header di atas dan tidak ada kebocoran versi PHP.

### 1.9 Upload: kunci direktori `uploads/`

- Tambahkan `.htaccess` di `uploads/` yang mematikan eksekusi PHP (`php_flag engine off` / `RemoveHandler`) dan menolak akses langsung ke file sensitif (lihat juga §1.6). Pertahankan validasi server-side yang sudah ada (whitelist ekstensi, batas ukuran, nama file acak `random_bytes(16)`).
- **Kriteria selesai:** upload file `.php` (atau file dengan ekstensi ganda) → ditolak; file `.php` yang ditaruh manual di `uploads/` tidak bisa dieksekusi lewat web.

---

## Fase 2 — Performa (situs terasa berat)

Pengukuran live 2026-11-21: TTFB homepage 0,75–1,67 detik, gallery 1,16 detik, **file CSS statis saja 1,39 detik**. HTML homepage hanya 11 KB — kontennya ringan, servernya yang lambat menjawab. Pagination (10 item/halaman) tidak memperbaiki TTFB.

### 2.1 Gambar homepage (dampak terbesar di sisi file)

- `Resource/img/banner1.png` 839 KB (1280×564) di-preload dengan `fetchpriority="high"` (`includes/header.php:36`); `banner2.png` 2,1 MB; `banner3.png` 1,1 MB; logo `Resource/img/IMG_1381.PNG` 372 KB ditampilkan 60 px dan dipakai juga sebagai favicon (`includes/header.php:39,90`). Total 3 banner + logo = 4,3 MB.
- **Kerjakan:** konversi ke WebP (kualitas ±80) dan ganti referensinya:
  - `banner1.png` → `banner1.webp` (~41 KB), `banner2.png` → `banner2.webp` (~248 KB), `banner3.png` → `banner3.webp` (~49 KB). Ketiga banner TIDAK memakai transparansi (alpha 255 di semua piksel) — aman ke RGB/WebP.
  - Logo: `IMG_1381.PNG` memakai transparansi — konversi ke WebP **dengan alpha dipertahankan** (~121 KB). Sediakan juga `logo-header-120.png` (120 px, ~15 KB) untuk header dan `favicon-32.png` (32 px, ~2 KB) untuk favicon; ubah `includes/header.php` agar header memakai logo kecil dan favicon memakai file 32 px.
  - File-file hasil kompresi (referensi, sudah terverifikasi tampil normal) ada di `workspace/your_files/audit-hmta/gambar-ringan/` — pakai sebagai pembanding, atau generate ulang dengan `cwebp`/PIL.
- **Kriteria selesai:** total transfer gambar homepage < 500 KB; logo header < 25 KB; favicon < 5 KB; tampilan visual tidak berubah (cek screenshot sebelum/sesudah).

### 2.2 TTFB ~1 detik (dampak terbesar keseluruhan)

- TTFB tinggi bahkan untuk file statis → masalah di hosting/server, bukan di kode halaman.
- **Kerjakan (kode):** pastikan OPcache aktif (cek `phpinfo` / `opcache_get_status` — jangan commit hasilnya), pastikan `.htaccess` gzip + cache browser tetap aktif, dan aktifkan `includes/cache.php` (lihat §2.4).
- **Tugas manual pemilik situs (agent tidak bisa):** pasang Cloudflare gratis di depan domain (cache aset statis di edge) dan/atau periksa paket Hostinger. Setelah Cloudflare aktif, verifikasi ulang TTFB dari lokasi berbeda.
- **Kriteria selesai:** TTFB homepage < 600 ms pada pengukuran ulang (3x `curl`, ambil median); aset statis ter-cache (`cf-cache-status: HIT` / `Cache-Control` sesuai).

### 2.3 Aset 404 (26 URL)

- 6 logo departemen, 5 foto kabinet, 8 foto organisasi, gambar testimoni/minerfesto/lustrum, dan halaman `lustrum.php` mengembalikan 404 — kemungkinan folder `uploads/` tidak ikut saat migrasi/deploy. Tiap 404 tetap memakan satu request ~1 detik + halaman error Hostinger.
- **Kerjakan:** rekonsiliasi `uploads/` dengan nilai `logo_url`/`photo_url` di DB — upload ulang file yang hilang dari repo (`uploads/departments/` berisi 6 logo, `uploads/kabinet/` berisi 2 file; sisanya minta ke pemilik situs) ATAU hapus referensi yang file-nya memang sudah tidak ada. Jangan biarkan referensi 404.
- **Kriteria selesai:** `curl` semua URL aset internal → 0 respons 404 (daftar lengkap ada di `audit-hmta-2026-11-21.md` §5).

### 2.4 N+1 query + cache mati

- `pages/department.php` (±baris 105) memanggil `get_division_members()` di dalam loop divisi — satu query per divisi.
- `includes/cache.php` sudah ditulis tapi **tidak pernah di-require** (bootstrap hanya me-require `database/config.php` + `database/db.php`), jadi cache events tidak aktif.
- **Kerjakan:** gabungkan anggota divisi jadi satu query `WHERE division_id IN (...)`; tambahkan `require_once includes/cache.php` di `includes/functions.php` dan pakai untuk data yang jarang berubah (events, kabinet, departemen).
- **Kriteria selesai:** halaman departemen dengan 6 divisi mengeksekusi ≤ 6 query (cek dengan query log); cache events aktif dan terisi.

### 2.5 Gambar responsif

- Tidak ada `srcset`/gambar responsif — HP memuat gambar yang sama besarnya dengan desktop.
- **Kerjakan:** tambahkan `srcset` + `sizes` untuk banner dan foto (sediakan varian lebar 480/768/1280), dan `loading="lazy"` untuk gambar di bawah lipatan (banner 2–3 slideshow sudah diklaim lazy tapi memakai `background-image` — pastikan benar-benar tidak diunduh sebelum tampil, atau ubah jadi `<img loading="lazy">`).
- **Kriteria selesai:** di viewport 390 px, total gambar yang diunduh < 40% dari total di desktop.

### 2.6 Pagination — verifikasi live

- Repo sudah punya `LIMIT ? OFFSET ?` di `pages/materi.php` (baris ±68, ±76) dan `pages/gallery.php` (baris ±39, ±47).
- **Kerjakan:** verifikasi pagination itu benar-benar aktif di produksi (drift §0.1) — buka halaman materi/galeri live dan pastikan hanya 10 item per halaman + tautan halaman berfungsi. Jika belum live, deploy dari repo.
- **Kriteria selesai:** materi/galeri live menampilkan ≤ 10 item per halaman dengan navigasi halaman yang berfungsi.

### 2.7 BASE_URL masih HTTP + redirect berantai (temuan dari audit Hostinger)

- Homepage memuat CSS lewat `http://hmtaitera.com/Resource/css/style.css` — setiap aset internal yang direferensikan dengan `http://` harus melewati satu redirect 301 ke `https://` (satu round-trip ~1 detik di server ini) sebelum dimuat, termasuk CSS yang render-blocking. `www` dan non-www sama-sama 200 tanpa konsolidasi.
- **Kerjakan:** ubah `BASE_URL` (dan semua URL internal) ke `https://hmtaitera.com/`; pastikan tidak ada lagi referensi `http://hmtaitera.com` di template/kode; tambahkan redirect permanen 301 satu arah www → non-www (atau sebaliknya, konsisten) di `.htaccess`.
- **Kriteria selesai:** `grep -r "http://hmtaitera.com" --include="*.php" .` → 0 hasil; `curl -I http://hmtaitera.com/` → satu 301 ke `https://`; `curl -I https://www.hmtaitera.com/` → 301 ke non-www.

### 2.8 Cache-Control dokumen + CSS tak terpakai + font

- Homepage dikirim dengan header `Cache-Control: no-store, no-cache, must-revalidate` — browser tidak boleh menyimpan halaman sama sekali, jadi kunjungan ulang sama lambatnya dengan kunjungan pertama. (Aset statis sudah punya cache 7 hari — yang bermasalah dokumen HTML-nya.)
- Audit Hostinger: "Reduce unused CSS" 50, "Font display" 50 — `style.css` lokal + Font Awesome 6.5.0 dari CDN dimuat penuh padahal sebagian besar tidak terpakai; font tanpa `font-display: swap`.
- **Kerjakan:** ubah header dokumen jadi cache yang wajar (mis. `Cache-Control: public, max-age=60` untuk halaman dinamis, atau `private` bila halaman memuat data login — jangan `no-store` untuk halaman publik); purge/split CSS yang tidak terpakai per halaman (atau muat Font Awesome secara subset / ganti ikon yang dipakai dengan SVG inline); tambahkan `font-display: swap` pada semua `@font-face`.
- **Kriteria selesai:** `curl -I https://hmtaitera.com/` tidak lagi mengirim `no-store` untuk halaman publik; total CSS yang diunduh homepage berkurang ≥ 40%; tidak ada teks yang tak terlihat saat font dimuat (cek `font-display`).

---

## Fase 3 — Data Departemen & Kabinet

Detail lengkap ada di `audit-kode-hmta-2026-11-21.md` §16. Tiga akar masalah: kode yang hardcode/tidak memakai kolom DB, data DB yang salah, file `uploads/` hilang.

### 3.1 Kode — logo departemen (`pages/department.php`, `includes/department_helpers.php`)

- `department.php` (±baris 40) hardcode logo ke `Resource/img/banner1.png` untuk semua departemen; `get_department_by_slug()` hanya SELECT `id, name, slug, description` — kolom `logo_url` tidak pernah dibaca di halaman ini. Sebaliknya `pages/samagri.php:112` sudah memakai `logo_url` dari DB.
- **Kerjakan:** tambahkan `logo_url` ke SELECT di `get_department_by_slug()` dan tampilkan di `department.php` (dengan fallback ke `banner1.png` bila kosong).
- **Kriteria selesai:** tiap halaman departemen menampilkan logo departemennya sendiri, bukan banner1.png.

### 3.2 Data — slug tertukar (diperbaiki via migrasi 004, dijalankan di produksi sebelum/saat deploy)

- Lookup slug di kode sudah benar (case-insensitive, exact match). Yang salah datanya: baris Medkominfo slug-nya "Internal".
- **Perbaiki di DB:** Medkominfo → `medkominfo`, Internal → `internal`. Perbarui tautan yang memakai slug lama. Inkonsistensi terkait: file logo menyebut "Medkomvis" (`Logo-Departemen-Medkomvis.jpg`) sementara departemennya "Medkominfo" — samakan penamaannya.
- **Kriteria selesai:** `department.php?slug=internal` menampilkan Departemen Internal; `?slug=medkominfo` menampilkan Medkominfo.

### 3.3 Data — periode kabinet duplikat (tugas manual via panel admin / SQL)

- METANOIA (ID 6) dan Samagri (ID 1) sama-sama mengklaim periode 2024-2025. Logika kabinet aktif di `pages/struktur.php:13-25` mencari periode yang memuat tahun berjalan, lalu fallback ke id terbaru — rapuh selama periodenya salah/duplikat.
- **Perbaiki di DB:** satu kabinet per periode, sesuai periode yang sebenarnya. Isi juga deskripsi dan jumlah anggota tiap kabinet (saat ini "Deskripsi belum tersedia" dan "— Anggota").
- **Kriteria selesai:** `struktur.php` menampilkan tepat satu "Kabinet Aktif" dengan periode yang benar dan tidak ada dua kabinet berperiode sama.

### 3.4 Kode — nama kabinet hardcode di homepage (`index.php:26`)

- Banner 2 menulis `<h2>Kabinet Samagri 2024-2025</h2>` langsung di template — bukan dari DB.
- **Kerjakan:** ambil nama + periode kabinet aktif dari DB (pakai logika yang sama dengan `struktur.php`) atau pindahkan ke pengaturan yang bisa diubah dari panel admin.
- **Kriteria selesai:** mengubah kabinet aktif di DB mengubah teks banner homepage tanpa edit kode.

### 3.5 Foto kabinet

- `logo_url` kabinet di DB menunjuk ke `uploads/kabinet/` yang tidak ada di server; di repo pun hanya ada 2 file di folder itu.
- **Kerjakan:** upload ulang foto kabinet (minta file-nya ke pemilik situs bila tidak ada di repo) dan sinkronkan `uploads/` dengan DB (lihat juga §2.3).
- **Kriteria selesai:** semua foto kabinet tampil (0 gambar rusak di halaman struktur/kabinet).

---

## Fase 4 — Konten, SEO & data admin

### 4.1 Konten kedaluwarsa

- Kabinet masih METANOIA 2024-2025 dengan Ketua lama Johan Hezekiel Marolop Ambarita (ketua saat ini Abdurrahman Zidan Rizki); pendaftaran Minerfesto 2025 masih dipajang; teks lelucon "Kegiatan inagur mueheheheheh" di Past Events; galeri dan Upcoming Events kosong; counter "Anggota Aktif 200" (data sebenarnya 440).
- **Kerjakan (kode):** pindahkan konten tahunan (nama kabinet aktif, tanggal pendaftaran, counter) ke database/panel admin — jangan hardcode tahun di template (`index.php`, `minerfesto.php`).
- **Tugas manual pemilik situs:** perbarui data kabinet, hapus/arsipkan pendaftaran Minerfesto 2025, perbaiki teks lelucon, isi counter dari data sebenarnya.
- **Kriteria selesai:** tidak ada lagi konten 2024-2025 yang tampil sebagai "saat ini"; counter sesuai data.

### 4.2 Email footer salah tulis

- Footer menampilkan `hmta.balakosa.itera.com` (teks + mailto) — seharusnya `hmta.balakosa.itera@gmail.com`.
- **Kerjakan:** perbaiki di template footer (dan cek apakah alamat yang sama hardcode di tempat lain — audit menemukan tiga alamat email berbeda untuk satu alamat kontak; satukan ke satu sumber).
- **Kriteria selesai:** `grep -r "hmta.balakosa.itera.com" --include="*.php" .` → 0 hasil; mailto footer bisa diklik dan benar.

### 4.3 SEO dasar

- Homepage tanpa H1, tanpa canonical, tanpa Open Graph; `robots.txt` dan `sitemap.xml` 404; www dan non-www sama-sama 200; judul halaman departemen ganda ("Departemen Departemen Eksternal").
- **Kerjakan:** tambahkan H1 di homepage; tambahkan canonical + Open Graph di header; buat `robots.txt` dan `sitemap.xml` (hasilkan otomatis dari rute/DB bila memungkinkan); redirect permanen www → non-www (atau sebaliknya, konsisten); perbaiki template judul halaman departemen.
- **Kriteria selesai:** homepage punya tepat satu H1; `robots.txt` + `sitemap.xml` mengembalikan 200; judul departemen tanpa kata ganda.

### 4.4 Bug kecil lain (dari audit)

- Include footer salah di `pages/annual/minerfesto.php` (`../includes/footer.php` → harusnya `../../includes/footer.php`) — footer halaman itu tidak tampil sama sekali.
- Typo "Pertambagan" di konten dan slug LinkedIn (`...-teknik-pertambagan-itera-...`).
- Typo "Sekretaris Jendral" di jabatan (ID 11).
- Slug departemen tidak konsisten ("Keilmuan" kapital, "Ekster" terpotong).
- FK tampil sebagai ID angka mentah di tabel Leaders/Programs/Divisi/Anggota — tampilkan nama (JOIN).
- Tanggal gabung tidak valid `0000-00-00`; entri materi duplikat (Yusup Roman, Silfia Ardhila, Abdul Aziz Nasution).
- Gambar event kalender memakai URL `http://localhost/root/uploads/...`.
- **Kriteria selesai:** masing-masing dicek dan diperbaiki; tidak ada lagi URL localhost di konten produksi.

---

## Fase 5 — Kualitas kode & maintainability

### 5.1 Pagination admin (`admin/manage.php` baris ±483)

- Halaman Anggota memuat SEMUA 903 anggota (283 KB) dan 908 pengguna dalam satu halaman tanpa pagination/search.
- **Kerjakan:** tambahkan `LIMIT/OFFSET` + pagination + pencarian di semua modul `manage.php` (contoh yang benar sudah ada di `pages/materi.php` — 20/halaman).
- **Kriteria selesai:** tidak ada halaman admin yang memuat > 50 baris sekaligus; ada kontrol halaman + pencarian.

### 5.2 Satukan konfigurasi, lengkapi skema

- Dua sistem config dan dua sistem koneksi DB dengan nilai bertentangan; `schema.sql` tidak punya tabel `members`/`password_resets`/`login_attempts` dan ENUM `role` tanpa `super_admin`; migrasi 002 gagal di DB baru (kolom `events.active` tidak ada) — deploy dari nol tidak dapat direproduksi.
- **Kerjakan:** satu sumber config: `.env` + `database/config.php` saja; hapus `config/config.php` legacy (setelah dipastikan tidak ada yang me-require-nya); lengkapi `schema.sql` (tambah tabel yang hilang, ENUM `super_admin`); perbaiki migrasi 002; buat satu perintah untuk menjalankan migrasi.
- **Kriteria selesai:** clone baru + `schema.sql` + migrasi → database lengkap dan aplikasi jalan tanpa edit manual.

### 5.3 Bersihkan dead code & aset

- Hapus dari repo: `test_view.php`, `debug.php`, class `Database` legacy, `.phpunit.result.cache` yang ter-commit. `includes/cache.php` yang mati diaktifkan (§2.4) atau dihapus bila memang tidak dipakai.
- Satukan 3 sidebar admin yang tidak sinkron (16 item di dashboard, 15 di manage.php, 8 di impor anggota) jadi satu sumber.
- Hapus definisi ulang helper `h()` per file — pakai satu helper bersama.
- Kompres aset besar: favicon 372 KB → < 50 KB, foto 3,2 MB → WebP/terkompres (lihat §2.1).
- **Kriteria selesai:** tidak ada file dead code di repo; sidebar konsisten di semua halaman admin; `grep -rn "function h(" --include="*.php" .` → 1 hasil.

### 5.4 Satu sumber kebenaran untuk konten

- Nama kabinet, tahun Minerfesto, counter, alamat email, dan logo departemen saat ini menempel di kode (lihat §4.1–4.2, §3.4).
- **Kerjakan:** pindahkan ke DB/panel admin atau satu file konfigurasi. `medkom-init.js` saat ini dimuat di semua halaman departemen padahal spesifik Medkominfo — muat hanya di halaman Medkominfo.
- **Kriteria selesai:** mengganti tahun/kabinet/email tidak butuh edit template.

---

## Cara verifikasi keseluruhan (checklist akhir)

Jalankan setelah semua fase selesai, dan laporkan hasilnya:

- [ ] `curl -I https://hmtaitera.com/` → ada HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy; tidak ada `X-Powered-By`; cookie `PHPSESSID` berflag HttpOnly + SameSite + Secure.
- [ ] `curl -s -o /dev/null -w "%{http_code}" https://hmtaitera.com/admin/forgot.php` → 404; `.../reset_password.php` → 404; `.../hmta.sql` → 404; `.../debug.php` → 404.
- [ ] Buka `manage.php?m=anggota&del=1` lewat GET saat login admin → tidak terhapus.
- [ ] Login admin biasa → buka langsung `admin/tools/import_members.php` dan `admin/maintenance.php` → ditolak.
- [ ] TTFB homepage (median 3x `curl`) < 600 ms; total gambar homepage < 500 KB.
- [ ] 0 aset internal 404 (cek daftar §5 laporan utama).
- [ ] `department.php?slug=internal` → Departemen Internal; `?slug=medkominfo` → Medkominfo; tiap halaman departemen menampilkan logonya sendiri.
- [ ] `struktur.php` → tepat satu Kabinet Aktif dengan periode benar; homepage tidak lagi menulis "Kabinet Samagri 2024-2025".
- [ ] Footer email benar dan bisa diklik; `robots.txt` + `sitemap.xml` → 200; homepage punya satu H1.
- [ ] Halaman admin Anggota/Pengguna menampilkan ≤ 50 baris + pagination + pencarian.
- [ ] Clone baru + schema + migrasi → aplikasi jalan tanpa edit manual.
- [ ] `php -l` semua file yang diubah lolos; test PHPUnit hijau.

---

## Yang TIDAK boleh dilakukan agent

- Membuat ulang `forgot.php`, `reset_password.php`, `debug.php`, atau menaruh file `.sql`/`.env` di web root.
- Mengganti password akun apa pun (tugas manual pemilik situs).
- Mengubah isi data (nama anggota, prestasi, nilai) — hanya struktur/kode.
- Mengirim email, mengubah DNS/Cloudflare, atau mengubah paket hosting (tugas manual pemilik situs).
- Menebak file/data yang hilang — tanyakan / tulis sebagai blokir dan lanjutkan tugas lain.

---

## Lampiran: Baseline audit Hostinger (2026-11-21, sebelum perbaikan)

Skor performa hmtaitera.com dari panel Hostinger (Lighthouse):

| Metrik | Nilai | Status |
|---|---|---|
| Skor performa | 79 | 🟡 50–89 |
| Largest Contentful Paint (LCP) | 4,2 dtk | 🔴 |
| Total Blocking Time | 0 ms | 🟢 |
| First Contentful Paint | 0,4 dtk | 🟢 |
| Cumulative Layout Shift | 0,001 | 🟢 |
| Speed Index | 0,7 dtk | 🟢 |
| Avoid multiple page redirects | 0 | 🔴 |
| Document request latency | 0 | 🔴 |
| Reduce unused CSS | 50 | 🟡 |
| Avoid enormous network payloads | 50 | 🟡 |
| Font display | 50 | 🟡 |
| Waktu Respon | 54 | 🟡 |

**Target setelah semua fase selesai:** skor ≥ 90, LCP < 2,5 dtk, document latency & redirects hijau. Pengukuran ulang memakai alat yang sama (panel Hostinger) + `curl` (lihat checklist verifikasi).

**Catatan verifikasi tambahan (Muse, 2026-11-21):** `BASE_URL` masih `http://` sehingga CSS homepage dimuat lewat `http://hmtaitera.com/Resource/css/style.css` (redirect 301 dulu sebelum dimuat); homepage dikirim dengan `Cache-Control: no-store, no-cache, must-revalidate` (kunjungan ulang tidak ter-cache); `www` dan non-www sama-sama 200 tanpa konsolidasi. Ketiga hal ini sudah masuk sebagai tugas §2.7–§2.8.
