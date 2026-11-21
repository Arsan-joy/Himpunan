# Audit Website HMTA ITERA — hmtaitera.com

**Tanggal audit:** 21 November 2026 (01:38–01:42 WIB)
**URL:** https://hmtaitera.com
**Metode:** Pasif saja (read-only). Hanya mengambil halaman publik dan memeriksa header respons; tidak ada pengujian eksploitasi, tidak ada brute force. Audit panel admin (§11–§12) dilakukan terpisah dengan login resmi pemilik situs (akun admin_1 lalu superadmin, read-only — tidak ada data yang dibuat, diubah, atau dihapus); akun admin_2 belum terverifikasi karena vault hanya menyimpan satu login per situs.
**Keterbatasan:** Lalu lintas dari lingkungan audit melewati egress proxy, sehingga sertifikat TLS yang terlihat langsung adalah sertifikat proxy. Sertifikat asli situs diverifikasi lewat catatan publik Certificate Transparency (crt.sh).

---

## Ringkasan eksekutif

Situs **hidup dan bisa diakses**, tapi kondisinya memprihatinkan untuk sebuah portal resmi himpunan: **40% aset internal mengembalikan 404** (semua logo departemen, foto kabinet, dan foto organisasi hilang), satu halaman menampilkan **peringatan PHP mentah yang membocorkan path server**, email kontak di footer **salah tulis sehingga tidak bisa diklik**, dan konten kepengurusan **sudah kedaluwarsa satu kabinet**. Keamanan dasar (header, cookie, versi PHP) juga masih bocor. Performa lambat (TTFB ~1 detik) dan SEO dasar belum dikerjakan (tanpa H1 di homepage, tanpa robots.txt/sitemap, tanpa Open Graph). Audit panel admin (akun admin_1) menambah temuan serius: penghapusan data lewat **tautan GET tanpa token**, **password admin yang sangat lemah** tanpa CAPTCHA atau rate-limit di login, dan **tidak adanya fitur ganti password maupun manajemen pengguna** di dalam panel. Audit superadmin (§12) menambah lagi: impor anggota massal memakai **password plaintext dari CSV**, **bug dropdown peran** yang bisa menurunkan peran superadmin, dan **tidak ada perlindungan hapus diri** — baris superadmin sendiri punya tombol Hapus.

**Total temuan:** 39 — 0 Kritis, 9 Tinggi, 17 Sedang, 13 Rendah/Info (24 dari audit publik + 8 dari audit panel admin §11 + 7 dari audit superadmin §12).

---

## 1. Ketersediaan & DNS

| Item | Hasil |
|---|---|
| `https://hmtaitera.com/` | **200 OK**, tanpa redirect |
| `http://hmtaitera.com/` | **301** → `https://hmtaitera.com/` |
| `https://www.hmtaitera.com/` | **200 OK** (tidak diarahkan ke non-www) |
| DNS A | `198.18.109.179`, `198.18.109.180` |
| DNS MX | **tidak ada** (email himpunan lewat Gmail — konsisten) |

### 1.1 — www dan non-www sama-sama aktif, tanpa canonical
- **Severity:** Sedang
- **Bukti:** `https://www.hmtaitera.com/` mengembalikan 200 dan menyajikan situs yang sama dengan `https://hmtaitera.com/`; tidak ada header `Location` dan tidak ada tag `<link rel="canonical">` di halaman mana pun.
- **Dampak:** Duplikat konten di mata mesin pencari; otoritas halaman terpecah antara dua versi URL.
- **Perbaikan:** Pilih satu versi kanonis (disarankan non-www), redirect 301 `www` → non-www di `.htaccess`/hPanel, dan tambahkan `<link rel="canonical">` di semua halaman.

---

## 2. SSL/TLS

### 2.1 — Sertifikat valid (verifikasi lewat CT log)
- **Severity:** Info
- **Bukti:** `crt.sh` mencatat sertifikat aktif: issuer **Let's Encrypt (CN=YE2)**, diterbitkan **2026-10-04**, berakhir **2027-01-02**, mencakup `hmtaitera.com` dan `www.hmtaitera.com`. Handshake teramati memakai **TLS 1.3** (`TLS_AES_256_GCM_SHA384`).
- **Catatan:** Sertifikat yang terlihat langsung dari lingkungan audit adalah sertifikat egress proxy (`CN=hmtaitera.com`, issuer `Hatch Sandbox Egress CA`), bukan sertifikat asli — karena itu verifikasi dilakukan lewat CT log publik.
- **Perbaikan:** Tidak ada tindakan mendesak; pastikan auto-renewal Let's Encrypt di Hostinger tetap aktif sebelum 2 Januari 2027. (Perpanjangan domain .com 8 Des 2026–8 Des 2027 adalah hal terpisah dari sertifikat TLS.)

---

## 3. Header keamanan

Header yang teramati pada semua halaman (contoh: `curl -I https://hmtaitera.com/`):

```
Server: LiteSpeed
platform: hostinger
panel: hpanel
X-Powered-By: PHP/8.3.37
Content-Security-Policy: upgrade-insecure-requests
Cache-Control: no-store, no-cache, must-revalidate
```

### 3.1 — Hampir semua header keamanan tidak ada
- **Severity:** Sedang
- **Bukti:** Satu-satunya header keamanan adalah `Content-Security-Policy: upgrade-insecure-requests` (CSP yang sangat minimal). **Tidak ada** `Strict-Transport-Security`, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, maupun `Permissions-Policy`.
- **Dampak:** Situs rentan clickjacking (tanpa `X-Frame-Options`), MIME sniffing (tanpa `X-Content-Type-Options`), dan downgrade ke HTTP pada kunjungan pertama (tanpa HSTS, meski redirect HTTP→HTTPS sudah ada).
- **Perbaikan:** Tambahkan di `.htaccess` (LiteSpeed mendukung `Header set`):
  ```
  Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-Content-Type-Options "nosniff"
  Header set Referrer-Policy "strict-origin-when-cross-origin"
  Header set Permissions-Policy "camera=(), microphone=(), geolocation=()"
  ```
  Perkuat CSP secara bertahap (mulai dari `default-src 'self'` + domain CDN yang dipakai).

### 3.2 — Versi PHP bocor lewat header
- **Severity:** Rendah
- **Bukti:** `X-Powered-By: PHP/8.3.37` pada setiap respons.
- **Dampak:** Memberi tahu penyerang versi runtime yang persis untuk dicocokkan dengan CVE yang diketahui.
- **Perbaikan:** Di `.htaccess` tambahkan `php_flag expose_php Off` (atau `Header unset X-Powered-By`).

