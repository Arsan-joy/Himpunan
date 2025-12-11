// Mobile menu + global interactions
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn  = document.getElementById("mobileMenuBtn");
    const nav      = document.getElementById("mainNav");
    const overlay  = document.getElementById("mobileMenuOverlay");
    const body     = document.body;

    function openMenu(){
        nav?.classList.add("active");
        overlay?.classList.add("active");
        body?.classList.add("no-scroll");
    }
    function closeMenu(){
        nav?.classList.remove("active");
        overlay?.classList.remove("active");
        body?.classList.remove("no-scroll");
    }

    if (menuBtn && nav && overlay) {
        menuBtn.addEventListener("click", () => {
            const willOpen = !nav.classList.contains('active');
            if (willOpen) openMenu(); else closeMenu();
        });

        overlay.addEventListener("click", closeMenu);

        // Tutup saat klik salah satu link menu (khusus mobile)
        nav.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));

        // Tutup otomatis saat melebar ke desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 980) closeMenu();
        });

        // ESC untuk menutup
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMenu();
        });
    }

    // Banner Slideshow (jika ada di halaman)
    const bannerSlides = document.querySelectorAll('.banner-slide');
    const bannerDots = document.querySelectorAll('.banner-dot');
    let currentBannerSlide = 0;
    let bannerInterval;

    function showBannerSlide(index) {
        bannerSlides.forEach(slide => slide.classList.remove('active'));
        bannerDots.forEach(dot => dot.classList.remove('active'));
        if (bannerSlides[index]) bannerSlides[index].classList.add('active');
        if (bannerDots[index]) bannerDots[index].classList.add('active');
        currentBannerSlide = index;
    }
    function nextBannerSlide() {
        let nextSlide = currentBannerSlide + 1;
        if (nextSlide >= bannerSlides.length) nextSlide = 0;
        showBannerSlide(nextSlide);
    }
    function startBannerSlideshow() {
        stopBannerSlideshow();
        bannerInterval = setInterval(nextBannerSlide, 5000);
    }
    function stopBannerSlideshow() {
        clearInterval(bannerInterval);
    }
    if (bannerDots.length > 0 && bannerSlides.length > 0) {
        bannerDots.forEach(dot => {
            dot.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-index'));
                showBannerSlide(slideIndex);
                stopBannerSlideshow();
                startBannerSlideshow();
            });
        });
        startBannerSlideshow();
    }

    // Event Cards Slideshow (jika ada di halaman)
    const eventWrapper = document.querySelector('.event-wrapper');
    const eventCards = document.querySelectorAll('.event-card');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    if (eventWrapper && eventCards.length > 0 && prevBtn && nextBtn) {
        let currentPosition = 0;
        let cardsToShow = 1;

        function getCardsToShow() {
            if (window.innerWidth < 768) return 1;
            else if (window.innerWidth < 1024) return 2;
            else return 3;
        }
        function updateButtonStates() {
            prevBtn.disabled = currentPosition === 0;
            nextBtn.disabled = currentPosition >= eventCards.length - cardsToShow;
        }
        function initEventSlider() {
            cardsToShow = getCardsToShow();
            if (currentPosition > eventCards.length - cardsToShow) {
                currentPosition = Math.max(0, eventCards.length - cardsToShow);
            }
            const cardWidth = eventCards[0].offsetWidth + 30;
            eventWrapper.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
            updateButtonStates();
        }
        prevBtn.addEventListener('click', function() {
            if (currentPosition > 0) {
                currentPosition--;
                const cardWidth = eventCards[0].offsetWidth + 30;
                eventWrapper.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
                updateButtonStates();
            }
        });
        nextBtn.addEventListener('click', function() {
            if (currentPosition < eventCards.length - cardsToShow) {
                currentPosition++;
                const cardWidth = eventCards[0].offsetWidth + 30;
                eventWrapper.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
                updateButtonStates();
            }
        });
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(initEventSlider, 250);
        });
        initEventSlider();
    }

    // Counter animation (jika ada di halaman)
    const counters = document.querySelectorAll('.counter');
    const statCards = document.querySelectorAll('.stat-card');
    if (counters.length > 0) {
        function animateCounter(counter, target, duration = 400) {
            let startValue = 0;
            const increment = target / duration;
            const updateCount = () => {
                if (startValue < target) {
                    startValue += increment;
                    counter.innerText = Math.min(Math.ceil(startValue), target);
                    requestAnimationFrame(updateCount);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        }
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            animateCounter(counter, target);
        });
        if (statCards.length > 0) {
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const counter = this.querySelector('.counter');
                    if (counter) {
                        const target = +counter.getAttribute('data-target');
                        counter.innerText = '0';
                        animateCounter(counter, target, 20);
                    }
                });
            });
        }
    }
});