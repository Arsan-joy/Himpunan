// Struktur Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeSearch();
    initializeFilters();
    initializeAnimations();
    initializeStatsCounter();
});

// Search Functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const kabinetCards = document.querySelectorAll('.kabinet-card');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            kabinetCards.forEach(card => {
                const kabinetName = card.querySelector('.kabinet-name').textContent.toLowerCase();
                const kabinetDescription = card.querySelector('.kabinet-description').textContent.toLowerCase();
                const kabinetYear = card.querySelector('.kabinet-year')?.textContent.toLowerCase() || '';
                
                const isVisible = kabinetName.includes(searchTerm) || 
                                kabinetDescription.includes(searchTerm) || 
                                kabinetYear.includes(searchTerm);
                
                if (isVisible) {
                    card.classList.remove('hidden');
                    card.style.display = 'block';
                } else {
                    card.classList.add('hidden');
                    card.style.display = 'none';
                }
            });

            // Update animation delays for visible cards
            updateAnimationDelays();
        });
    }
}

// Filter Functionality
function initializeFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const kabinetCards = document.querySelectorAll('.kabinet-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            kabinetCards.forEach(card => {
                const status = card.getAttribute('data-status');
                let shouldShow = false;

                switch(filter) {
                    case 'all':
                        shouldShow = true;
                        break;
                    case 'current':
                        shouldShow = status === 'current';
                        break;
                    case 'past':
                        shouldShow = status === 'past';
                        break;
                }

                if (shouldShow) {
                    card.classList.remove('hidden');
                    card.style.display = 'block';
                } else {
                    card.classList.add('hidden');
                    card.style.display = 'none';
                }
            });

            // Update animation delays for visible cards
            updateAnimationDelays();
            
            // Clear search when filtering
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
            }
        });
    });
}

// Update animation delays for visible cards
function updateAnimationDelays() {
    const visibleCards = document.querySelectorAll('.kabinet-card:not(.hidden)');
    
    visibleCards.forEach((card, index) => {
        card.style.animationDelay = `${(index + 1) * 0.1}s`;
        // Trigger reanimation
        card.style.animation = 'none';
        card.offsetHeight; // Trigger reflow
        card.style.animation = 'fadeInUp 0.6s ease forwards';
    });
}

// Initialize scroll animations
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);

    // Observe kabinet cards
    const kabinetCards = document.querySelectorAll('.kabinet-card');
    kabinetCards.forEach(card => {
        observer.observe(card);
    });

    // Observe stats cards
    const statsCards = document.querySelectorAll('.struktur-stats-section .stat-card');
    statsCards.forEach(card => {
        observer.observe(card);
    });
}

// Stats Counter Animation
function initializeStatsCounter() {
    const statNumbers = document.querySelectorAll('.struktur-stats-section .stat-number');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                entry.target.classList.add('counted');
                animateCounter(entry.target);
            }
        });
    }, observerOptions);

    statNumbers.forEach(number => {
        statsObserver.observe(number);
    });
}

// Counter animation function
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 seconds
    const step = target / (duration / 16); // 60fps
    let current = 0;

    const updateCounter = () => {
        current += step;
        if (current < target) {
            element.textContent = Math.floor(current);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    };

    updateCounter();
}

// Add smooth scrolling for internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading states for cabinet images
document.querySelectorAll('.kabinet-image img').forEach(img => {
    img.addEventListener('load', function() {
        this.style.opacity = '1';
    });
    
    img.addEventListener('error', function() {
        this.style.opacity = '1';
        // You can add a default image or placeholder here
        console.log('Image failed to load:', this.src);
    });
});

// Add keyboard navigation for accessibility
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Clear search
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    }
});

// Add touch/swipe support for mobile
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
});

document.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        // Add swipe functionality if needed
        // For example, navigate between filter options
        const activeFilter = document.querySelector('.filter-btn.active');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const activeIndex = Array.from(filterButtons).indexOf(activeFilter);
        
        if (diff > 0 && activeIndex < filterButtons.length - 1) {
            // Swipe left - next filter
            filterButtons[activeIndex + 1].click();
        } else if (diff < 0 && activeIndex > 0) {
            // Swipe right - previous filter
            filterButtons[activeIndex - 1].click();
        }
    }
}