### 3.3 — Cookie sesi tanpa HttpOnly dan SameSite
- **Severity:** Sedang
- **Bukti:** Respons homepage menyetel `PHPSESSID` dengan atribut `path=/; secure` **tanpa `HttpOnly` dan tanpa `SameSite`**. Cookie sesi juga dikeluarkan untuk pengunjung anonim yang belum login.
- **Dampak:** Tanpa `HttpOnly`, cookie sesi bisa dibaca JavaScript (risiko pencurian sesi lewat XSS). Tanpa `SameSite`, cookie ikut terkirim pada permintaan lintas situs (risiko CSRF).
- **Perbaikan:** Di `php.ini`/kode awal aplikasi: `session.cookie_httponly = 1`, `session.cookie_secure = 1`, `session.cookie_samesite = "Lax"`. Idealnya mulai sesi hanya setelah login, bukan untuk setiap pengunjung.

### 3.4 — Form tanpa token CSRF
- **Severity:** Sedang
- **Bukti:** Form login (`pages/login.php`, `method="post"`, input `username`/`password`) hanya punya satu input tersembunyi (`next`) — tidak ada token CSRF. Form kontak di `pages/annual/minerfesto.php` (`id="contactForm"`, input `name`/`email`/`subject`) juga tidak menunjukkan token CSRF.
- **Dampak:** Permintaan POST bisa dipalsukan dari situs lain (login CSRF risikonya rendah, tapi form kontak bisa dipakai untuk spam).
- **Perbaikan:** Tambahkan token CSRF per-sesi pada semua form POST dan validasi di sisi server.

### 3.5 — `display_errors` aktif: peringatan PHP bocor ke publik
- **Severity:** Tinggi
- **Bukti:** Halaman `https://hmtaitera.com/pages/annual/minerfesto.php` menampilkan di badan halaman:
  > `Warning: include(/home/u782889506/domains/hmtaitera.com/public_html/../includes/footer.php): Failed to open stream: No such file or directory in /home/u782889506/domains/hmtaitera.com/public_html/pages/annual/minerfesto.php on line 356`
- **Dampak:** Membocorkan username hosting (`u782889506`), struktur direktori server, dan nama file internal. Selain itu footer halaman tersebut **tidak tampil sama sekali** (include gagal) — halaman berakhir pada pesan warning.
- **Perbaikan:** (1) Matikan tampilan error di produksi (`display_errors = Off`, `log_errors = On`, arahkan ke file log). (2) Perbaiki path include di `minerfesto.php` baris 356: file berada di `pages/annual/`, jadi path yang benar adalah `../../includes/footer.php`, bukan `../includes/footer.php`.

---

## 4. File sensitif & area admin

| Path | Hasil |
|---|---|
| `/admin` | **301** → `/admin/` (lalu 302, diduga ke login) |
| `/admin/` | **302** (mengarah ke login) |
| `/pages/login.php` | **200** — form login tampil normal |
| `/pages/materi.php` | **302** → `http://hmtaitera.com/pages/login.php?next=%2Fpages%2Fmateri.php` (konten MIROTA terkunci login — sudah benar) |
| `/.env` | 403 |
| `/.git/HEAD` | 403 |
| `/.htaccess` | 403 |
| `/phpinfo.php`, `/config.php`, `/backup.zip`, `/wp-admin`, `/wp-login.php`, `/login.php`, `/admin.php` | 404 |

- **Severity:** Info (tidak ada temuan)
- **Bukti:** Tidak ada file sensitif yang bisa diakses publik. Status 403 pada `.env`/`.git/HEAD`/`.htaccess` adalah blokir bawaan Hostinger; tidak bisa disimpulkan file itu ada atau tidak.
- **Catatan:** Redirect login `materi.php` memakai URL `http://` (bukan `https`) pada header `Location` — saat ini diselamatkan oleh CSP `upgrade-insecure-requests`, tapi sebaiknya diperbaiki ke `https` secara eksplisit.

---

## 5. Performa

| Pengukuran | Hasil |
|---|---|
| TTFB homepage | **1,13 dtk** dan **1,36 dtk** (2 sampel) |
| TTFB halaman lain | 0,94–1,28 dtk (`profile` 1,01 dtk; `struktur` 1,15 dtk; `calendar` 1,28 dtk; `past` 1,28 dtk; `upcoming` 0,99 dtk) |
| TTFB aset statis | 0,95–1,44 dtk (`style.css` 1,10 dtk; `index.js` 1,44 dtk; `banner1.png` 0,95 dtk) |
| Ukuran HTML homepage | 11.414 byte (gzip → 3.631 byte, kompresi aktif) |
| Total bobot homepage | **~1,33 MB** (HTML 11 KB + CSS 19 KB + JS 8 KB + Font Awesome CDN ±110 KB + 2 gambar 1,21 MB) |
| Porsi gambar | **89%** dari total bobot |

### 5.1 — TTFB lambat (~1 detik) di semua halaman
- **Severity:** Sedang
- **Bukti:** Setiap halaman PHP butuh ~1 detik sebelum byte pertama, padahal ukuran HTML hanya 8–22 KB. Header HTML memakai `Cache-Control: no-store, no-cache, must-revalidate` (tidak ada page cache).
- **Dampak:** Pengalaman pengguna terasa lambat; skor Core Web Vitals (LCP) buruk.
- **Perbaikan:** Aktifkan OPcache (cek di hPanel → PHP), pertimbangkan page cache/output cache LiteSpeed, dan kurangi kueri database di halaman publik bila ada.

### 5.2 — Gambar tidak dioptimasi (89% bobot halaman)
- **Severity:** Sedang
- **Bukti:** `Resource/img/banner1.png` = **838.721 byte** dan dipakai sebagai gambar LCP (di-`preload` dengan `fetchpriority="high"`). `Resource/img/IMG_1381.PNG` (logo) = **371.913 byte** tapi ditampilkan setinggi 60 px — dan file yang sama dipakai sebagai **favicon**.
- **Dampak:** Banner besar memperlambat LCP; logo 372 KB untuk tampilan 60 px adalah pemborosan besar; favicon 372 KB dimuat di setiap halaman.
- **Perbaikan:** Kompres `banner1.png` ke WebP/JPEG ≤150 KB (atau `banner1.webp` dengan fallback), buat logo versi kecil (±20 KB PNG/WebP) untuk header, dan favicon terpisah ukuran 32×32 (±5 KB). Pertahankan `preload` LCP setelah gambar diperkecil.

