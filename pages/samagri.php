<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Kabinet Samagri HMTA ITERA';
$additional_css = ['stylesamagri.css']; // pastikan file ada di folder css/
$additional_js  = [];                   // tambahkan jika ada JS khusus halaman ini, misal ['home2.js']

include __DIR__ . '/../includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Himpunan Mahasiswa Teknik Pertambangan ITERA</h1>
            <p>Membangun Karakter Profesional dan Berkompeten dalam Bidang Pertambangan</p>
            <a href="<?php echo getBaseUrl(); ?>pages/struktur.php" class="btn-primary">Lihat Struktur Organisasi</a>
        </div>
    </section>

    <!-- Organization Overview -->
    <section class="org-overview">
        <div class="container">
            <h2>Tentang HMTA ITERA</h2>
            <div class="overview-content">
                <div class="overview-text">
                    <p>Himpunan Mahasiswa Teknik Pertambangan Institut Teknologi Sumatera (HMTA ITERA) merupakan organisasi kemahasiswaan yang menaungi seluruh mahasiswa teknik pertambangan ITERA. HMTA ITERA berfokus pada pengembangan akademik, profesionalisme, dan soft skill mahasiswa dalam bidang pertambangan.</p>
                    <p>Dengan visi menjadi organisasi kemahasiswaan yang profesional, inovatif, dan berkontribusi nyata untuk kemajuan industri pertambangan di Indonesia, HMTA ITERA terus berkarya melalui berbagai program kerja dan kegiatan.</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/profile.php" class="btn-secondary">Selengkapnya</a>
                </div>
                <div class="overview-image">
                    <img src="<?php echo getBaseUrl(); ?>Resource/hmta-group.jpg" alt="HMTA ITERA Group Photo" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default.jpg'">
                </div>
            </div>
        </div>
    </section>

    <!-- Current Cabinet -->
    <section class="current-cabinet">
        <div class="container">
            <h2>Kabinet Samagri</h2>
            <p class="cabinet-tagline">"Bersinergi dalam Keberagaman untuk Membangun Prestasi"</p>
            
            <div class="cabinet-leaders">
                <div class="leader-card">
                    <img src="<?php echo getBaseUrl(); ?>Resource/leader-ketua.jpg" alt="Ketua HMTA" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-profile.jpg'">
                    <h3>Ahmad Fadilah</h3>
                    <p>Ketua Senator HMTA</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/samagri.php#ketua" class="btn-tertiary">Profil</a>
                </div>
                <div class="leader-card">
                    <img src="<?php echo getBaseUrl(); ?>Resource/leader-wakil.jpg" alt="Wakil Ketua HMTA" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-profile.jpg'">
                    <h3>Putri Ramadhani</h3>
                    <p>Ketua Himpunan HMTA</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/samagri.php#wakil" class="btn-tertiary">Profil</a>
                </div>
                <div class="leader-card">
                    <img src="<?php echo getBaseUrl(); ?>Resource/leader-bendahara.jpg" alt="BPO" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-profile.jpg'">
                    <h3>Budi Santoso<br>Welly Hardianto</h3>
                    <p>Badan Penasihat Organisasi</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/samagri.php#bpo" class="btn-tertiary">Profil</a>
                </div>
                <div class="leader-card">
                    <img src="<?php echo getBaseUrl(); ?>Resource/leader-sekretaris.jpg" alt="Sekretaris Jendral" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-profile.jpg'">
                    <h3>Dewi Anggraini</h3>
                    <p>Sekretaris Jendral HMTA</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/samagri.php#sekjen" class="btn-tertiary">Profil</a>
                </div>
                <div class="leader-card">
                    <img src="<?php echo getBaseUrl(); ?>Resource/leader-bendahara.jpg" alt="Sekum" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-profile.jpg'">
                    <h3>Budi Santoso</h3>
                    <p>Sekretaris Umum HMTA</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/samagri.php#sekum" class="btn-tertiary">Profil</a>
                </div>
                <div class="leader-card">
                    <img src="<?php echo getBaseUrl(); ?>Resource/leader-bendahara.jpg" alt="Bendum" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-profile.jpg'">
                    <h3>Budi Santoso</h3>
                    <p>Bendahara Umum HMTA</p>
                    <a href="<?php echo getBaseUrl(); ?>pages/samagri.php#bendum" class="btn-tertiary">Profil</a>
                </div>
            </div>
            
            <div class="cabinet-departments">
                <h3>Departemen</h3>
                <div class="department-grid">
                    <a href="<?php echo getBaseUrl(); ?>pages/internal.php" class="department-card">
                        <img class="logo-kabinet" src="<?php echo getBaseUrl(); ?>Resource/logo kabinet/DEPARTEMEN INTERNAL.LOGO.png" alt="Departemen Internal" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-dept.png'">
                        <h4>Departemen Internal</h4>
                    </a>
                    <a href="<?php echo getBaseUrl(); ?>pages/external.php" class="department-card">
                        <img class="logo-kabinet" src="<?php echo getBaseUrl(); ?>Resource/logo kabinet/DEPARTEMEN EKSTERNAL.LOGO.png" alt="Departemen Eksternal" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-dept.png'">
                        <h4>Departemen Eksternal</h4>
                    </a>
                    <a href="<?php echo getBaseUrl(); ?>pages/kaderisasi.php" class="department-card">
                        <img class="logo-kabinet" s
                        rc="<?php echo getBaseUrl(); ?>Resource/logo kabinet/DEPARTEMEN KADERISASI.LOGO.png" alt="Departemen Kaderisasi" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-dept.png'">
                        <h4>Departemen Kaderisasi</h4>
                    </a>
                    <a href="<?php echo getBaseUrl(); ?>pages/psda.php" class="department-card">
                        <img class="logo-kabinet" src="<?php echo getBaseUrl(); ?>Resource/logo kabinet/DEPARTEMEN PSDA.LOGO.png" alt="Departemen PSDA" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-dept.png'">
                        <h4>Departemen Pengembangan Sumberdaya Anggota</h4>
                    </a>
                    <a href="<?php echo getBaseUrl(); ?>pages/keilmuan.php" class="department-card">
                        <img class="logo-kabinet" src="<?php echo getBaseUrl(); ?>Resource/logo kabinet/DEPARTEMEN KEILMUAN.LOGO.png" alt="Departemen Keilmuan" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-dept.png'">
                        <h4>Departemen Keilmuan</h4>
                    </a>
                    <a href="<?php echo getBaseUrl(); ?>pages/departemensamagri/medkom.php" class="department-card">
                        <img class="logo-kabinet" src="<?php echo getBaseUrl(); ?>Resource/logo kabinet/Logo Departemen Medkomvis.jpg" alt="Departemen Media Informasi Komunikasi" onerror="this.src='<?php echo getBaseUrl(); ?>Resource/default-dept.png'">
                        <h4>Departemen Media Informasi Komunikasi</h4>
                    </a>
                </div>
            </div>
            
            <div class="text-center">
                <a href="<?php echo getBaseUrl(); ?>pages/samagri.php" class="btn-primary">Lihat Struktur Lengkap</a>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="upcoming-events">
            <div class="text-center">
                <a href="<?php echo getBaseUrl(); ?>pages/upcoming.php" class="btn-secondary">Lihat Semua Event</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>