document.addEventListener('DOMContentLoaded', function() {
    // Navbar mobile: konsisten gunakan class 'active'
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mainNav = document.getElementById('mainNav');
    if (mobileMenuBtn && mainNav && mobileMenuOverlay) {
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
        mobileMenuOverlay.addEventListener('click', function() {
            mainNav.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    }

    // Elemen kalender
    const calendar = document.getElementById('calendar');
    const eventList = document.getElementById('eventList');
    const currentMonthEl = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    // Tanggal saat ini
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    // Ambil data event dari PHP
    const raw = Array.isArray(window.CALENDAR_EVENTS) ? window.CALENDAR_EVENTS : [];
    // Parse aman (YYYY-MM-DD -> Date(y, m-1, d)) agar tidak terdampak zona waktu
    const academicEvents = raw.map(e => {
        const parts = String(e.start_date || '').split('-').map(Number);
        const d = (parts.length === 3) ? new Date(parts[0], parts[1] - 1, parts[2]) : new Date(NaN);
        return {
            date: d,
            title: e.title || '',
            description: e.description || '',
            type: (e.type || 'event').toLowerCase()
        };
    }).filter(e => !isNaN(e.date.getTime()));
    console.log('Parsed academicEvents length:', academicEvents.length, academicEvents);

    function generateCalendar(month, year) {
        calendar.innerHTML = '';

        const monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        currentMonthEl.textContent = `${monthNames[month]} ${year}`;

        const weekdays = ["Min","Sen","Sel","Rab","Kam","Jum","Sab"];
        const grid = document.createElement('div');
        grid.className = 'calendar-grid';

        // Header hari
        weekdays.forEach(day => {
            const el = document.createElement('div');
            el.className = 'calendar-weekday';
            el.textContent = day;
            grid.appendChild(el);
        });

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        let day = 1;
        let nextMonthDay = 1;
        const totalCells = 42;

        for (let i = 0; i < totalCells; i++) {
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day';

            const dateEl = document.createElement('div');
            dateEl.className = 'calendar-date';

            if (i < firstDayOfMonth) {
                const prev = daysInPrevMonth - (firstDayOfMonth - i - 1);
                dateEl.textContent = prev;
                dayEl.classList.add('outside-month');
            } else if (i < firstDayOfMonth + daysInMonth) {
                dateEl.textContent = day;

                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayEl.classList.add('today');
                }

                const dThis = new Date(year, month, day);
                const evs = academicEvents.filter(ev =>
                    ev.date.getFullYear() === dThis.getFullYear() &&
                    ev.date.getMonth() === dThis.getMonth() &&
                    ev.date.getDate() === dThis.getDate()
                );

                evs.forEach(ev => {
                    const evEl = document.createElement('div');
                    evEl.className = `calendar-event ${ev.type}`;
                    evEl.textContent = ev.title;
                    evEl.title = ev.description || ev.title;
                    dayEl.appendChild(evEl);
                });

                day++;
            } else {
                dateEl.textContent = nextMonthDay++;
                dayEl.classList.add('outside-month');
            }

            dayEl.appendChild(dateEl);
            grid.appendChild(dayEl);
        }

        calendar.appendChild(grid);
        updateEventList();
    }

    function updateEventList() {
        eventList.innerHTML = '';

        const from = new Date(currentYear, currentMonth, 1);
        const upcoming = academicEvents
            .filter(e => e.date >= from)
            .sort((a,b) => a.date - b.date)
            .slice(0, 5);

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

            const dWrap = document.createElement('div'); dWrap.className = 'event-date';
            const d = document.createElement('div'); d.className = 'event-day'; d.textContent = ev.date.getDate();
            const m = document.createElement('div'); m.className = 'event-month'; m.textContent = `${monthShort[ev.date.getMonth()]} ${ev.date.getFullYear()}`;
            dWrap.appendChild(d); dWrap.appendChild(m);

            const info = document.createElement('div'); info.className = 'event-info';
            const h4 = document.createElement('h4'); h4.textContent = ev.title;
            const p = document.createElement('p'); p.textContent = ev.description;
            info.appendChild(h4); info.appendChild(p);

            item.appendChild(dWrap); item.appendChild(info);
            eventList.appendChild(item);
        });
    }

    // Init + navigasi
    generateCalendar(currentMonth, currentYear);
    prevMonthBtn.addEventListener('click', () => { currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; } generateCalendar(currentMonth, currentYear); });
    nextMonthBtn.addEventListener('click', () => { currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; } generateCalendar(currentMonth, currentYear); });
});