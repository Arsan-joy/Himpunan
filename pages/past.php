<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Kegiatan Terdahulu';
$additional_css = ['stylepast.css'];     // Pastikan ada di Resource/css/stylepast.css
$additional_js  = ['past-events.js'];    // Pastikan ada di Resource/js/past-events.js

include __DIR__ . '/../includes/header.php';
?>

<main class="events-page">
    <div class="events-container">
        <div class="filter-bar">
            <div class="filter-buttons">
                <button class="filter-btn active" data-category="semua">Semua</button>
                <button class="filter-btn" data-category="akademik">Akademik</button>
                <button class="filter-btn" data-category="organisasi">Organisasi</button>
                <button class="filter-btn" data-category="komunitas">Komunitas</button>
                <button class="filter-btn" data-category="lainnya">Lainnya</button>
            </div>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Cari Kegiatan...">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <div class="events-grid">
            <div class="event-card" data-category="akademik">
                <img src="https://via.placeholder.com/400x250/007bff/ffffff?text=Workshop+Pertambangan" alt="Workshop Pertambangan">
                <div class="card-content">
                    <h3>Workshop Pertambangan</h3>
                    <p>Workshop teknik pertambangan bersama profesional industri</p>
                </div>
            </div>

            <div class="event-card" data-category="organisasi">
                <img src="https://via.placeholder.com/400x250/28a745/ffffff?text=Rapat+Kerja" alt="Rapat Kerja HMTA">
                <div class="card-content">
                    <h3>Rapat Kerja HMTA</h3>
                    <p>Rapat kerja tahunan pengurus HMTA ITERA</p>
                </div>
            </div>

            <div class="event-card" data-category="komunitas">
                <img src="Resource/img/IMG_1381.PNG" alt="Bakti Sosial">
                <div class="card-content">
                    <h3>Bakti Sosial</h3>
                    <p>Kegiatan bakti sosial di desa sekitar kampus</p>
                </div>
            </div>

            <div class="event-card" data-category="akademik">
                <img src="https://via.placeholder.com/400x250/dc3545/ffffff?text=Seminar+Nasional" alt="Seminar Nasional">
                <div class="card-content">
                    <h3>Seminar Nasional Geologi</h3>
                    <p>Seminar membahas perkembangan terbaru di dunia geologi</p>
                </div>
            </div>

            <div class="event-card" data-category="lainnya">
                <img src="https://via.placeholder.com/400x250/17a2b8/ffffff?text=Mining+Competition" alt="Mining Competition">
                <div class="card-content">
                    <h3>Mining Competition</h3>
                    <p>Kompetisi pertambangan tingkat nasional antar universitas</p>
                </div>
            </div>

            <div class="event-card" data-category="organisasi">
                <img src="https://via.placeholder.com/400x250/6c757d/ffffff?text=Upgrading+Anggota" alt="Upgrading Anggota">
                <div class="card-content">
                    <h3>Upgrading Anggota</h3>
                    <p>Kegiatan peningkatan kapasitas untuk anggota baru HMTA</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>