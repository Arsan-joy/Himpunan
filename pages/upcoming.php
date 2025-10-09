<?php
require_once '../includes/functions.php';

$page_title = 'Upcoming Events';
$additional_css = ['upcoming.css'];
$additional_js = ['upcoming.js'];

// Contoh data event mendatang (dari database atau API)
$upcoming_events = [
    [
        'id' => 1,
        'title' => 'Webinar Teknik Pertambangan',
        'date' => '2024-07-15',
        'location' => 'Online',
        'time' => '10:00 - 12:00 WIB',
    ],
    [
        'id' => 2,
        'title' => 'Field Trip ke Tambang XYZ',
        'date' => '2024-08-05',
        'location' => 'Lampung',
        'time' => '08:00 - 17:00 WIB',
    ],
    // Tambah event lainnya sesuai kebutuhan
];
?>
<?php include '../includes/header.php'; ?>

<main>
    <section class="upcoming-events-page">
        <div class="container">
            <h1>Upcoming Events</h1>
            <div class="events-grid">
                <?php if (!empty($upcoming_events)): ?>
                    <?php foreach ($upcoming_events as $event): ?>
                        <div class="event-card" id="event-<?php echo $event['id']; ?>">
                            <div class="event-date">
                                <span class="day"><?php echo date('d', strtotime($event['date'])); ?></span>
                                <span class="month"><?php echo date('M', strtotime($event['date'])); ?></span>
                            </div>
                            <div class="event-details">
                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                                <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($event['time']); ?></p>
                                <a href="#" class="btn-detail">Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-events">
                        <h3>Tidak ada event mendatang</h3>
                        <p>Pantau terus website kami untuk update event terbaru!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>