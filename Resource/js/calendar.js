document.addEventListener('DOMContentLoaded', function() {
    // Initialize the calendar
    const calendar = document.getElementById('calendar');
    const eventList = document.getElementById('eventList');
    const currentMonthEl = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    // Current date
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    // Academic events data
    const academicEvents = [
        {
            date: new Date(2025, 3, 21), // April 21, 2025
            title: "Awal Pembelajaran",
            description: "Hari pertama perkuliahan semester genap 2024/2025",
            type: "academic"
        },
        {
            date: new Date(2025, 3, 25), // April 25, 2025
            title: "Batas Akhir Pengisian KRS",
            description: "Tenggat waktu pengisian Kartu Rencana Studi untuk mahasiswa",
            type: "academic"
        },
        {
            date: new Date(2025, 4, 1), // May 1, 2025
            title: "Hari Buruh Nasional",
            description: "Libur Nasional",
            type: "holiday"
        },
        {
            date: new Date(2025, 4, 12), // May 12, 2025
            title: "Seminar Tambang Berkelanjutan",
            description: "Seminar nasional tentang praktik pertambangan berkelanjutan",
            type: "event"
        },
        {
            date: new Date(2025, 4, 16), // May 16, 2025
            title: "Kunjungan Industri",
            description: "Kunjungan ke PT. Tambang Batubara Bukit Asam",
            type: "event"
        },
        {
            date: new Date(2025, 5, 1), // June 1, 2025
            title: "Pancasila Day",
            description: "Hari Lahir Pancasila - Libur Nasional",
            type: "holiday"
        },
        {
            date: new Date(2025, 5, 10), // June 10, 2025
            title: "Ujian Tengah Semester",
            description: "Mulai periode Ujian Tengah Semester",
            type: "academic"
        },
        {
            date: new Date(2025, 6, 15), // July 15, 2025
            title: "Batas Pengumpulan Tugas Besar",
            description: "Tenggat waktu pengumpulan tugas besar mata kuliah inti",
            type: "academic"
        },
        {
            date: new Date(2025, 7, 17), // August 17, 2025
            title: "Hari Kemerdekaan RI",
            description: "Peringatan Kemerdekaan Republik Indonesia",
            type: "holiday"
        },
        {
            date: new Date(2025, 7, 25), // August 25, 2025
            title: "Ujian Akhir Semester",
            description: "Mulai periode Ujian Akhir Semester",
            type: "academic"
        }
    ];
    
    // Initialize mobile menu functionality
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('show');
            mobileMenuOverlay.classList.toggle('show');
        });
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function() {
            mainNav.classList.remove('show');
            mobileMenuOverlay.classList.remove('show');
        });
    }
    
    // Function to generate calendar
    function generateCalendar(month, year) {
        // Clear previous calendar
        calendar.innerHTML = '';
        
        // Update the current month/year display
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        currentMonthEl.textContent = `${monthNames[month]} ${year}`;
        
        // Create weekday headers
        const weekdays = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
        const calendarGrid = document.createElement('div');
        calendarGrid.className = 'calendar-grid';
        
        // Add weekday headers
        weekdays.forEach(day => {
            const weekdayEl = document.createElement('div');
            weekdayEl.className = 'calendar-weekday';
            weekdayEl.textContent = day;
            calendarGrid.appendChild(weekdayEl);
        });
        
        // Get first day of month and total days
        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Get days from previous month
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        // Calculate total cells needed (previous month days + current month days + next month days)
        let day = 1;
        let nextMonthDay = 1;
        const totalCells = 42; // 6 rows * 7 columns
        
        // Create day cells
        for (let i = 0; i < totalCells; i++) {
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day';
            
            const dateEl = document.createElement('div');
            dateEl.className = 'calendar-date';
            
            // Previous month days
            if (i < firstDayOfMonth) {
                const prevMonthDate = daysInPrevMonth - (firstDayOfMonth - i - 1);
                dateEl.textContent = prevMonthDate;
                dayEl.classList.add('outside-month');
                dayEl.dataset.date = `${year}-${month === 0 ? 12 : month}-${prevMonthDate}`;
            } 
            // Current month days
            else if (i < firstDayOfMonth + daysInMonth) {
                dateEl.textContent = day;
                
                // Check if it's today
                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayEl.classList.add('today');
                }
                
                dayEl.dataset.date = `${year}-${month + 1}-${day}`;
                
                // Add events to the day
                const currentDateObj = new Date(year, month, day);
                const dayEvents = academicEvents.filter(event => 
                    event.date.getDate() === currentDateObj.getDate() && 
                    event.date.getMonth() === currentDateObj.getMonth() && 
                    event.date.getFullYear() === currentDateObj.getFullYear()
                );
                
                if (dayEvents.length > 0) {
                    dayEvents.forEach(event => {
                        const eventEl = document.createElement('div');
                        eventEl.className = `calendar-event ${event.type}`;
                        eventEl.textContent = event.title;
                        eventEl.dataset.eventid = academicEvents.indexOf(event);
                        dayEl.appendChild(eventEl);
                        
                        // Add click event to show event details
                        eventEl.addEventListener('click', function() {
                            showEventDetails(event);
                        });
                    });
                }
                
                day++;
            } 
            // Next month days
            else {
                dateEl.textContent = nextMonthDay;
                dayEl.classList.add('outside-month');
                dayEl.dataset.date = `${year}-${month === 11 ? 1 : month + 2}-${nextMonthDay}`;
                nextMonthDay++;
            }
            
            dayEl.appendChild(dateEl);
            calendarGrid.appendChild(dayEl);
        }
        
        calendar.appendChild(calendarGrid);
        
        // Update event list
        updateEventList();
    }
    
    // Function to update event list
    function updateEventList() {
        eventList.innerHTML = '';
        
        // Filter events for the current month and future
        const now = new Date();
        const upcomingEvents = academicEvents.filter(event => 
            (event.date.getMonth() === currentMonth && event.date.getFullYear() === currentYear && event.date >= now) ||
            (event.date.getMonth() > currentMonth && event.date.getFullYear() === currentYear) ||
            (event.date.getFullYear() > currentYear)
        );
        
        // Sort events by date
        upcomingEvents.sort((a, b) => a.date - b.date);
        
        // Limit to next 5 events
        const nextEvents = upcomingEvents.slice(0, 5);
        
        if (nextEvents.length === 0) {
            const noEventEl = document.createElement('p');
            noEventEl.textContent = 'Tidak ada event dalam waktu dekat.';
            eventList.appendChild(noEventEl);
        } else {
            nextEvents.forEach(event => {
                const eventItemEl = document.createElement('div');
                eventItemEl.className = `event-item ${event.type}`;
                
                const eventDateEl = document.createElement('div');
                eventDateEl.className = 'event-date';
                
                const eventDayEl = document.createElement('div');
                eventDayEl.className = 'event-day';
                eventDayEl.textContent = event.date.getDate();
                
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", 
                                   "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                const eventMonthEl = document.createElement('div');
                eventMonthEl.className = 'event-month';
                eventMonthEl.textContent = `${monthNames[event.date.getMonth()]} ${event.date.getFullYear()}`;
                
                eventDateEl.appendChild(eventDayEl);
                eventDateEl.appendChild(eventMonthEl);
                
                const eventInfoEl = document.createElement('div');
                eventInfoEl.className = 'event-info';
                
                const eventTitleEl = document.createElement('h4');
                eventTitleEl.textContent = event.title;
                
                const eventDescEl = document.createElement('p');
                eventDescEl.textContent = event.description;
                
                eventInfoEl.appendChild(eventTitleEl);
                eventInfoEl.appendChild(eventDescEl);
                
                eventItemEl.appendChild(eventDateEl);
                eventItemEl.appendChild(eventInfoEl);
                
                eventList.appendChild(eventItemEl);
            });
        }
    }
    
    // Function to show event details
    function showEventDetails(event) {
        // You could implement a modal or expand the details in the sidebar
        console.log("Event details:", event);
    }
    
    // Initialize calendar
    generateCalendar(currentMonth, currentYear);
    
    // Event listeners for calendar navigation
    prevMonthBtn.addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        generateCalendar(currentMonth, currentYear);
    });
    
    nextMonthBtn.addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        generateCalendar(currentMonth, currentYear);
    });
});