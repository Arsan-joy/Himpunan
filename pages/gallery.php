<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Galeri';
$additional_css = ['stylegallery.css']; // pastikan ada di Resource/css/stylegallery.css
$additional_js  = ['gallery.js'];       // pastikan ada di Resource/js/gallery.js

include __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="gallery-hero">
        <div class="container">
            <h1>Galeri Kegiatan HMTA ITERA</h1>
            <p>Dokumentasi momen-momen berharga dari berbagai kegiatan Himpunan Mahasiswa Teknik Pertambangan ITERA</p>
        </div>
    </section>

    <section class="gallery-filter">
        <div class="container">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="academic">Akademik</button>
                <button class="filter-btn" data-filter="organizational">Organisasi</button>
                <button class="filter-btn" data-filter="community">Komunitas</button>
                <button class="filter-btn" data-filter="other">Lainnya</button>
            </div>
            <div class="search-box">
                <input type="text" id="gallery-search" placeholder="Cari kegiatan...">
                <button type="button" aria-label="Cari"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </section>

    <section class="gallery-grid">
        <div class="container">
            <div class="gallery-items" id="galleryItems">
                <!-- Contoh item 1 -->
                <div class="gallery-item" data-category="academic"
                     data-title="Workshop Pertambangan"
                     data-date="Oktober 2024"
                     data-description="Workshop teknik pertambangan bersama profesional industri">
                    <div class="gallery-image">
                        <img src="<?php echo BASE_URL; ?>Resource/IMG_1381.PNG" alt="Workshop Pertambangan"
                             onerror="this.src='https://via.placeholder.com/400x300?text=HMTA'">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Workshop Pertambangan</h3>
                                <p>Oktober 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Workshop Pertambangan</h3>
                        <p>Workshop teknik pertambangan bersama profesional industri</p>
                    </div>
                </div>

                <!-- Contoh item 2 -->
                <div class="gallery-item" data-category="academic"
                     data-title="Workshop Pertambangan"
                     data-date="Oktober 2024"
                     data-description="Workshop teknik pertambangan bersama profesional industri">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Workshop Pertambangan">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Workshop Pertambangan</h3>
                                <p>Oktober 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Workshop Pertambangan</h3>
                        <p>Workshop teknik pertambangan bersama profesional industri</p>
                    </div>
                </div>

                <!-- Contoh item 3 -->
                <div class="gallery-item" data-category="organizational"
                     data-title="Rapat Kerja HMTA"
                     data-date="September 2024"
                     data-description="Rapat kerja tahunan pengurus HMTA ITERA">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Rapat Kerja HMTA">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Rapat Kerja HMTA</h3>
                                <p>September 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Rapat Kerja HMTA</h3>
                        <p>Rapat kerja tahunan pengurus HMTA ITERA</p>
                    </div>
                </div>

                <!-- Contoh item 4 -->
                <div class="gallery-item" data-category="community"
                     data-title="Bakti Sosial"
                     data-date="Agustus 2024"
                     data-description="Kegiatan bakti sosial di desa sekitar kampus">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Bakti Sosial">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Bakti Sosial</h3>
                                <p>Agustus 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Bakti Sosial</h3>
                        <p>Kegiatan bakti sosial di desa sekitar kampus</p>
                    </div>
                </div>

                <!-- Contoh item 5 -->
                <div class="gallery-item" data-category="academic"
                     data-title="Kunjungan Industri"
                     data-date="Juli 2024"
                     data-description="Kunjungan ke tambang batu bara di Kalimantan">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Kunjungan Industri">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Kunjungan Industri</h3>
                                <p>Juli 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Kunjungan Industri</h3>
                        <p>Kunjungan ke tambang batu bara di Kalimantan</p>
                    </div>
                </div>

                <!-- Contoh item 6 -->
                <div class="gallery-item" data-category="other"
                     data-title="Perayaan Hari Tambang"
                     data-date="Mei 2024"
                     data-description="Acara perayaan Hari Pertambangan Nasional">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Perayaan Hari Tambang">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Perayaan Hari Tambang</h3>
                                <p>Mei 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Perayaan Hari Tambang</h3>
                        <p>Acara perayaan Hari Pertambangan Nasional</p>
                    </div>
                </div>

                <!-- Contoh item 7 -->
                <div class="gallery-item" data-category="organizational"
                     data-title="Pelantikan Pengurus"
                     data-date="April 2024"
                     data-description="Pelantikan pengurus baru HMTA periode 2024/2025">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Pelantikan Pengurus">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Pelantikan Pengurus</h3>
                                <p>April 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Pelantikan Pengurus</h3>
                        <p>Pelantikan pengurus baru HMTA periode 2024/2025</p>
                    </div>
                </div>

                <!-- Contoh item 8 -->
                <div class="gallery-item" data-category="community"
                     data-title="Seminar Lingkungan"
                     data-date="Maret 2024"
                     data-description="Seminar tentang reklamasi tambang dan pelestarian lingkungan">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Seminar Lingkungan">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Seminar Lingkungan</h3>
                                <p>Maret 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Seminar Lingkungan</h3>
                        <p>Seminar tentang reklamasi tambang dan pelestarian lingkungan</p>
                    </div>
                </div>

                <!-- Contoh item 9 -->
                <div class="gallery-item" data-category="other"
                     data-title="Kompetisi Mahasiswa"
                     data-date="Februari 2024"
                     data-description="Kompetisi karya ilmiah mahasiswa teknik pertambangan">
                    <div class="gallery-image">
                        <img src="https://via.placeholder.com/400x300" alt="Kompetisi Mahasiswa">
                        <div class="overlay">
                            <div class="overlay-content">
                                <h3>Kompetisi Mahasiswa</h3>
                                <p>Februari 2024</p>
                                <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-info">
                        <h3>Kompetisi Mahasiswa</h3>
                        <p>Kompetisi karya ilmiah mahasiswa teknik pertambangan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="modal" id="galleryModal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <button class="close-modal" type="button" aria-label="Tutup">&times;</button>
            <div class="modal-image-container">
                <img id="modal-image" src="" alt="">
                <button class="nav-btn prev-btn" type="button" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
                <button class="nav-btn next-btn" type="button" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="modal-info">
                <h2 id="modal-title"></h2>
                <p id="modal-date"></p>
                <p id="modal-description"></p>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>