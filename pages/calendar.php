<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Kalender Akademik';
$additional_css = ['stylecalender.css'];
$additional_js  = ['calendar.js'];

include __DIR__ . '/../includes/header.php';

// Ambil semua event dari DB (diisi via Dashboard -> Kelola Kegiatan)
$events = db()->query("
    SELECT id, title, description, start_date, end_date, is_all_day, type, image_url
    FROM events
    ORDER BY start_date ASC, id ASC
")->fetchAll();
?>
<main class="calendar-container">
    <div class="calendar-header">
        <h1>Kalender Akademik</h1>
        <p>Jadwal kegiatan HMTA</p>
        
        <div class="calendar-controls">
            <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
            <h2 id="currentMonth"></h2>
            <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
    
    <div class="legend">
        <div class="legend-item">
            <span class="legend-color academic"></span>
            <span>Kegiatan Akademik</span>
        </div>
        <div class="legend-item">
            <span class="legend-color holiday"></span>
            <span>Hari Libur</span>
        </div>
        <div class="legend-item">
            <span class="legend-color event"></span>
            <span>Event Kampus</span>
        </div>
    </div>
    
    <div class="calendar" id="calendar"><!-- Calendar populated by JS --></div>
    
    <div class="event-details" id="eventDetails">
        <h3>Upcoming Events</h3>
        <div class="event-list" id="eventList"><!-- Events populated by JS --></div>
    </div>
</main>

<!-- Injeksi data dari DB ke JS -->
<script>
window.CALENDAR_EVENTS = <?= json_encode($events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>