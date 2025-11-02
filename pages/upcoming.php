<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Upcoming Events';
$additional_css = ['upcoming.css'];
$additional_js  = ['upcoming.js'];

include __DIR__ . '/../includes/header.php';

// Ambil event mendatang dari DB
$stmt = db()->query("
    SELECT id, title, description, start_date, end_date, is_all_day, type, image_url
    FROM events
    WHERE start_date >= CURDATE()
    ORDER BY start_date ASC, id ASC
");
$upcoming_events = $stmt->fetchAll();
?>
<main>
    <section class="upcoming-events-page">
        <div class="container">
            <h1>Upcoming Events</h1>
            <div class="events-grid">
                <?php if (!empty($upcoming_events)): ?>
                    <?php foreach ($upcoming_events as $event): ?>
                        <?php
                            $day   = date('d', strtotime($event['start_date']));
                            $month = date('M', strtotime($event['start_date']));
                            $img   = $event['image_url'] ?: (BASE_URL . 'Resource/img/banner1.png');
                            $desc  = trim((string)($event['description'] ?? ''));
                        ?>
                        <div class="event-card" id="event-<?= (int)$event['id'] ?>">
                            <div class="event-date">
                                <span class="day"><?= htmlspecialchars($day) ?></span>
                                <span class="month"><?= htmlspecialchars($month) ?></span>
                            </div>
                            <div class="event-details">
                                <h3><?= htmlspecialchars($event['title']) ?></h3>
                                <?php if ($desc !== ''): ?>
                                    <p><i class="fas fa-info-circle"></i> <?= htmlspecialchars(mb_strimwidth($desc, 0, 140, '…')) ?></p>
                                <?php endif; ?>
                                <p><i class="fas fa-tag"></i> <?= htmlspecialchars(ucfirst($event['type'])) ?></p>
                                <?php if (!empty($event['end_date']) && $event['end_date'] !== $event['start_date']): ?>
                                    <p><i class="fas fa-calendar"></i> <?= htmlspecialchars(format_date_id($event['start_date'])) ?> – <?= htmlspecialchars(format_date_id($event['end_date'])) ?></p>
                                <?php else: ?>
                                    <p><i class="fas fa-calendar"></i> <?= htmlspecialchars(format_date_id($event['start_date'])) ?></p>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>pages/calendar.php#event-<?= (int)$event['id'] ?>" class="btn-detail">Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-events" style="grid-column: 1/-1; text-align:center; color:#6b7280;">
                        <h3>Tidak ada event mendatang</h3>
                        <p>Pantau terus website kami untuk update event terbaru!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>