<?php
require_once dirname(__DIR__, 2) . '/includes/functions.php';

$page_title     = 'Departemen Kaderisasi';

// Pastikan nama CSS sesuai file yang ada. Saya gunakan kaderisasi.css (lihat file CSS di bawah).
$additional_css = ['kaderisasi.css'];

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
                <img src="<?php echo BASE_URL; ?>Resource/logo kabinet/DEPARTEMEN KADERISASI.LOGO.png"
                     alt="Logo Departemen Kaderisasi"
                     class="dept-logo"
                     onerror="this.src='https://via.placeholder.com/120?text=KADERISASI'">
                <h1>Departemen Kaderisasi</h1>
                <p>Membentuk Kader HMTA yang Berkualitas dan Berdedikasi</p>
            </div>
        </div>
    </section>

    <!-- Department Mission -->
    <section class="department-mission">
        <div class="container">
            <h2><i class="fas fa-bullseye"></i> Visi & Misi</h2>
            <div class="mission-content">
                <div class="mission-box">
                    <h3><i class="fas fa-eye"></i> Visi</h3>
                    <p>Menjadikan Departemen Kaderisasi sebagai wadah pembentukan kader HMTA yang memiliki integritas, kompetensi, dan leadership yang kuat.</p>
                </div>
                <div class="mission-box">
                    <h3><i class="fas fa-tasks"></i> Misi</h3>
                    <ul>
                        <li>Menyelenggarakan program regenerasi organisasi</li>
                        <li>Membina dan mengembangkan karakter kader</li>
                        <li>Meningkatkan kapasitas kepemimpinan anggota</li>
                        <li>Menanamkan nilai-nilai organisasi</li>
                        <li>Membangun mental dan attitude yang baik</li>
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
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Pelatihan Kader</h3>
                    <p>Menyelenggarakan pelatihan dan workshop untuk meningkatkan kompetensi kader HMTA.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-box">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>Regenerasi Organisasi</h3>
                    <p>Mengatur dan memfasilitasi proses regenerasi kepemimpinan organisasi HMTA.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-box">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Mentoring & Coaching</h3>
                    <p>Memberikan bimbingan dan pendampingan kepada kader baru HMTA.</p>
                </div>
                <div class="responsibility-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-box">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Pembinaan Karakter</h3>
                    <p>Membangun karakter dan mental yang tangguh bagi kader HMTA.</p>
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
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Latihan Kader</h3>
                    <p>Program pelatihan intensif untuk membentuk kader yang berkualitas dan memahami nilai-nilai HMTA.</p>
                    <span class="program-frequency">Tahunan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="100">
                    <div class="program-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Mentoring Program</h3>
                    <p>Pendampingan kader baru oleh senior untuk berbagi pengalaman dan pengetahuan.</p>
                    <span class="program-frequency">Berkelanjutan</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="200">
                    <div class="program-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Leadership Training</h3>
                    <p>Pelatihan kepemimpinan untuk mempersiapkan calon pemimpin HMTA masa depan.</p>
                    <span class="program-frequency">Semester</span>
                </div>
                <div class="program-card" data-aos="fade-right" data-aos-delay="300">
                    <div class="program-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>Character Building</h3>
                    <p>Program pembangunan karakter melalui kegiatan outbound dan pelatihan soft skills.</p>
                    <span class="program-frequency">Tahunan</span>
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
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>