<?php
// File berada di pages/departemensamagri/, naik 2 level ke root
require_once dirname(__DIR__, 2) . '/includes/functions.php';

$page_title     = 'Departemen Media Informasi Komunikasi';

// Pastikan nama CSS sesuai file yang ada. Saya gunakan medkom.css (lihat file CSS di bawah).
$additional_css = ['medkom.css'];

// Muat JS yang diperlukan:
// - mobile_menu.js: animasi on scroll + mobile nav
// - department.js: counter, filter divisi, toggle konten
// - medkom-init.js: jaring pengaman bila animasi/JS lain gagal (paksa tampilkan [data-aos] dan buka konten divisi)
$additional_js  = [ 'department.js', 'medkom-init.js'];

include dirname(__DIR__, 2) . '/includes/header.php';
?>

<main>
    <!-- Department Header -->
    <section class="department-header">
        <div class="container">
            <div class="header-content">
                <img src="<?php echo BASE_URL; ?>Resource/logo kabinet/Logo Departemen Medkomvis.jpg"
                     alt="Logo Departemen Medkomvis"
                     class="dept-logo"
                     onerror="this.src='https://via.placeholder.com/120?text=MEDKOM'">
                <h1>Departemen Media Informasi Komunikasi</h1>
                <p>Menyebarkan Informasi dan Membangun Komunikasi yang Efektif</p>
            </div>
        </div>
    </section>

    <!-- Visi & Misi -->
    <section class="department-mission">
        <div class="container">
            <h2><i class="fas fa-bullseye"></i> Visi & Misi</h2>
            <div class="mission-content">
                <div class="mission-box" data-aos="fade-up">
                    <h3><i class="fas fa-eye"></i> Visi</h3>
                    <p>Menjadi departemen yang unggul dalam mengelola media dan komunikasi HMTA secara kreatif dan inovatif.</p>
                </div>
                <div class="mission-box" data-aos="fade-up" data-aos-delay="100">
                    <h3><i class="fas fa-tasks"></i> Misi</h3>
                    <ul>
                        <li>Mengelola media sosial dan publikasi HMTA</li>
                        <li>Mendokumentasikan seluruh kegiatan organisasi</li>
                        <li>Menyebarkan informasi secara efektif</li>
                        <li>Membangun branding HMTA yang positif</li>
                        <li>Mengembangkan konten kreatif dan edukatif</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Tanggung Jawab -->
    <section class="responsibilities">
        <div class="container">
            <h2><i class="fas fa-clipboard-list"></i> Tanggung Jawab</h2>
            <div class="responsibilities-grid">
                <div class="responsibility-card" data-aos="fade-up">
                    <div class="icon-box"><i class="fas fa-camera"></i></div>
                    <h3>Dokumentasi</h3>
                    <p>Mendokumentasikan seluruh kegiatan HMTA dalam bentuk foto dan video.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-box"><i class="fas fa-bullhorn"></i></div>
                    <h3>Publikasi</h3>
                    <p>Mempublikasikan informasi dan kegiatan HMTA melalui berbagai media.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-box"><i class="fas fa-palette"></i></div>
                    <h3>Desain Grafis</h3>
                    <p>Membuat desain poster, banner, dan konten visual untuk kegiatan HMTA.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-box"><i class="fas fa-hashtag"></i></div>
                    <h3>Social Media</h3>
                    <p>Mengelola dan mengembangkan media sosial HMTA secara profesional.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pimpinan Inti -->
    <section class="team-section">
        <div class="container">
            <h2><i class="fas fa-users"></i> Pimpinan Inti</h2>
            <div class="team-grid">
                <div class="team-card" data-aos="zoom-in">
                    <div class="team-image">
                        <img src="<?php echo BASE_URL; ?>Resource/leader-ketua.jpg" alt="Kepala Departemen Medkomvis"
                             onerror="this.src='https://via.placeholder.com/600x400?text=Kepala+Departemen'">
                        <div class="team-overlay">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h3>Rina Wijayanti</h3>
                        <p class="position">Kepala Departemen Medkomvis</p>
                        <p class="description">Memimpin strategi media dan komunikasi HMTA</p>
                    </div>
                </div>

                <div class="team-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="team-image">
                        <img src="<?php echo BASE_URL; ?>Resource/leader-wakil.jpg" alt="Wakil Kepala Departemen"
                             onerror="this.src='https://via.placeholder.com/600x400?text=Wakil+Departemen'">
                        <div class="team-overlay">
                            <div class="social-links">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h3>Bayu Saputra</h3>
                        <p class="position">Wakil Kepala Departemen</p>
                        <p class="description">Koordinasi konten dan publikasi media sosial</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Kerja -->
    <section class="programs-section">
        <div class="container">
            <h2><i class="fas fa-rocket"></i> Program Kerja</h2>
            <div class="programs-grid">
                <div class="program-card" data-aos="fade-right">
                    <div class="program-icon"><i class="fas fa-video"></i></div>
                    <h3>Content Creation</h3>
                    <p>Produksi konten video dan artikel edukatif tentang pertambangan untuk media sosial.</p>
                    <span class="program-frequency">Mingguan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="100">
                    <div class="program-icon"><i class="fas fa-newspaper"></i></div>
                    <h3>Newsletter</h3>
                    <p>Publikasi newsletter bulanan tentang kegiatan dan informasi terkini HMTA.</p>
                    <span class="program-frequency">Bulanan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="200">
                    <div class="program-icon"><i class="fas fa-podcast"></i></div>
                    <h3>HMTA Podcast</h3>
                    <p>Podcast yang membahas topik pertambangan dan pengalaman mahasiswa.</p>
                    <span class="program-frequency">Bulanan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="300">
                    <div class="program-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Social Media Campaign</h3>
                    <p>Kampanye media sosial untuk meningkatkan engagement dan awareness HMTA.</p>
                    <span class="program-frequency">Berkelanjutan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Divisi & Anggota -->
    <section class="department-divisions">
        <div class="section-division">
            <h2><i class="fa-solid fa-people-group"></i> Divisi & Anggota</h2>
        </div>

        <div class="division-filter">
            <button class="filter-btn active" data-filter="all">Semua Divisi</button>
            <button class="filter-btn" data-filter="kreatif">Media Kreatif</button>
            <button class="filter-btn" data-filter="humas">Hubungan Masyarakat</button>
            <button class="filter-btn" data-filter="jurnal">Jurnalistik</button>
        </div>

        <div class="divisions-container">
            <!-- Media Kreatif -->
            <div class="division-card" data-category="kreatif">
                <div class="division-header">
                    <div class="division-icon"><i class="fas fa-palette"></i></div>
                    <div class="division-title">
                        <h3>Divisi Media Kreatif</h3>
                        <span class="member-count">6 Anggota</span>
                    </div>
                    <button class="toggle-btn" data-target="kreatif"><i class="fas fa-chevron-down"></i></button>
                </div>

                <div class="division-content" id="kreatif" data-aos="fade-up">
                    <p class="division-description">
                        Bertanggung jawab atas pembuatan konten visual, desain grafis, video, dan seluruh kebutuhan publikasi kreatif himpunan.
                    </p>

                    <div class="division-leadership-info">
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/dc3545/white?text=KD" alt="Kepala Divisi">
                            <div><h5>Reza Pratama</h5><span>Kepala Divisi</span></div>
                        </div>
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/ffc107/white?text=SD" alt="Sekretaris Divisi">
                            <div><h5>Maya Sari</h5><span>Sekretaris Divisi</span></div>
                        </div>
                    </div>

                    <div class="staff-section">
                        <h4>Anggota Staff</h4>
                        <div class="staff-grid">
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Ahmad Fauzi</h6><span>Graphic Designer</span></div></div>
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Putri Handayani</h6><span>Video Editor</span></div></div>
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Dimas Pratama</h6><span>UI/UX Designer</span></div></div>
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Sari Melati</h6><span>Content Creator</span></div></div>
                        </div>
                    </div>

                    <div class="division-projects">
                        <h4>Project Terkini</h4>
                        <div class="project-tags">
                            <span class="project-tag">Rebranding HMTA</span>
                            <span class="project-tag">Video Profile 2024</span>
                            <span class="project-tag">Social Media Campaign</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Humas -->
            <div class="division-card" data-category="humas">
                <div class="division-header">
                    <div class="division-icon"><i class="fas fa-handshake"></i></div>
                    <div class="division-title">
                        <h3>Divisi Hubungan Masyarakat</h3>
                        <span class="member-count">5 Anggota</span>
                    </div>
                    <button class="toggle-btn" data-target="humas"><i class="fas fa-chevron-down"></i></button>
                </div>

                <div class="division-content" id="humas" data-aos="fade-up">
                    <p class="division-description">
                        Menjadi jembatan komunikasi antara HMTA dengan pihak eksternal untuk membangun relasi yang kuat dan berkelanjutan.
                    </p>

                    <div class="division-leadership-info">
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/17a2b8/white?text=KD" alt="Kepala Divisi">
                            <div><h5>Indira Salsabila</h5><span>Kepala Divisi</span></div>
                        </div>
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/6f42c1/white?text=SD" alt="Sekretaris Divisi">
                            <div><h5>Bayu Saputra</h5><span>Sekretaris Divisi</span></div>
                        </div>
                    </div>

                    <div class="staff-section">
                        <h4>Anggota Staff</h4>
                        <div class="staff-grid">
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Rani Permata</h6><span>External Relations</span></div></div>
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Fajar Ramadhan</h6><span>Partnership Manager</span></div></div>
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Dewi Lestari</h6><span>Event Coordinator</span></div></div>
                        </div>
                    </div>

                    <div class="division-projects">
                        <h4>Project Terkini</h4>
                        <div class="project-tags">
                            <span class="project-tag">MOU Universitas</span>
                            <span class="project-tag">Alumni Networking</span>
                            <span class="project-tag">Industry Visit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jurnalistik -->
            <div class="division-card" data-category="jurnal">
                <div class="division-header">
                    <div class="division-icon"><i class="fas fa-camera"></i></div>
                    <div class="division-title">
                        <h3>Divisi Jurnalistik & Dokumentasi</h3>
                        <span class="member-count">4 Anggota</span>
                    </div>
                    <button class="toggle-btn" data-target="jurnal"><i class="fas fa-chevron-down"></i></button>
                </div>

                <div class="division-content" id="jurnal" data-aos="fade-up">
                    <p class="division-description">
                        Meliput, mendokumentasikan, dan mempublikasikan setiap kegiatan himpunan dalam bentuk tulisan, foto, dan video.
                    </p>

                    <div class="division-leadership-info">
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/fd7e14/white?text=KD" alt="Kepala Divisi">
                            <div><h5>Andi Setiawan</h5><span>Kepala Divisi</span></div>
                        </div>
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/20c997/white?text=SD" alt="Sekretaris Divisi">
                            <div><h5>Fitri Ramadhani</h5><span>Sekretaris Divisi</span></div>
                        </div>
                    </div>

                    <div class="staff-section">
                        <h4>Anggota Staff</h4>
                        <div class="staff-grid">
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Rizki Maulana</h6><span>Journalist</span></div></div>
                            <div class="staff-member"><img src="https://placehold.co/60x60" alt=""><div class="staff-info"><h6>Sinta Dewi</h6><span>Photographer</span></div></div>
                        </div>
                    </div>

                    <div class="division-projects">
                        <h4>Project Terkini</h4>
                        <div class="project-tags">
                            <span class="project-tag">Newsletter Bulanan</span>
                            <span class="project-tag">Event Documentation</span>
                            <span class="project-tag">Student Stories</span>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.divisions-container -->
    </section>
</main>

<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>