### 5.3 — Aset statis sudah di-cache dengan benar
- **Severity:** Info (bagus)
- **Bukti:** `style.css`, `banner1.png`, `index.js` memakai `Cache-Control: public, max-age=604800` (7 hari) + `ETag` + `Last-Modified`.
- **Perbaikan:** Tidak perlu; pertahankan. (Catatan: aset memakai query `?v=1d1801dd` untuk cache-busting — sudah benar.)

---

## 6. SEO dasar

### 6.1 — Homepage tanpa H1, tanpa canonical, tanpa Open Graph
- **Severity:** Sedang
- **Bukti:** `<title>Homepage | HMTA ITERA</title>` (judul generik, bukan nama organisasi), `<meta name="description" content="Himpunan Mahasiswa Teknik Pertambangan ITERA">` (ada, tapi sangat pendek), **tidak ada `<h1>` sama sekali**, **tidak ada `rel="canonical"`**, **tidak ada tag Open Graph/Twitter Card**.
- **Dampak:** Mesin pencari dan pratinjau tautan (WhatsApp/Telegram) tidak mendapat judul, deskripsi, atau gambar yang layak; "Homepage" sebagai judul tidak memberi sinyal relevansi.
- **Perbaikan:** Judul menjadi `HMTA ITERA — Himpunan Mahasiswa Teknik Pertambangan ITERA`, tambahkan satu `<h1>` (mis. nama himpunan), tambahkan `rel="canonical"`, dan Open Graph (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`) di semua halaman.

### 6.2 — robots.txt dan sitemap.xml tidak ada
- **Severity:** Rendah
- **Bukti:** `https://hmtaitera.com/robots.txt` → **404** (halaman error Hostinger "This Page Does Not Exist"); `https://hmtaitera.com/sitemap.xml` → **404** yang sama.
- **Perbaikan:** Buat `robots.txt` (izinkan semua, tunjuk ke sitemap) dan `sitemap.xml` berisi 9+ halaman publik, lalu daftarkan di Google Search Console.

### 6.3 — Judul halaman departemen ganda
- **Severity:** Rendah
- **Bukti:** Semua halaman departemen memakai `<title>Departemen Departemen Eksternal | HMTA ITERA</title>` — kata "Departemen" tertulis dua kali.
- **Perbaikan:** Perbaiki template judul menjadi `Departemen Eksternal | HMTA ITERA`.

### 6.4 — H1 hilang / H2 kosong di beberapa halaman
- **Severity:** Rendah
- **Bukti:** `pages/past.php` tidak punya `<h1>`; `pages/calendar.php`, `pages/gallery.php`, dan `pages/upcoming.php` punya tag `<h2>` kosong.
- **Perbaikan:** Tambahkan H1 di `past.php` dan hapus/isi tag H2 kosong.

### 6.5 — Slug departemen tertukar di database
- **Severity:** Rendah
- **Bukti:** `department.php?slug=Internal` menampilkan **Departemen Media Komunikasi Informasi**, sedangkan `department.php?slug=intern` menampilkan **Departemen Internal**. Tautan di `samagri.php` memakai slug yang "salah" itu secara konsisten sehingga situs tetap berfungsi, tapi datanya membingungkan.
- **Perbaikan:** Perbaiki nilai `slug` di database (Medkominfo → `medkominfo`, Internal → `internal`) dan perbarui tautannya; jangan mengandalkan slug yang tertukar.

### 6.6 — Typo pada tautan LinkedIn
- **Severity:** Rendah
- **Bukti:** Footer menautkan `https://id.linkedin.com/company/himpunan-mahasiswa-teknik-pertambagan-itera-hmta-itera` — tertulis **"pertambagan"** (huruf "n" hilang). LinkedIn mengembalikan 403 (anti-bot) sehingga keberadaan halaman di slug itu tidak bisa diverifikasi dari sini.
- **Perbaikan:** Perbaiki slug menjadi `...-teknik-pertambangan-itera-...` dan verifikasi manual dengan membuka tautannya di browser.

---

## 7. Konten & struktur situs

Halaman publik yang ditemukan: Beranda (`/`), `pages/profile.php`, `pages/struktur.php`, `pages/samagri.php` (kabinet), `pages/annual.php`, `pages/annual/minerfesto.php`, `pages/calendar.php`, `pages/gallery.php`, `pages/materi.php` (MIROTA, terkunci login), `pages/past.php`, `pages/upcoming.php`, `pages/department.php?slug=…`, `pages/login.php`.

### 7.1 — Email kontak di footer salah tulis
- **Severity:** Tinggi
- **Bukti:** Footer di **semua halaman** menampilkan `hmta.balakosa.itera.com` sebagai teks kontak, dan tautannya `mailto:hmta.balakosa.itera.com` — itu bukan alamat email (tidak ada `@`). Alamat yang benar adalah `hmta.balakosa.itera@gmail.com`.
- **Dampak:** Pengunjung yang mengeklik "email" akan membuka klien email dengan alamat tujuan yang tidak valid; surel tidak akan terkirim.
- **Perbaikan:** Ganti teks dan `href` di template footer menjadi `hmta.balakosa.itera@gmail.com`.

### 7.2 — Kepengurusan kedaluwarsa satu kabinet
- **Severity:** Sedang
- **Bukti:** Halaman kabinet (`samagri.php`) menampilkan **METANOIA, Periode: 2024-2025**, dengan **Ketua Himpunan: Johan Hezekiel Marolop Ambarita**. Sementara itu Ketua HMTA saat ini (per SK 079/080 tahun 2026 dan proposal dana domain) adalah **Abdurrahman Zidan Rizki**. Homepage masih menampilkan H2 **"Kabinet Samagri 2024-2025"** — nama kabinet yang berbeda dari halaman kabinet (METANOIA).
- **Dampak:** Pengunjung mendapat info pengurus yang sudah tidak berlaku; inkonsistensi nama kabinet (Samagri vs METANOIA) membingungkan.
- **Perbaikan:** Perbarui data kabinet aktif (nama, periode 2025-2026/2026-2027, ketua, dan jajaran) lewat Dashboard Admin, dan samakan penyebutan nama kabinet di homepage.

### 7.3 — Dua kabinet mengklaim periode yang sama
- **Severity:** Sedang
- **Bukti:** Halaman struktur (`struktur.php`) menampilkan **METANOIA 2024-2025** sebagai "Kabinet Aktif" dan juga **Samagri 2024-2025** sebagai entri terpisah. Semua kabinet (2020-2021 Pionir, 2021-2022 Reformasi, 2022-2023 Sradhakorsa, 2023-2024 Radiant, 2024-2025 METANOIA & Samagri) berlabel "Deskripsi belum tersedia" dan "— Anggota".
- **Perbaikan:** Tentukan satu kabinet per periode (kemungkinan Samagri adalah nama lama/pendahulu METANOIA — hapus duplikatnya), isi deskripsi dan jumlah anggota tiap kabinet.

### 7.4 — Pendaftaran Minerfesto 2025 masih dipajang padahal sudah lewat
- **Severity:** Sedang
- **Bukti:** `pages/annual/minerfesto.php` menampilkan blok "Pendaftaran Minerfesto 2025 — Daftar sekarang! Tanggal Pelaksanaan 15-20 September 2025, Pendaftaran Dibuka 1 Juni - 31 Agustus 2025". Kontak panitia memakai placeholder `0821-XXXX-XXXX` (telepon dan WhatsApp).
- **Dampak:** Calon peserta diarahkan mendaftar ke acara yang sudah selesai 2 bulan lalu; nomor kontak tidak bisa dihubungi.
- **Perbaikan:** Ganti blok pendaftaran dengan status "Minerfesto 2025 telah selesai" + tautan dokumentasi, atau siapkan halaman Minerfesto 2026. Isi nomor kontak panitia yang sebenarnya (atau hapus barisnya).

### 7.5 — Teks candaan di halaman Past Events
- **Severity:** Rendah
- **Bukti:** `pages/past.php` menampilkan acara **"Inagurasi"** dengan deskripsi **"Kegiatan inagur mueheheheheh"**.
- **Perbaikan:** Ganti dengan deskripsi acara yang sebenarnya atau hapus entrinya.

### 7.6 — Galeri kosong, Upcoming Events kosong
- **Severity:** Rendah
- **Bukti:** `pages/gallery.php`: **"Belum ada foto. Tambahkan dari Dashboard."** `pages/upcoming.php` dan blok homepage: **"Tidak ada event mendatang"**.
- **Perbaikan:** Unggah foto kegiatan lewat Dashboard Admin; isi agenda kegiatan terdekat (mis. Open Recruitment Balakosa III yang berjalan 6 Nov–3 Des 2026) agar halaman tidak terlihat mati.

### 7.7 — Kalender punya 2 acara, satu gambarnya URL localhost
- **Severity:** Sedang
- **Bukti:** Data acara di `pages/calendar.php` berisi 2 acara ("Inagurasi", "UAS"). Acara Inagurasi memakai `image_url` = `http://localhost/root/uploads/events/20251031_073833-6314ff-WhatsApp-Image-2025-08-18-at-21.42.31-1-.jpeg` — URL `localhost` yang tidak akan pernah bisa dimuat di produksi, sekaligus membocorkan path lokal (`/root/`).
- **Perbaikan:** Perbaiki data acara di database/admin: ganti `image_url` dengan path produksi (`/uploads/events/...`) dan pastikan file gambarnya benar-benar ada di server.

### 7.8 — Testimoni tampak seperti placeholder
- **Severity:** Rendah
- **Bukti:** Halaman `annual.php` menampilkan testimoni "Andi Pratama — Mahasiswa Teknik Pertambangan 2022", "Dina Safitri — Alumni Teknik Pertambangan 2019", "Budi Santoso — Pemenang Kompetisi Minerfesto 2023" — nama-nama generik; ketiga foto testimoni (`testimonial1/2/3.jpg`) semuanya 404 (lihat §8).
- **Perbaikan:** Ganti dengan testimoni asli anggota/alumni beserta foto, atau hapus bagian testimoni sampai ada data nyata.

### 7.9 — Inkonsistensi penomoran Minerfesto
- **Severity:** Rendah
- **Bukti:** Halaman Minerfesto menyebut "Sejak pertama kali diadakan pada tahun 2018", tapi linimasanya menulis "2022 Minerfesto I", "2023 Minerfesto VI", "2024 Minerfesto VII". Halaman annual menulis "2022 Minerfesto 1" dan "2024 Minerfesto III & Lustrum I".
- **Perbaikan:** Samakan penomoran edisi Minerfesto di semua halaman.

### 7.10 — Angka anggota tidak konsisten dengan dokumen resmi
- **Severity:** Rendah
- **Bukti:** Counter homepage memakai `data-target="200"` untuk "Anggota Aktif", sedangkan formulir K-EXPO 2026 yang diisi HMTA menulis **440 anggota aktif**.
- **Perbaikan:** Verifikasi angka yang benar (200 vs 440 — kemungkinan 200 = aktif, 440 = total terdaftar) dan samakan di website dan dokumen resmi.

### 7.11 — Teks maintenance kedaluwarsa di markup semua halaman
- **Severity:** Info
- **Bukti:** Setiap halaman memuat overlay tersembunyi: "Sedang Maintenance — Website HMTA ITERA sedang dalam proses pemeliharaan. Harap kembali beberapa saat lagi. Diperbarui: 2026-05-23T18:48:18+00:00 oleh superadmin". Overlay saat ini tidak aktif (class tanpa `active`), jadi tidak memengaruhi pengunjung.
- **Perbaikan:** Tidak mendesak; perbarui/hapus teksnya saat maintenance berikutnya agar tidak membingungkan.

### 7.12 — Typo "Pertambagan" di konten
- **Severity:** Rendah
- **Bukti:** Halaman annual menulis "5 tahun Program Studi Teknik **Pertambagan**" (huruf "n" hilang). Typo yang sama ada di slug LinkedIn (§6.6).
- **Perbaikan:** Perbaiki menjadi "Pertambangan" di kedua tempat.

---

## 8. Tautan & aset rusak (hasil spider)

Spider memeriksa **65 URL internal** yang ditemukan di semua halaman (tautan + `src` gambar/CSS/JS). Hasil: **38× 200, 1× 302 (by design), 26× 404 (40%)**.

### 8.1 — 26 URL internal mengembalikan 404
- **Severity:** Tinggi
- **Bukti (lengkap):**
  - 6 logo departemen: `uploads/departments/20251212_142538-e8a596-DEPARTEMEN-PSDA.LOGO.png`, `...142600...-KEILMUAN.LOGO.png`, `...142640...-EKSTERNAL.LOGO.png`, `...142653...-INTERNAL.LOGO.png`, `...142708...-KADERISASI.LOGO.png`, `...142721...-Logo-Departemen-Medkomvis.jpg`
  - 5 logo/foto kabinet: `uploads/kabinet/20251212_143631-84c65a-Logo-Ketua-Himpunan.jpg`, `20251213_022919-c3cea0-IMG_1381.png`, `20251213_023049-a336e7-IMG_1381.png`, `20251213_023118-554f17-IMG_1381.png`, `20251213_023230-c683f3-logo-kabinet.png`
  - 8 foto organisasi: `uploads/org/20251215_135155-…-WhatsApp-Image-2025-12-15-at-20.51.10.jpeg`, `...140049…-20.57.26.jpeg`, `...140221…-20.59.09.jpeg`, `...140256…-20.55.39.jpeg`, `...140323…-20.56.27.jpeg`, `...140811…-21.07.11.jpeg`, `...141920…-21.18.20.jpeg`, `20251219_105605-…-WhatsApp-Image-2025-12-19-at-17.35.28.jpeg`
  - Gambar statis: `Resource/hmta-group.jpg`, `Resource/img/minerfesto.jpg`, `Resource/lustrum.jpg`, `Resource/testimonial1.jpg`, `Resource/testimonial2.jpg`, `Resource/testimonial3.jpg`
  - Halaman: `pages/lustrum.php` (ditautkan dari `annual.php` — "Selengkapnya" Lustrum)
  - Verifikasi ulang salah satu URL (`DEPARTEMEN-PSDA.LOGO.png`) mengonfirmasi 404 dengan halaman error Hostinger.
- **Dampak:** Logo semua departemen, foto kabinet, dan foto organisasi tampil sebagai gambar rusak di halaman departemen/struktur/kabinet; testimoni dan kartu Lustrum di halaman annual juga kehilangan gambar. Ini kerusakan visual yang paling terlihat di seluruh situs — kemungkinan folder `uploads/` tidak ikut saat migrasi/deploy, atau file-file dihapus setelah entri databasenya dibuat (Desember 2025).
- **Perbaikan:** Periksa isi folder `uploads/` di File Manager Hostinger. Jika file masih ada tapi jalurnya berbeda, perbaiki path di database. Jika hilang, unggah ulang lewat Dashboard Admin (atau hapus referensinya dari database agar tidak menampilkan gambar rusak). Setelah itu jalankan ulang spider untuk memastikan 0× 404.

---

## 9. Aksesibilitas dasar

| Item | Hasil |
|---|---|
| Atribut `lang` | `lang="id"` ✓ |
| Viewport mobile | Ada ✓ |
| Alt text gambar | Hampir semua ada; **1 gambar tanpa alt** di `pages/gallery.php` |
| Label form | Form login punya `<label>` Username/Password ✓; keberadaan label pada form kontak Minerfesto belum terverifikasi dari markup yang diambil |
| H1 | **Tidak ada** di homepage dan `past.php` (lihat §6.1, §6.4) |
| Kontras | Teks isi `#333` di atas putih — memadai; tidak ada masalah kontras yang terlihat dari CSS |

- **Severity keseluruhan:** Rendah
- **Perbaikan:** Tambahkan H1 di homepage dan `past.php`, tambahkan `alt` pada gambar galeri, pastikan semua input form punya `<label>` yang terasosiasi.

---

## 10. Temuan lain

- **Mixed content (http) di semua URL internal:** Semua tautan dan aset internal ditulis absolut dengan `http://hmtaitera.com/...` (stylesheet, JS, gambar, preload banner, favicon). Saat ini diselamatkan oleh CSP `upgrade-insecure-requests`, tapi praktik ini rapuh — satu perubahan CSP bisa membuat situs memuat konten campuran. **Severity: Rendah.** Perbaikan: ubah semua URL internal menjadi `https://` atau relatif (`/Resource/...`).
- **Favicon 372 KB:** `Resource/img/IMG_1381.PNG` dipakai sebagai favicon — terlalu besar (lihat §5.2).
- **Tidak ada masalah lain yang teramati:** tidak ada `.env`/`.git` yang bisa diakses (§4), tidak ada lorem ipsum, tidak ada halaman error dengan stack trace selain warning di §3.5.

---

## 11. Audit panel admin (akun admin_1, 21 November 2026)

**Cakupan:** Login ke `https://hmtaitera.com/admin/` sebagai admin_1 (kredensial resmi pemilik situs), memeriksa dashboard dan seluruh 10 seksi pengelolaan (`manage.php?m=...`), lalu logout. Read-only: tidak ada tombol Edit/Hapus/Simpan yang diklik, tidak ada form yang dikirim (kecuali form login), tidak ada berkas yang diunggah, tidak ada password/pengaturan yang diubah. Logout berfungsi dan mengarah ke homepage publik. Akun admin utama ("admin", diduga superadmin) dan admin_2 **belum terverifikasi** — vault hanya menyimpan satu login per situs (admin_1). Dashboard menampilkan: 8 Pengurus, 6 Departemen, 16 Divisi, 12 Leader Dept, 30 Program, 170 Materi, 0 Foto, 2 Kegiatan, 903 Anggota; header "Status: Maintenance Parsial (2 halaman)", "Terakhir diubah: 2026-05-23T18:48:18+00:00 oleh superadmin". Tidak ada error/peringatan PHP di halaman admin mana pun.

### 11.1 — Penghapusan data lewat tautan GET tanpa token (CSRF)
- **Severity:** Tinggi
- **Bukti:** Tombol Hapus di semua tabel pengelolaan adalah tautan GET biasa, mis. `https://hmtaitera.com/admin/manage.php?m=kegiatan&del=3` dan `?m=pengurus&del=11` — hanya dijaga dialog `confirm()` JavaScript, tanpa token yang terlihat di URL. Pola yang sama di semua 10 seksi.
- **Dampak:** Siapa pun yang bisa membuat admin yang sedang login mengklik tautan jahat (mis. lewat gambar atau link di halaman lain) dapat menghapus data himpunan — pengurus, program, materi, kegiatan — tanpa konfirmasi server. Penghapusan tidak sengaja juga mudah terjadi.
- **Perbaikan:** Ubah semua aksi hapus menjadi POST dengan token CSRF, dan tambahkan pemeriksaan token di server. Jangan pernah memakai GET untuk aksi yang mengubah data.

### 11.2 — Password admin sangat lemah, tanpa CAPTCHA/rate-limit, tanpa fitur ganti password
- **Severity:** Tinggi
- **Bukti:** Ketiga akun admin memakai password yang sangat lemah dan mudah ditebak (pola umum). Form login di `https://hmtaitera.com/admin/login.php` tidak menampilkan CAPTCHA dan tidak ada pemberitahuan pembatasan laju/penguncian akun. Di dalam panel tidak ada fitur ganti password, tidak ada manajemen pengguna/peran — akun admin tidak dapat didaftar, ditambah, atau diubah dari panel (harus langsung lewat database).
- **Dampak:** Akun admin dapat dibobol dengan tebakan/brute force; setelah dibobol, pemilik tidak bisa mengganti password dari panel dan tidak bisa mencabut akun dari panel.
- **Perbaikan:** Ganti SEGERA ketiga password admin dengan password kuat yang unik (minimal 16 karakter, acak); tambahkan CAPTCHA dan rate-limit/lockout di form login (mis. maks 5 percobaan gagal lalu kunci 15 menit); tambahkan fitur ganti password dan manajemen pengguna di panel, atau dokumentasikan prosedur pergantian lewat database. (Nilai password tidak ditulis di laporan ini.)

### 11.3 — Halaman Anggota mengekspos nama + NIM 903 anggota dalam satu halaman
- **Severity:** Sedang
- **Bukti:** `?m=anggota` memuat SEMUA 903 baris (ID sampai 906) dalam satu halaman 283 KB, tanpa pagination. Sebagian besar baris hanya berisi Nama + NIM; Email, No. HP, Foto, Kabinet, Departemen, Divisi, Peran, dan Tanggal Bergabung kosong; ada tanggal gabung tidak valid `0000-00-00`; baris yang berisi data menampilkan ID angka mentah untuk Kabinet/Departemen/Divisi.
- **Dampak:** Setiap admin yang masuk dapat melihat dan menyalin daftar lengkap nama + NIM seluruh anggota sekaligus; halaman berat dan lambat dimuat.
- **Perbaikan:** Tambahkan pagination (mis. 50 baris/halaman) dengan pencarian/filter; batasi kolom sensitif sesuai kebutuhan peran; perbaiki data kosong dan tanggal `0000-00-00`.

### 11.4 — Unggahan berkas tanpa batasan jenis/ukuran yang terlihat
- **Severity:** Sedang
- **Bukti:** Unggahan foto/logo/gambar (pengurus, leaders, departemen, kabinet, kegiatan, foto) dan unggahan PDF multi-file (materi, berlabel "Upload File PDF") tidak menampilkan batasan jenis atau ukuran berkas di UI — tidak ada atribut `accept`, tidak ada catatan "hanya JPG/PDF, maks X MB".
- **Dampak:** Jika validasi hanya di sisi klien atau tidak ada, penyerang dengan akses admin dapat mengunggah berkas berbahaya (mis. shell PHP) ke server. Validasi sisi server tidak dapat diverifikasi dari UI — risiko ini belum terkonfirmasi, bukan terbukti aman.
- **Perbaikan:** Terapkan dan tampilkan batasan di server: whitelist ekstensi (JPG/PNG untuk gambar, PDF untuk materi), batas ukuran, simpan di luar `public_html` atau tolak eksekusi script di folder uploads (`.htaccess` `php_flag engine off`), dan ganti nama berkas saat disimpan.

### 11.5 — Tidak ada label peran dan tidak ada manajemen pengguna
- **Severity:** Sedang
- **Bukti:** Peran admin_1 tidak diberi label di mana pun di panel (hanya nama pengguna "admin_1" dan "Status: Normal"), tetapi admin_1 dapat mengakses seluruh 10 seksi — akses pengelolaan konten penuh. Dashboard menyebut "oleh superadmin", yang mengisyaratkan peran lebih tinggi, tetapi tidak ada kontrol maintenance atau label peran yang terlihat.
- **Dampak:** Tidak jelas siapa bisa apa; akun admin biasa tampaknya setara dengan superadmin dalam pengelolaan konten, dan tidak ada cara mencabut akses dari panel.
- **Perbaikan:** Tampilkan peran di header/sidebar; bedakan hak akses admin biasa vs superadmin (mis. admin biasa tidak boleh mengelola pengurus/kabinet); tambahkan halaman manajemen pengguna.

### 11.6 — Tautan HTTP campuran di dalam panel
- **Severity:** Sedang
- **Bukti:** "Lihat Website" mengarah ke `http://hmtaitera.com/` dan "Lupa password?" ke `http://hmtaitera.com/admin/forgot.php`, padahal panel dilayani melalui HTTPS.
- **Dampak:** Sesi admin dapat bocor lewat tautan HTTP; halaman "Lupa password?" juga belum diperiksa (hanya target tautannya yang dibaca).
- **Perbaikan:** Ubah semua tautan internal menjadi `https://` atau relatif; periksa alur forgot-password (token reset harus sekali pakai dan kedaluwarsa).

### 11.7 — Counter homepage publik tampil 0+ padahal dashboard menunjukkan angka sebenarnya
- **Severity:** Rendah
- **Bukti:** Setelah logout, homepage menampilkan semua counter "0+" (Anggota Aktif 0+, Event Tahunan 0+, Prestasi 0+, Tahun Berdiri 0), "Tidak ada event mendatang", dan hero image hilang — padahal dashboard admin menampilkan 903 anggota, 2 kegiatan, dsb. Catatan: counter memakai animasi `data-target`, jadi angka 0 bisa jadi hanya keadaan awal sebelum animasi berjalan; perlu verifikasi dengan menunggu animasi selesai.
- **Perbaikan:** Verifikasi apakah counter akhirnya menampilkan angka benar; jika tidak, perbaiki query/JS counter di homepage.

### 11.8 — Kualitas data di panel: slug salah, FK tampil sebagai angka, duplikat, typo
- **Severity:** Rendah
- **Bukti:** Slug departemen tidak konsisten/salah — "Keilmuan" (huruf kapital), "Ekster" (terpotong), "Internal" untuk Media Komunikasi Informasi, "intern" untuk Internal; kolom Departemen menampilkan ID angka mentah (mis. 9, 4, 8) di tabel Leaders, Programs, Divisi, dan Anggota; Deskripsi/Email/LinkedIn kosong hampir di semua baris; entri materi duplikat ("TA_K3 - TA20_K3_Yusup Roman" ID 186 & 177; "Silfia Ardhila" 185 & 176; "Abdul Aziz Nasution" 184 & 175); periode kabinet 2024-2025 duplikat untuk METANOIA (ID 6) dan Samagri (ID 1); jabatan "Sekretaris Jendral" (typo, tidak ada di dropdown); celah ID yang mengisyaratkan baris yang dihapus (pengurus 1–3, leaders 1–4, programs 1–2, divisi 21–22, kegiatan 1, materi 58–59 hilang).
- **Dampak:** Tampilan admin membingungkan; slug yang salah dapat merusak URL publik departemen (lihat §6.6 — `department.php?slug=Internal` menampilkan Medkominfo).
- **Perbaikan:** Perbaiki slug di database dan samakan dengan nama departemen; tampilkan nama (JOIN) bukan ID angka; hapus duplikat materi; perbaiki typo jabatan; isi deskripsi yang kosong atau sembunyikan field yang tidak dipakai.

---

## 12. Audit panel admin sebagai SUPERADMIN (21 November 2026)

**Cakupan:** Login ke `https://hmtaitera.com/admin/` sebagai superadmin (username `superadmin`, ID 4, peran `super_admin` di database; kredensial resmi pemilik situs), memeriksa dashboard dan 3 menu baru superadmin, lalu logout. Read-only: tidak ada data yang dibuat, diubah, atau dihapus. Satu klik tidak disengaja pada tombol "Simpan" di form Kelola Materi diblokir validasi browser (field Kategori wajib diisi), sehingga tidak ada yang terkirim. Tombol "Aktifkan Global", semua tautan Hapus, dan tombol Simpan di Maintenance/Impor tidak diklik. Akun admin_2 **belum terverifikasi**.

Dashboard sama persis dengan admin_1 (8 Pengurus, 6 Departemen, 16 Divisi, 12 Leader Dept, 30 Program, 170 Materi, 0 Foto, 2 Kegiatan, 903 Anggota) plus satu kartu tambahan "908 Pengguna". Sidebar superadmin menampilkan 16 item — baru dibanding admin_1 (13 item): **Impor Anggota, Kelola Pengguna, Maintenance Mode**. Label peran tidak konsisten: tidak ada di dashboard, "Super Admin" di halaman Impor Anggota, "super_admin" di tabel Kelola Pengguna. Sidebar juga tidak konsisten antar halaman (16 item di dashboard, 15 di halaman manage.php, 8 di halaman Impor Anggota).

### 12.1 — Impor anggota massal memakai password plaintext dari CSV
- **Severity:** Tinggi
- **Bukti:** `https://hmtaitera.com/admin/tools/import_members.php` — format CSV "No.;NIM;Nama;Password;Username", artinya password anggota diimpor dalam bentuk plaintext dari file CSV. Opsi tercentang: "Buat/Update akun Users" dan "Buat/Update data Members"; "Update password jika user sudah ada" TIDAK tercentang. Pilihan default Kabinet menampilkan periode 2024-2025 dua kali (Samagri dan METANOIA).
- **Dampak:** Password 903 anggota beredar sebagai teks biasa di file CSV dan kemungkinan tersimpan tanpa hashing yang benar di database; siapa pun yang mendapat file CSV atau akses baca database mendapat seluruh password anggota.
- **Perbaikan:** Hentikan impor password plaintext — buat password acak di server dan hash dengan `password_hash()` saat impor, atau kirim tautan set-password ke anggota; hapus file CSV impor setelah dipakai; audit apakah password di database tersimpan ter-hash (cek kolom password di tabel users).

### 12.2 — Bug dropdown peran + tidak ada perlindungan hapus diri
- **Severity:** Tinggi
- **Bukti:** `https://hmtaitera.com/admin/manage.php?m=users` — dropdown Role di form pengguna hanya berisi User dan Admin, TIDAK ada opsi super_admin. Saat membuka edit superadmin (`?m=users&id=4`), dropdown menampilkan "— Pilih —" (tidak terpilih), padahal peran di database adalah super_admin. Baris superadmin sendiri (ID 4) memiliki tombol Hapus, dan tidak ada perlindungan hapus diri yang terlihat.
- **Dampak:** Mengedit akun superadmin dari UI ini berisiko mengosongkan/menurunkan perannya; superadmin dapat menghapus akunnya sendiri dan mengunci seluruh himpunan keluar dari panel (tidak ada superadmin lain — hanya 1 super_admin dari 908 pengguna).
- **Perbaikan:** Tambahkan opsi super_admin ke dropdown (hanya boleh dipilih superadmin); cegah penurunan peran superadmin terakhir dan cegah hapus diri di server (bukan hanya di UI); tampilkan peringatan saat mengedit akun superadmin.

### 12.3 — Tidak ada token CSRF yang terlihat di halaman superadmin
- **Severity:** Sedang
- **Bukti:** Pencarian "csrf" kosong di Kelola Pengguna, Maintenance Mode, dan Impor Anggota; tidak ada hidden token yang tampil pada form. Dikombinasikan dengan hapus lewat GET (§11.1), pola ini konsisten dengan tidak adanya perlindungan CSRF. Keberadaan token di field tersembunyi tidak dapat dipastikan dari UI — belum terkonfirmasi, bukan terbukti aman.
- **Perbaikan:** Tambahkan token CSRF di semua form (termasuk Maintenance dan Impor) dan verifikasi di server; ubah aksi hapus menjadi POST (§11.1).

### 12.4 — Tautan PDF materi memakai HTTP + path uploads langsung dengan nama hash yang dapat ditebak
- **Severity:** Sedang
- **Bukti:** Tautan "Lihat" materi memakai `http://` (bukan https) ke `hmtaitera.com/uploads/materials/<hash-md5>.pdf` — jalur uploads langsung dengan nama file hash MD5.
- **Dampak:** Materi yang seharusnya tidak publik (semua baris materi teramati "Publik? Tidak", §11) tetap dapat diakses siapa pun yang menebak/mendapat URL-nya, tanpa login dan tanpa HTTPS.
- **Perbaikan:** Sajikan berkas materi lewat script PHP yang memeriksa login dan status publik, bukan tautan langsung ke folder uploads; pakai nama file acak yang tidak dapat ditebak; paksa HTTPS.

### 12.5 — 908 pengguna dimuat sekaligus tanpa pagination/pencarian
- **Severity:** Sedang
- **Bukti:** `?m=users` mengelola 908 akun (904 role user, 3 admin: admin ID 2, admin_1 ID 909, admin_2 ID 908; 1 super_admin: superadmin ID 4). Semua 908 baris dimuat dalam satu halaman tanpa paginasi/pencarian. Password hanya tampil sebagai •••••• (tidak terekspos). Tidak ditemukan menu 2FA/pengaturan keamanan.
- **Dampak:** Sama seperti halaman Anggota (§11.3) — halaman berat, sulit mencari akun, dan seluruh daftar pengguna terekspos ke admin mana pun.
- **Perbaikan:** Tambahkan pagination + pencarian berdasarkan username/peran; ini juga satu-satunya tempat ganti password yang ditemukan (form edit pengguna, "Kosongkan jika tidak ingin mengubah password") — pertimbangkan menu "Ubah Password" terpisah dan 2FA.

### 12.6 — Label peran dan sidebar tidak konsisten; typo di Maintenance Mode
- **Severity:** Rendah
- **Bukti:** Label peran: tidak ada di dashboard, "Super Admin" di Impor Anggota, "super_admin" di tabel pengguna. Sidebar: 16 item di dashboard, 15 di halaman manage.php (Lihat Website hilang, "Kelola Leader Departemen" disingkat "Kelola Leader"), 8 di halaman Impor Anggota. Halaman Maintenance Mode (`https://hmtaitera.com/admin/maintenance.php`, tanpa sidebar, status PARSIAL — 2 halaman dicentang: "Annual (daftar)" `/pages/annual.php` dan "Annual: Minerfesto" `/pages/annual/minerfesto.php`; Toggle Mode Global OFF; ada Mode Kustom wildcard dan daftar ±19 halaman serta tombol Simpan Pengaturan) menulis typo "Minerfesto" dan "samagri.php".
- **Perbaikan:** Samakan label peran ("Super Admin") dan sidebar di semua halaman (satu template sidebar); perbaiki typo; dokumentasikan halaman mana saja yang sedang maintenance parsial.

### 12.7 — Duplikat materi dan kategori tampil sebagai slug mentah
- **Severity:** Rendah
- **Bukti:** `?m=materi` — judul TA yang sama muncul dua kali ("TA_K3 Yusup Roman" ID 186 dan 177; "TA_K3 Silfia Ardhila" 185 dan 176; "TA_K3 Abdul Aziz" 184 dan 175). Kategori di tabel tampil sebagai slug mentah (laporan_ta, modul, ppt, sop, video), bukan label manusiawi seperti di form.
- **Perbaikan:** Hapus duplikat; tampilkan label kategori (JOIN/mapping) bukan slug; tambahkan validasi duplikat saat unggah.

---

## Prioritas perbaikan (Top 8)

1. **Pulihkan 26 aset yang 404 (§8.1 — Tinggi).** Periksa folder `uploads/` di Hostinger; unggah ulang logo departemen, foto kabinet, dan foto organisasi, atau bersihkan referensinya dari database. Ini kerusakan paling terlihat di situs.
2. **Ganti password admin dan perkuat login (§11.2 — Tinggi).** Ganti segera ketiga password admin dengan yang kuat dan unik; tambahkan CAPTCHA + rate-limit/lockout di form login; tambahkan fitur ganti password dan manajemen pengguna di panel.
3. **Ubah hapus dari GET menjadi POST + token CSRF (§11.1, §12.3 — Tinggi).** Semua aksi hapus harus POST dengan token CSRF yang diperiksa di server; jangan pernah memakai GET untuk aksi yang mengubah data. Sekalian tambahkan token CSRF di semua form admin (termasuk Maintenance dan Impor) dan form login/kontak.
4. **Hentikan impor password plaintext; perbaiki bug peran superadmin (§12.1, §12.2 — Tinggi).** Hash password saat impor (`password_hash()`), hapus file CSV setelah dipakai; tambahkan opsi super_admin ke dropdown peran, cegah penurunan peran dan hapus diri superadmin terakhir di server.
5. **Matikan `display_errors` dan perbaiki include footer Minerfesto (§3.5 — Tinggi).** Ubah path `../includes/footer.php` → `../../includes/footer.php` di `minerfesto.php` baris 356, dan set `display_errors = Off` di produksi agar path server tidak bocor lagi.
6. **Perbaiki email footer (§7.1 — Tinggi).** Ganti `hmta.balakosa.itera.com` → `hmta.balakosa.itera@gmail.com` pada teks dan `mailto:` di template footer — saat ini pengunjung tidak bisa menghubungi himpunan lewat email.
7. **Perkuat header keamanan & cookie (§3.1–3.3 — Sedang).** Tambahkan HSTS, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`; sembunyikan `X-Powered-By`; set `HttpOnly` + `SameSite=Lax` pada cookie sesi; perbaiki tautan HTTP campuran di panel (§11.6) dan batasi unggahan berkas di server (§11.4); sajikan berkas materi lewat script ber-login, bukan tautan langsung (§12.4).
8. **Perbarui konten kedaluwarsa & SEO dasar (§7.2–7.4, §6.1–6.2 — Sedang).** Perbarui kabinet/ketua (METANOIA, Abdurrahman Zidan Rizki), turunkan pendaftaran Minerfesto 2025, perbaiki teks "mueheheheheh", lalu tambahkan H1 + canonical + Open Graph di semua halaman dan buat `robots.txt` + `sitemap.xml`. Bonus performa: kompres `banner1.png` (839 KB) dan logo (372 KB). Bonus data: tambahkan pagination di halaman Anggota dan Pengguna (§11.3, §12.5) dan bersihkan duplikat/slug (§11.8, §12.7).

---

*Daftar URL yang diperiksa: `/`, `pages/profile.php`, `pages/struktur.php`, `pages/samagri.php`, `pages/annual.php`, `pages/annual/minerfesto.php`, `pages/calendar.php`, `pages/gallery.php`, `pages/materi.php`, `pages/past.php`, `pages/upcoming.php`, `pages/department.php?slug=Ekster|Internal|Keilmuan|intern|kaderisasi|psda`, `pages/login.php`, `/admin`, `/admin/`, `/.env`, `/.git/HEAD`, `/.htaccess`, `/phpinfo.php`, `/config.php`, `/backup.zip`, `/wp-admin`, `/wp-login.php`, `/robots.txt`, `/sitemap.xml`, plus 65 URL internal hasil spider dan 4 tautan sosial media. Panel admin (§11–§12): `/admin/`, `/admin/login.php`, `/admin/manage.php?m=pengurus|leaders|programs|departemen|divisi|kabinet|anggota|foto|materi|kegiatan|users`, `/admin/maintenance.php`, `/admin/tools/import_members.php` — diperiksa sebagai admin_1 dan superadmin (read-only).*
