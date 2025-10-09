// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById("mobileMenuBtn");
    const nav = document.getElementById("mainNav");
    const overlay = document.getElementById("mobileMenuOverlay");

    if (menuBtn && nav && overlay) {
        menuBtn.addEventListener("click", () => {
            nav.classList.toggle("active");
            overlay.classList.toggle("active");
        });

        overlay.addEventListener("click", () => {
            nav.classList.remove("active");
            overlay.classList.remove("active");
        });
    }

    // Banner Slideshow
    const bannerSlides = document.querySelectorAll('.banner-slide');
    const bannerDots = document.querySelectorAll('.banner-dot');
    let currentBannerSlide = 0;
    let bannerInterval;

    function showBannerSlide(index) {
        // Hide all slides
        bannerSlides.forEach(slide => {
            slide.classList.remove('active');
        });
        bannerDots.forEach(dot => {
            dot.classList.remove('active');
        });

        // Show selected slide
        bannerSlides[index].classList.add('active');
        bannerDots[index].classList.add('active');
        currentBannerSlide = index;
    }

    function nextBannerSlide() {
        let nextSlide = currentBannerSlide + 1;
        if (nextSlide >= bannerSlides.length) {
            nextSlide = 0;
        }
        showBannerSlide(nextSlide);
    }

    // Start automatic slideshow
    function startBannerSlideshow() {
        stopBannerSlideshow(); // Clear any existing interval first
        bannerInterval = setInterval(nextBannerSlide, 5000); // Change slide every 5 seconds
    }

    // Stop slideshow on user interaction
    function stopBannerSlideshow() {
        clearInterval(bannerInterval);
    }

    // Add click events to dots if they exist
    if (bannerDots.length > 0 && bannerSlides.length > 0) {
        bannerDots.forEach(dot => {
            dot.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-index'));
                showBannerSlide(slideIndex);
                stopBannerSlideshow();
                startBannerSlideshow();
            });
        });

        // Start the slideshow
        startBannerSlideshow();
    }

    // Event Cards Slideshow
    const eventWrapper = document.querySelector('.event-wrapper');
    const eventCards = document.querySelectorAll('.event-card');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    if (eventWrapper && eventCards.length > 0 && prevBtn && nextBtn) {
        let currentPosition = 0;
        let cardsToShow = 1; // Default value

        // Calculate how many cards to show based on screen width
        function getCardsToShow() {
            if (window.innerWidth < 768) {
                return 1;
            } else if (window.innerWidth < 1024) {
                return 2;
            } else {
                return 3;
            }
        }

        // Update button states
        function updateButtonStates() {
            prevBtn.disabled = currentPosition === 0;
            nextBtn.disabled = currentPosition >= eventCards.length - cardsToShow;
        }

        // Initialize card slider
        function initEventSlider() {
            cardsToShow = getCardsToShow();
            // Make sure currentPosition is valid after resize
            if (currentPosition > eventCards.length - cardsToShow) {
                currentPosition = Math.max(0, eventCards.length - cardsToShow);
            }
            
            const cardWidth = eventCards[0].offsetWidth + 30; // card width + margin
            eventWrapper.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
            updateButtonStates();
        }

        // Move to previous cards
        prevBtn.addEventListener('click', function() {
            if (currentPosition > 0) {
                currentPosition--;
                const cardWidth = eventCards[0].offsetWidth + 30; // Recalculate in case window was resized
                eventWrapper.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
                updateButtonStates();
            }
        });

        // Move to next cards
        nextBtn.addEventListener('click', function() {
            if (currentPosition < eventCards.length - cardsToShow) {
                currentPosition++;
                const cardWidth = eventCards[0].offsetWidth + 30; // Recalculate in case window was resized
                eventWrapper.style.transform = `translateX(-${currentPosition * cardWidth}px)`;
                updateButtonStates();
            }
        });

        // Update on window resize with debounce
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(initEventSlider, 250);
        });

        // Initialize on page load
        initEventSlider();
    }

    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const statCards = document.querySelectorAll('.stat-card');
    
    if (counters.length > 0) {
        // Counter animation function
        function animateCounter(counter, target, duration = 400) {
            let startValue = 0;
            const increment = target / duration;
            
            const updateCount = () => {
                if (startValue < target) {
                    startValue += increment;
                    // Ensure we don't exceed target and round to avoid decimals
                    counter.innerText = Math.min(Math.ceil(startValue), target);
                    requestAnimationFrame(updateCount);
                } else {
                    counter.innerText = target;
                }
            };
            
            updateCount();
        }
        
        // Start the animations for all counters
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            animateCounter(counter, target);
        });
        
        // Add hover effects for stat cards
        if (statCards.length > 0) {
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    // Reset and restart counter animation on hover
                    const counter = this.querySelector('.counter');
                    if (counter) {
                        const target = +counter.getAttribute('data-target');
                        counter.innerText = '0';
                        animateCounter(counter, target, 20); // Faster on hover
                    }
                });
            });
        }
    }
});