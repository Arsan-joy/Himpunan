<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Struktur Organisasi';
$additional_css = ['struktur.css'];       // pastikan ada di assets/css/struktur.css
$additional_js  = ['struktur.js'];        // pastikan ada di assets/js/struktur.js

include __DIR__ . '/../includes/header.php';
?>
<main>
    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <h1>Struktur Organisasi</h1>
            <p>Kabinet-kabinet yang telah memimpin HMTA ITERA dari masa ke masa</p>
            <div class="breadcrumb">
                <a href="<?php echo BASE_URL; ?>">Beranda</a>
                <i class="fas fa-chevron-right"></i>
                <span>Struktur</span>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="search-filter-section">
        <div class="container">
            <div class="search-filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari kabinet...">
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <button class="filter-btn" data-filter="current">Aktif</button>
                    <button class="filter-btn" data-filter="past">Sebelumnya</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Kabinet Cards Section -->
    <section class="kabinet-section">
        <div class="container">
            <div class="kabinet-grid" id="kabinetGrid">
                <!-- Current Active Cabinet -->
                <div class="kabinet-card current" data-year="2024" data-status="current">
                    <div class="kabinet-badge">Kabinet Aktif</div>
                    <div class="kabinet-image">
                        <img src="<?php echo IMG_URL; ?>samagri-logo.png"
                             onerror="this.src='<?php echo BASE_URL; ?>Resource/samagri-logo.png'"
                             alt="Kabinet Samagri">
                        <div class="kabinet-overlay">
                            <div class="kabinet-year">2024-2025</div>
                        </div>
                    </div>
                    <div class="kabinet-content">
                        <h3 class="kabinet-name">Kabinet Samagri</h3>
                        <p class="kabinet-description">
                            Kabinet yang fokus pada integrasi teknologi dan pengembangan sumber daya manusia dalam bidang pertambangan.
                        </p>
                        <div class="kabinet-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>25 Anggota</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>2024-2025</span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>pages/samagri.php" class="kabinet-btn">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Past Cabinets -->
                <div class="kabinet-card" data-year="2023" data-status="past">
                    <div class="kabinet-image">
                        <img src="<?php echo IMG_URL; ?>radiant-logo.png"
                             onerror="this.src='<?php echo BASE_URL; ?>Resource/radiant-logo.png'"
                             alt="Kabinet Radiant">
                        <div class="kabinet-overlay">
                            <div class="kabinet-year">2023-2024</div>
                        </div>
                    </div>
                    <div class="kabinet-content">
                        <h3 class="kabinet-name">Kabinet Radiant</h3>
                        <p class="kabinet-description">
                            Kabinet yang berfokus pada pencerahan dan inovasi dalam pengembangan organisasi mahasiswa.
                        </p>
                        <div class="kabinet-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>23 Anggota</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>2023-2024</span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>radiant.php" class="kabinet-btn">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="kabinet-card" data-year="2022" data-status="past">
                    <div class="kabinet-image">
                        <img src="<?php echo IMG_URL; ?>sradhakorsa-logo.png"
                             onerror="this.src='<?php echo BASE_URL; ?>Resource/sradhakorsa-logo.png'"
                             alt="Kabinet Sradhakorsa">
                        <div class="kabinet-overlay">
                            <div class="kabinet-year">2022-2023</div>
                        </div>
                    </div>
                    <div class="kabinet-content">
                        <h3 class="kabinet-name">Kabinet Sradhakorsa</h3>
                        <p class="kabinet-description">
                            Kabinet yang mengusung semangat gotong royong dan kerjasama dalam setiap kegiatan organisasi.
                        </p>
                        <div class="kabinet-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>22 Anggota</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>2022-2023</span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>Sradhakorsa.php" class="kabinet-btn">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="kabinet-card" data-year="2021" data-status="past">
                    <div class="kabinet-image">
                        <img src="<?php echo IMG_URL; ?>reformasi-logo.png"
                             onerror="this.src='<?php echo BASE_URL; ?>Resource/reformasi-logo.png'"
                             alt="Kabinet Reformasi">
                        <div class="kabinet-overlay">
                            <div class="kabinet-year">2021-2022</div>
                        </div>
                    </div>
                    <div class="kabinet-content">
                        <h3 class="kabinet-name">Kabinet Reformasi</h3>
                        <p class="kabinet-description">
                            Kabinet yang membawa perubahan besar dalam sistem organisasi dan manajemen kegiatan mahasiswa.
                        </p>
                        <div class="kabinet-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>20 Anggota</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>2021-2022</span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>Reformasi.php" class="kabinet-btn">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="kabinet-card" data-year="2020" data-status="past">
                    <div class="kabinet-image">
                        <img src="<?php echo IMG_URL; ?>pionir-logo.png"
                             onerror="this.src='<?php echo BASE_URL; ?>Resource/pionir-logo.png'"
                             alt="Kabinet Pionir">
                        <div class="kabinet-overlay">
                            <div class="kabinet-year">2020-2021</div>
                        </div>
                    </div>
                    <div class="kabinet-content">
                        <h3 class="kabinet-name">Kabinet Pionir</h3>
                        <p class="kabinet-description">
                            Kabinet pertama HMTA ITERA yang menjadi perintis dan fondasi organisasi hingga saat ini.
                        </p>
                        <div class="kabinet-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>18 Anggota</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>2020-2021</span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>Pionir.php" class="kabinet-btn">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="struktur-stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="5">0</div>
                        <div class="stat-label">Kabinet Terbentuk</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="5">0</div>
                        <div class="stat-label">Tahun Berkarya</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="108">0</div>
                        <div class="stat-label">Total Anggota</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="50">0</div>
                        <div class="stat-label">Program Terlaksana</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>