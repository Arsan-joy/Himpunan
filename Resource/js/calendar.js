document.addEventListener('DOMContentLoaded', function () {

    // ── Navbar mobile ────────────────────────────────────────────────────────
    const mobileMenuBtn     = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mainNav           = document.getElementById('mainNav');
    if (mobileMenuBtn && mainNav && mobileMenuOverlay) {
        mobileMenuBtn.addEventListener('click', function () {
            mainNav.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
        mobileMenuOverlay.addEventListener('click', function () {
            mainNav.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    }

    // ── Elemen kalender ──────────────────────────────────────────────────────
    const calendar        = document.getElementById('calendar');
    const eventList       = document.getElementById('eventList');
    const currentMonthEl  = document.getElementById('currentMonth');
    const prevMonthBtn    = document.getElementById('prevMonth');
    const nextMonthBtn    = document.getElementById('nextMonth');

    let currentDate  = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear  = currentDate.getFullYear();

    // ── Helper: parse "YYYY-MM-DD" ke Date lokal tanpa timezone shift ────────
    // new Date("2025-12-08") diinterpretasi sebagai UTC midnight → bisa geser
    // ke hari sebelumnya di timezone +7. Gunakan split manual agar aman.
    function parseLocalDate(str) {
        if (!str) return new Date(NaN);
        const parts = String(str).split('-').map(Number);
        if (parts.length !== 3) return new Date(NaN);
        return new Date(parts[0], parts[1] - 1, parts[2]); // local midnight
    }

    // ── Parse data event dari PHP ────────────────────────────────────────────
    const raw = Array.isArray(window.CALENDAR_EVENTS) ? window.CALENDAR_EVENTS : [];

    const academicEvents = raw.map(e => ({
        id:          e.id,
        title:       e.title       || '',
        description: e.description || '',
        type:        (e.type       || 'event').toLowerCase(),
        imageUrl:    e.image_url   || '',

        // FIX: simpan start DAN end sebagai Date lokal
        startDate: parseLocalDate(e.start_date),

        // FIX: jika end_date kosong/null, anggap event satu hari (= start_date)
        endDate: parseLocalDate(e.end_date || e.start_date),
    })).filter(e => !isNaN(e.startDate.getTime()));

    console.log('Parsed academicEvents:', academicEvents.length, academicEvents);

    // ── Helper: cek apakah sebuah tanggal jatuh dalam rentang event ──────────
    // FIX UTAMA: sebelumnya hanya membandingkan ev.date === dThis (hanya start_date).
    // Sekarang: event muncul di kotak jika startDate <= dThis <= endDate.
    function getEventsForDate(dateObj) {
        // Normalisasi ke midnight agar perbandingan akurat
        const d = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate());
        return academicEvents.filter(ev => {
            const start = new Date(ev.startDate.getFullYear(), ev.startDate.getMonth(), ev.startDate.getDate());
            const end   = new Date(ev.endDate.getFullYear(),   ev.endDate.getMonth(),   ev.endDate.getDate());
            return d >= start && d <= end; // inklusif di kedua ujung
        });
    }

    // ── Render kalender ──────────────────────────────────────────────────────
    function generateCalendar(month, year) {
        calendar.innerHTML = '';

        const monthNames = [
            "Januari","Februari","Maret","April","Mei","Juni",
            "Juli","Agustus","September","Oktober","November","Desember"
        ];
        currentMonthEl.textContent = `${monthNames[month]} ${year}`;

        const weekdays = ["Min","Sen","Sel","Rab","Kam","Jum","Sab"];
        const grid = document.createElement('div');
        grid.className = 'calendar-grid';

        // Header nama hari
        weekdays.forEach(day => {
            const el = document.createElement('div');
            el.className = 'calendar-weekday';
            el.textContent = day;
            grid.appendChild(el);
        });

        const firstDayOfMonth  = new Date(year, month, 1).getDay();
        const daysInMonth      = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth  = new Date(year, month, 0).getDate();

        let day          = 1;
        let nextMonthDay = 1;
        const totalCells = 42; // 6 baris × 7 kolom

        for (let i = 0; i < totalCells; i++) {
            const dayEl  = document.createElement('div');
            dayEl.className = 'calendar-day';

            const dateEl = document.createElement('div');
            dateEl.className = 'calendar-date';

            if (i < firstDayOfMonth) {
                // Hari dari bulan sebelumnya
                dateEl.textContent = daysInPrevMonth - (firstDayOfMonth - i - 1);
                dayEl.classList.add('outside-month');

            } else if (i < firstDayOfMonth + daysInMonth) {
                // Hari dalam bulan ini
                dateEl.textContent = day;

                // Tandai hari ini
                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayEl.classList.add('today');
                }

                // FIX: gunakan getEventsForDate() yang mengecek rentang tanggal,
                // bukan hanya mencocokkan start_date saja.
                const dThis = new Date(year, month, day);
                const evs   = getEventsForDate(dThis);

                evs.forEach(ev => {
                    const evEl = document.createElement('div');
                    evEl.className = `calendar-event ${ev.type}`;
                    evEl.textContent = ev.title;
                    evEl.title = ev.description || ev.title;

                    // Tambahkan anchor agar bisa di-scroll dari halaman lain
                    // (mis. dari index.php: calendar.php#event-5)
                    evEl.dataset.eventId = ev.id;

                    // Klik event → scroll ke detail di sidebar
                    evEl.addEventListener('click', () => scrollToEventDetail(ev.id));

                    dayEl.appendChild(evEl);
                });

                // Anchor untuk deep-link (#event-ID)
                dayEl.id = `day-${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;

                day++;

            } else {
                // Hari dari bulan berikutnya
                dateEl.textContent = nextMonthDay++;
                dayEl.classList.add('outside-month');
            }

            dayEl.appendChild(dateEl);
            grid.appendChild(dayEl);
        }

        calendar.appendChild(grid);
        updateEventList();

        // Scroll ke event jika ada hash di URL (mis. #event-5)
        handleUrlHash();
    }

    // ── Sidebar: daftar event bulan ini ke depan ─────────────────────────────
    function updateEventList() {
        eventList.innerHTML = '';

        const from = new Date(currentYear, currentMonth, 1);

        // FIX: filter berdasarkan endDate >= from agar event multi-hari yang
        // dimulai bulan lalu tapi berakhir bulan ini tetap muncul di sidebar.
        const upcoming = academicEvents
            .filter(e => e.endDate >= from)
            .sort((a, b) => a.startDate - b.startDate)
            .slice(0, 8); // tampilkan maks 8 event

        if (upcoming.length === 0) {
            const p = document.createElement('p');
            p.textContent = 'Tidak ada event dalam waktu dekat.';
            eventList.appendChild(p);
            return;
        }

        const monthShort = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];

        upcoming.forEach(ev => {
            const item = document.createElement('div');
            item.className = `event-item ${ev.type}`;
            item.id = `event-${ev.id}`; // anchor untuk deep-link

            // Blok tanggal
            const dWrap = document.createElement('div');
            dWrap.className = 'event-date';

            const dDay = document.createElement('div');
            dDay.className = 'event-day';
            dDay.textContent = ev.startDate.getDate();

            const dMonth = document.createElement('div');
            dMonth.className = 'event-month';
            dMonth.textContent = `${monthShort[ev.startDate.getMonth()]} ${ev.startDate.getFullYear()}`;

            dWrap.appendChild(dDay);
            dWrap.appendChild(dMonth);

            // Info event
            const info = document.createElement('div');
            info.className = 'event-info';

            const h4 = document.createElement('h4');
            h4.textContent = ev.title;

            const pDesc = document.createElement('p');
            pDesc.textContent = ev.description;

            // FIX: tampilkan rentang tanggal jika event multi-hari
            const startStr = ev.startDate.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
            const endStr   = ev.endDate.toLocaleDateString('id-ID',   { day:'numeric', month:'short', year:'numeric' });
            const isSameDay = ev.startDate.getTime() === ev.endDate.getTime();

            const pRange = document.createElement('p');
            pRange.className = 'event-range';
            pRange.textContent = isSameDay ? startStr : `${startStr} – ${endStr}`;

            info.appendChild(h4);
            info.appendChild(pRange);
            if (ev.description) info.appendChild(pDesc);

            item.appendChild(dWrap);
            item.appendChild(info);
            eventList.appendChild(item);
        });
    }

    // ── Deep-link: scroll ke event dari URL hash (#event-5) ─────────────────
    function handleUrlHash() {
        const hash = window.location.hash; // mis. "#event-5"
        if (!hash) return;

        // Cari elemen dengan id yang cocok
        const target = document.querySelector(hash);
        if (target) {
            setTimeout(() => target.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
            target.classList.add('highlight');
        }
    }

    function scrollToEventDetail(eventId) {
        const target = document.getElementById(`event-${eventId}`);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            target.classList.add('highlight');
            setTimeout(() => target.classList.remove('highlight'), 2000);
        }
    }

    // ── Init + navigasi bulan ────────────────────────────────────────────────
    generateCalendar(currentMonth, currentYear);

    prevMonthBtn.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        generateCalendar(currentMonth, currentYear);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        generateCalendar(currentMonth, currentYear);
    });
});
