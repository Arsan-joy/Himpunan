<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Struktur Organisasi';
$additional_css = ['struktur.css'];
$additional_js  = ['struktur.js'];

include __DIR__ . '/../includes/header.php';

// Ambil data kabinet dari DB
$kabinet = db()->query("SELECT id, name, period, description, logo_url, created_at FROM kabinet ORDER BY id DESC")->fetchAll();

// Tentukan kabinet aktif:
// - Cari yang periodenya memuat tahun berjalan
// - Jika tidak ketemu, gunakan baris pertama (terbaru)
$currentYear = (int)date('Y');
$activeIndex = null;
foreach ($kabinet as $i => $row) {
    $period = strtolower(trim((string)$row['period']));
    if ($period !== '' && strpos($period, (string)$currentYear) !== false) {
        $activeIndex = $i;
        break;
    }
}
if ($activeIndex === null && !empty($kabinet)) $activeIndex = 0;

$activeCabinet = $activeIndex !== null ? $kabinet[$activeIndex] : null;
$pastCabinets  = [];
if ($activeIndex !== null) {
    foreach ($kabinet as $i => $row) {
        if ($i !== $activeIndex) $pastCabinets[] = $row;
    }
} else {
    // Tidak ada data kabinet sama sekali
    $pastCabinets = [];
}
?>
<main>
    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <h1>Struktur Organisasi</h1>
            <p>Kabinet-kabinet yang telah memimpin HMTA ITERA dari masa ke masa</p>
            <div class="breadcrumb">
                <a href="<?= BASE_URL; ?>">Beranda</a>
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
                <?php if ($activeCabinet): ?>
                    <!-- Kabinet Aktif -->
                    <div class="kabinet-card current" data-year="<?= htmlspecialchars(preg_replace('~[^0-9-]~','', (string)$activeCabinet['period'])) ?>" data-status="current">
                        <div class="kabinet-badge">Kabinet Aktif</div>
                        <div class="kabinet-image">
                            <img src="<?= htmlspecialchars($activeCabinet['logo_url'] ?: (BASE_URL . 'Resource/img/banner1.png')) ?>"
                                 onerror="this.src='<?= BASE_URL; ?>Resource/img/banner1.png'"
                                 alt="<?= htmlspecialchars($activeCabinet['name']) ?>">
                            <div class="kabinet-overlay">
                                <div class="kabinet-year"><?= htmlspecialchars($activeCabinet['period'] ?: date('Y')) ?></div>
                            </div>
                        </div>
                        <div class="kabinet-content">
                            <h3 class="kabinet-name"><?= htmlspecialchars($activeCabinet['name']) ?></h3>
                            <p class="kabinet-description">
                                <?= htmlspecialchars($activeCabinet['description'] ?: 'Deskripsi kabinet aktif belum tersedia.') ?>
                            </p>
                            <div class="kabinet-stats">
                                <div class="stat-item">
                                    <i class="fas fa-users"></i>
                                    <span>— Anggota</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?= htmlspecialchars($activeCabinet['period'] ?: date('Y')) ?></span>
                                </div>
                            </div>
                            <a href="<?= BASE_URL; ?>pages/samagri.php" class="kabinet-btn">
                                <span>Lihat Detail</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Kabinet-kabinet sebelumnya -->
                <?php if (!empty($pastCabinets)): ?>
                    <?php foreach ($pastCabinets as $row): ?>
                        <div class="kabinet-card" data-year="<?= htmlspecialchars(preg_replace('~[^0-9-]~','', (string)$row['period'])) ?>" data-status="past">
                            <div class="kabinet-image">
                                <img src="<?= htmlspecialchars($row['logo_url'] ?: (BASE_URL . 'Resource/img/banner1.png')) ?>"
                                     onerror="this.src='<?= BASE_URL; ?>Resource/img/banner1.png'"
                                     alt="<?= htmlspecialchars($row['name']) ?>">
                                <div class="kabinet-overlay">
                                    <div class="kabinet-year"><?= htmlspecialchars($row['period'] ?: '-') ?></div>
                                </div>
                            </div>
                            <div class="kabinet-content">
                                <h3 class="kabinet-name"><?= htmlspecialchars($row['name']) ?></h3>
                                <p class="kabinet-description">
                                    <?= htmlspecialchars(mb_strimwidth((string)($row['description'] ?? ''), 0, 160, '…')) ?: 'Deskripsi belum tersedia.' ?>
                                </p>
                                <div class="kabinet-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-users"></i>
                                        <span>— Anggota</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?= htmlspecialchars($row['period'] ?: '-') ?></span>
                                    </div>
                                </div>
                                <a href="<?= BASE_URL; ?>pages/samagri.php" class="kabinet-btn">
                                    <span>Lihat Detail</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if (!$activeCabinet): ?>
                        <div style="grid-column:1/-1; text-align:center; color:#6b7280">
                            Belum ada data kabinet. Tambahkan melalui Dashboard &rarr; Kelola Kabinet.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="struktur-stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="<?= (int)db()->query('SELECT COUNT(*) c FROM kabinet')->fetch()['c'] ?>">0</div>
                        <div class="stat-label">Kabinet Terbentuk</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="<?= (int)db()->query('SELECT COUNT(*) c FROM events')->fetch()['c'] ?>">0</div>
                        <div class="stat-label">Total Kegiatan</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-award"></i></div>
                    <div class="stat-content">
                        <div class="stat-number" data-target="<?= (int)db()->query('SELECT COUNT(*) c FROM members')->fetch()['c'] ?>">0</div>
                        <div class="stat-label">Total Anggota</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
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