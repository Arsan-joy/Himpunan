<?php
require_once __DIR__ . '/includes/functions.php';

$page_title     = 'Homepage';
$additional_css = [];
$additional_js  = ['index.js'];

include __DIR__ . '/includes/header.php';

// Ambil event dari DB
$upcoming_events = get_upcoming_events(9);
?>
<!-- Banner Slideshow -->
<div class="banner-container">
    <div class="banner-slide active" style="background-image: url('Resource/img/banner1.png')">
        <div class="banner-content">
            <h2>Selamat Datang di HMTA ITERA</h2>
            <p>Himpunan Mahasiswa Teknik Pertambangan Institut Teknologi Sumatera</p>
            <a href="pages/profile.php" class="btn">Tentang Kami</a>
        </div>
    </div>
    <div class="banner-slide" style="background-image: url('Resource/img/banner2.png')">
        <div class="banner-content">
            <h2>Kabinet Samagri 2024-2025</h2>
            <p>Bersinergi dalam Keberagaman untuk Membangun Prestasi</p>
            <a href="pages/samagri.php" class="btn">Lihat Struktur</a>
        </div>
    </div>
    <div class="banner-slide" style="background-image: url('Resource/img/banner3.png')">
        <div class="banner-content">
            <h2>Bergabunglah dengan Kami</h2>
            <p>Wujudkan potensi terbaikmu bersama HMTA ITERA</p>
            <a href="pages/upcoming.php" class="btn">Lihat Event</a>
        </div>
    </div>
    
    <div class="banner-navigation">
        <span class="banner-dot active" data-index="0"></span>
        <span class="banner-dot" data-index="1"></span>
        <span class="banner-dot" data-index="2"></span>
    </div>
</div>

<!-- Event Section -->
<section class="event-section">
    <h2 class="section-title">Upcoming Events</h2>
    <div class="event-container">
        <div class="event-wrapper">
            <?php if (!empty($upcoming_events)): ?>
                <?php foreach ($upcoming_events as $event): ?>
                    <div class="event-card">
                        <div class="event-image" style="background-image: url('<?= htmlspecialchars($event['image_url'] ?: (BASE_URL . 'Resource/default-event.jpg')) ?>')"></div>
                        <div class="event-details">
                            <div class="event-date"><?= htmlspecialchars(format_date_id($event['start_date'])) ?></div>
                            <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="event-description"><?= htmlspecialchars(mb_strimwidth((string)$event['description'], 0, 120, '...')) ?></p>
                            <a href="pages/calendar.php#event-<?= (int)$event['id'] ?>" class="event-link">Selengkapnya</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="event-card">
                    <div class="event-details">
                        <h3 class="event-title">Tidak ada event mendatang</h3>
                        <p class="event-description">Pantau terus website kami untuk update event terbaru!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="event-navigation">
            <button class="event-nav-btn" id="prev-btn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="event-nav-btn" id="next-btn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- Stats Container -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-content">
            <div class="stat-value"><span class="counter" data-target="200">0</span>+</div>
            <div class="stat-label">Anggota Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-content">
            <div class="stat-value"><span class="counter" data-target="50">0</span>+</div>
            <div class="stat-label">Event Tahunan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
        <div class="stat-content">
            <div class="stat-value"><span class="counter" data-target="25">0</span>+</div>
            <div class="stat-label">Prestasi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="stat-content">
            <div class="stat-value"><span class="counter" data-target="5">0</span></div>
            <div class="stat-label">Tahun Berdiri</div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>