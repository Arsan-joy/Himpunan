<?php
require_once dirname(__DIR__, 2) . '/includes/functions.php';
$pageTitle = "Departemen Eksternal";
$additional_css = ['eksternal.css'];
$additional_js  = [ 'department.js', 'medkom-init.js'];
require_once dirname(__DIR__, 2) . '/includes/header.php';
?>

<main>
    <!-- Department Header -->
    <section class="department-header">
        <div class="container">
            <div class="header-content">
               <img src="<?php echo getBaseUrl(); ?>Resource/DEPARTEMEN EKSTERNAL.LOGO" 
               alt="Departemen Eksternal"
               class="dept-logo"
               onerror="this.src='https://placehold.co/100x100/6c757d/white?text=No+Image'">
            </div>
            <h1>Departemen Eksternal</h1>
            <p>Departemen Eksternal bertanggung jawab dalam menjalin hubungan dan kerjasama dengan pihak eksternal, seperti organisasi mahasiswa lain, perusahaan, dan komunitas luar kampus. Departemen ini juga mengelola komunikasi eksternal serta mempromosikan kegiatan himpunan ke publik.</p>
        </div>
    </section>

    <!--Departement Mission-->
     <section class="department-mission">
        <div class="container">
            <h2><i class="fas fa-bullseye"></i> Visi & Misi</h2>
            <div class="mission-content">
                <div class="mission-box">
                    <h3><i class="fas fa-eye"></i> Visi</h3>
                    <p>Menjadikan Departemen Eksternal sebagai jembatan komunikasi dan kerjasama HMTA ITERA dengan dunia luar untuk mencapai prestasi dan pengembangan optimal.</p>
                </div>
                <div class="mission-box">
                    <h3><i class="fas fa-tasks"></i> Misi</h3>
                    <ul>
                        <li>Membangun kerjasama dengan industri pertambangan</li>
                        <li>Menjalin hubungan dengan organisasi eksternal</li>
                        <li>Mengembangkan networking profesional</li>
                        <li>Mewakili HMTA dalam kegiatan eksternal</li>
                        <li>Mencari sponsor dan dukungan untuk kegiatan</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Responsibilities Section -->
    <section class="responsibilities">
        <div class="container">
            <h2><i class="fas fa-clipboard-list"></i> Tanggung Jawab</h2>
            <div class="responsibilities-grid">
                <div class="responsibility-card" data-aos="fade-up">
                    <div class="icon-box">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Partnership & Kerjasama</h3>
                    <p>Membangun dan menjaga hubungan kerjasama dengan industri, alumni, dan organisasi luar.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-box">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Public Relations</h3>
                    <p>Mengelola citra dan reputasi HMTA di mata publik serta stakeholder eksternal.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-box">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Networking</h3>
                    <p>Mengembangkan jaringan profesional untuk membuka peluang bagi anggota HMTA.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-box">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Sponsorship</h3>
                    <p>Mencari dan mengelola sponsorship untuk mendukung kegiatan dan program HMTA.</p>
                </div>
            </div>
        </div>
    </section>

 <!-- Team Section -->
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
                        <p class="position">Kepala Departemen Kaderisasi</p>
                        <p class="description">Memimpin kaderisasi  HMTA</p>
                    </div>
                </div>

                <div class="team-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="team-image">
                        <img src="<?php echo BASE_URL; ?>Resource/leader-sekretaris.jpg" alt="Sekretaris Departemen Kaderisasi"
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
                        <p class="position">Sekretaris Departemen Kaderisasi</p>
                        <p class="description">Koordinasi Surat Menyurat Departemen Kaderisasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Programs Section -->
    <section class="programs-section">
        <div class="container">
            <h2><i class="fas fa-rocket"></i> Program Kerja</h2>
            <div class="programs-grid">
                <div class="program-card" data-aos="fade-right">
                    <div class="program-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Industry Visit</h3>
                    <p>Kunjungan industri ke perusahaan pertambangan untuk memberikan wawasan praktis kepada anggota.</p>
                    <span class="program-frequency">Semester</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="100">
                    <div class="program-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Alumni Gathering</h3>
                    <p>Pertemuan dengan alumni HMTA untuk berbagi pengalaman dan membuka peluang networking.</p>
                    <span class="program-frequency">Tahunan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="200">
                    <div class="program-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>MoU Partnership</h3>
                    <p>Penandatanganan kerjasama dengan perusahaan dan organisasi untuk mendukung program HMTA.</p>
                    <span class="program-frequency">Berkelanjutan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="300">
                    <div class="program-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Competition Support</h3>
                    <p>Dukungan sponsorship dan pendampingan untuk tim yang mengikuti kompetisi nasional/internasional.</p>
                    <span class="program-frequency">Insidental</span>
                </div>
            </div>
        </div>
    </section>

     <!-- Mitra Kerjasama -->
    <section class="partners-section">
        <div class="container">
            <div class="partners-title">
                <i class="fas fa-building"></i>
                <h2>Mitra Kerjasama</h2>
            </div>

            <div class="partners-grid">
                <article class="partner-card" data-aos="fade-up">
                    <div class="partner-logo"><i class="fas fa-industry"></i></div>
                    <h3>PT Bukit Asam Tbk</h3>
                    <p>Kerjasama dalam kunjungan industri dan seminar</p>
                </article>

                <article class="partner-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="partner-logo"><i class="fas fa-oil-can"></i></div>
                    <h3>PT Freeport Indonesia</h3>
                    <p>Program magang dan workshop profesional</p>
                </article>

                <article class="partner-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="partner-logo"><i class="fas fa-gem"></i></div>
                    <h3>PT Antam Tbk</h3>
                    <p>Sponsorship kegiatan dan career development</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Divisi & Anggota -->
    <section class="department-divisions">
        <div class="container">
            <div class="section-header">
            <h2><i class="fa-solid fa-people-group"></i> Divisi & Anggota</h2>
            </div>
        </div>  

        <div class="division-filter">
            <button class="filter-btn active" data-filter="all">Semua Divisi</button>
            <button class="filter-btn" data-filter="Konseptor">Konseptor</button>
            <button class="filter-btn" data-filter="Eksekutor">Eksekutor</button>
        </div>

        <div class="divisions-container">
            <!-- Konseptor -->
            <div class="division-card" data-category="Konseptor">
                <div class="division-header">
                    <div class="division-icon"><i class="fa-solid fa-compass-drafting"></i></div>
                    <div class="division-title">
                        <h3>Divisi Konseptor</h3>
                        <span class="member-count">6 Anggota</span>
                    </div>
                    <button class="toggle-btn" data-target="Konseptor"><i class="fas fa-chevron-down"></i></button>
                </div>

                <div class="division-content" id="Konseptor" data-aos="fade-up">
                    <p class="division-description">
                        Bertanggung jawab atas pembuatan konsep dan perencanaan program kaderisasi Himpunan.
                    </p>

                    <div class="division-leadership-info">
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/dc3545/white?text=KD" alt="Kepala Divisi">
                            <div><h5>Reza Pratama</h5><span>Kepala Divisi</span></div>
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

            <!-- Eksekutor -->
            <div class="division-card" data-category="eksekutor">
                <div class="division-header">
                    <div class="division-icon"><i class="fa-solid fa-wrench"></i></div>
                    <div class="division-title">
                        <h3>Divisi eksekutor</h3>
                        <span class="member-count">4 Anggota</span>
                    </div>
                    <button class="toggle-btn" data-target="eksekutor"><i class="fas fa-chevron-down"></i></button>
                </div>

                <div class="division-content" id="eksekutor" data-aos="fade-up">
                    <p class="division-description">
                        Melaksanakan setiap program yang dirancang oleh divisi konseptor dengan efektif dan efisien.
                    </p>

                    <div class="division-leadership-info">
                        <div class="leader-info">
                            <img src="https://placehold.co/50x50/fd7e14/white?text=KD" alt="Kepala Divisi">
                            <div><h5>Andi Setiawan</h5><span>Kepala Divisi</span></div>
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

<?php require_once dirname(__DIR__, 2) . '/includes/footer.php'; ?>