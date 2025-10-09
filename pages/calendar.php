<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Kalender Akademik';
$additional_css = ['stylecalender.css'];   // assets/css/stylecalender.css
$additional_js  = ['calendar.js'];         // assets/js/calendar.js

include __DIR__ . '/../includes/header.php';
?>
<main class="calendar-container">
    <div class="calendar-header">
        <h1>Kalender Akademik</h1>
        <p>Semester Genap 2024/2025</p>
        
        <div class="calendar-controls">
            <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
            <h2 id="currentMonth">April 2025</h2>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>