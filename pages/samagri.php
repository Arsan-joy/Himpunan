<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Kabinet HMTA ITERA';
$additional_css = ['stylesamagri.css']; // pastikan file CSS ini ada
$additional_js  = [];                   // tambahkan jika ada JS khusus

include __DIR__ . '/../includes/header.php';

// Ambil Kabinet dari DB
$kabinetRows = db()->query("SELECT id, name, period, description, logo_url FROM kabinet ORDER BY id DESC")->fetchAll();

// Tentukan kabinet aktif (period memuat tahun berjalan). Jika tak ada yang cocok, pakai terbaru.
$currentYear = (int)date('Y');
$activeCabinet = null;
foreach ($kabinetRows as $row) {
    $period = strtolower((string)($row['period'] ?? ''));
    if ($period !== '' && strpos($period, (string)$currentYear) !== false) {
        $activeCabinet = $row;
        break;
    }
}
if (!$activeCabinet && !empty($kabinetRows)) {
    $activeCabinet = $kabinetRows[0];
}

// Ambil daftar departemen dari DB
$departments = db()->query("SELECT id, name, slug, description FROM departments ORDER BY name ASC")->fetchAll();

// Helper ringkas
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function dept_link(string $slug): string {
    // Pakai template generik departemen
    return BASE_URL . 'pages/department.php?slug=' . rawurlencode($slug);
}
?>
<main>
    <!-- Hero Section (dinamis dari Kabinet Aktif) -->
    <section class="hero">
        <div class="hero-content">
            <h1><?= h($activeCabinet['name'] ?? 'Himpunan Mahasiswa Teknik Pertambangan ITERA') ?></h1>
            <?php if (!empty($activeCabinet['period'])): ?>
                <p style="margin:.25rem 0;opacity:.9">Periode: <?= h($activeCabinet['period']) ?></p>
            <?php endif; ?>
            <p><?= h($activeCabinet['description'] ?? 'Membangun Karakter Profesional dan Berkompeten dalam Bidang Pertambangan') ?></p>
            <a href="<?= BASE_URL ?>pages/struktur.php" class="btn-primary">Lihat Struktur Organisasi</a>
        </div>
    </section>

    <!-- Organization Overview (konten ringkas) -->
    <section class="org-overview">
        <div class="container">
            <h2>Tentang HMTA ITERA</h2>
            <div class="overview-content">
                <div class="overview-text">
                    <p>Himpunan Mahasiswa Teknik Pertambangan (HMTA) ITERA merupakan organisasi kemahasiswaan yang menaungi seluruh mahasiswa Teknik Pertambangan ITERA.</p>
                    <p>Dengan visi menjadi organisasi kemahasiswaan yang profesional, inovatif, dan berkontribusi nyata untuk kemajuan industri pertambangan di Indonesia, HMTA ITERA terus berkarya melalui program-program unggulan.</p>
                    <a href="<?= BASE_URL ?>pages/profile.php" class="btn-secondary">Selengkapnya</a>
                </div>
                <div class="overview-image">
                    <img src="<?= h($activeCabinet['logo_url'] ?? (BASE_URL . 'Resource/hmta-group.jpg')) ?>"
                         alt="<?= h($activeCabinet['name'] ?? 'Kabinet HMTA') ?>"
                         onerror="this.src='<?= BASE_URL ?>Resource/hmta-group.jpg'">
                </div>
            </div>
        </div>
    </section>

    <!-- Daftar Departemen (terhubung ke department.php?slug=...) -->
    <section class="current-cabinet">
        <div class="container">
            <h2>Departemen</h2>
            <p class="cabinet-tagline" style="margin-bottom:10px">"Bersinergi dalam Keberagaman untuk Membangun Prestasi"</p>

            <div class="cabinet-departments">
                <div class="department-grid">
                    <?php if (!empty($departments)): ?>
                        <?php foreach ($departments as $d): ?>
                            <a href="<?= dept_link($d['slug']) ?>" class="department-card" title="Lihat <?= h($d['name']) ?>">
                                <img class="logo-kabinet"
                                     src="<?= BASE_URL ?>Resource/default-dept.png"
                                     alt="<?= h($d['name']) ?>"
                                     onerror="this.src='<?= BASE_URL ?>Resource/default-dept.png'">
                                <h4><?= h($d['name']) ?></h4>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column:1/-1; text-align:center; color:#6b7280">
                            Belum ada data departemen. Tambahkan melalui Dashboard → Kelola Departemen.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center" style="margin-top:16px">
                <a href="<?= BASE_URL ?>pages/upcoming.php" class="btn-secondary">Lihat Semua Event</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>