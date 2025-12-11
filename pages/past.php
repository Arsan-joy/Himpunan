<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Kegiatan Terdahulu';
$additional_css = ['stylepast.css'];     // Resource/css/stylepast.css
$additional_js  = ['past-events.js', 'index.js'];    // Resource/js/past-events.js (opsional)

include __DIR__ . '/../includes/header.php';

// Ambil event lampau dari DB
// Pakai COALESCE(end_date, start_date) < hari ini
$stmt = db()->query("
    SELECT id, title, description, start_date, end_date, is_all_day, type, image_url
    FROM events
    WHERE COALESCE(end_date, start_date) < CURDATE()
    ORDER BY start_date DESC, id DESC
");
$past_events = $stmt->fetchAll();

// Mapping type DB -> kategori filter yang dipakai tombol (agar JS existing tetap cocok)
function map_type_to_filter(string $type): string {
    $t = strtolower(trim($type));
    return match ($t) {
        'academic' => 'akademik',
        'event'    => 'organisasi',
        'holiday'  => 'lainnya',
        default    => 'lainnya',
    };
}
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

        <div class="events-grid" id="pastEventsGrid">
            <?php if (!empty($past_events)): ?>
                <?php foreach ($past_events as $ev): ?>
                    <?php
                        $cat  = map_type_to_filter($ev['type'] ?? '');
                        $img  = $ev['image_url'] ?: (BASE_URL . 'Resource/img/banner1.png');
                        $desc = trim((string)($ev['description'] ?? ''));
                        $title = trim((string)($ev['title'] ?? 'Kegiatan'));
                    ?>
                    <div class="event-card" data-category="<?= htmlspecialchars($cat) ?>">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" onerror="this.src='https://via.placeholder.com/400x250?text=HMTA'">
                        <div class="card-content">
                            <h3><?= htmlspecialchars($title) ?></h3>
                            <?php if ($desc !== ''): ?>
                                <p><?= htmlspecialchars(mb_strimwidth($desc, 0, 150, '…')) ?></p>
                            <?php else: ?>
                                <p><?= htmlspecialchars(format_date_id($ev['start_date'])) ?><?= !empty($ev['end_date']) && $ev['end_date'] !== $ev['start_date'] ? ' — ' . htmlspecialchars(format_date_id($ev['end_date'])) : '' ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column:1/-1; text-align:center; color:#6b7280;">
                    Belum ada kegiatan terdahulu.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>