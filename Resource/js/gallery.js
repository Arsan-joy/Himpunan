document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function() {
            mainNav.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    }

    // Gallery Filtering
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const searchBox = document.getElementById('gallery-search');
    
    // Initialize Isotope-like filtering
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterValue = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            galleryItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Search functionality
    if (searchBox) {
        searchBox.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            galleryItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const description = item.querySelector('.gallery-info p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Lightbox Modal
    const modal = document.getElementById('galleryModal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDate = document.getElementById('modal-date');
    const modalDescription = document.getElementById('modal-description');
    const closeModal = document.querySelector('.close-modal');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const viewButtons = document.querySelectorAll('.view-btn');
    
    let currentIndex = 0;
    const visibleItems = () => Array.from(galleryItems).filter(item => item.style.display !== 'none');
    
    // Open modal when view button is clicked
    viewButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const item = this.closest('.gallery-item');
            const visItems = visibleItems();
            currentIndex = visItems.indexOf(item);
            
            openModal(item);
        });
    });
    
    // Close modal when close button is clicked
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside of modal content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Navigate through images
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            navigateGallery(-1);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            navigateGallery(1);
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (modal.style.display === 'block') {
            if (event.key === 'ArrowLeft') {
                navigateGallery(-1);
            } else if (event.key === 'ArrowRight') {
                navigateGallery(1);
            } else if (event.key === 'Escape') {
                modal.style.display = 'none';
            }
        }
    });
    
    // Helper function to open the modal with item data
    function openModal(item) {
        const img = item.querySelector('img').src;
        const title = item.querySelector('.overlay-content h3').textContent;
        const date = item.querySelector('.overlay-content p').textContent;
        const description = item.querySelector('.gallery-info p').textContent;
        
        modalImage.src = img;
        modalTitle.textContent = title;
        modalDate.textContent = date;
        modalDescription.textContent = description;
        
        modal.style.display = 'block';
    }
    
    // Helper function to navigate through gallery items
    function navigateGallery(direction) {
        const visItems = visibleItems();
        
        // Update current index
        currentIndex += direction;
        
        // Loop back to start/end if needed
        if (currentIndex < 0) {
            currentIndex = visItems.length - 1;
        } else if (currentIndex >= visItems.length) {
            currentIndex = 0;
        }
        
        // Display new item
        openModal(visItems[currentIndex]);
    }

    // Animation on scroll
    const animateOnScroll = function() {
        const items = document.querySelectorAll('.gallery-item');
        
        items.forEach(item => {
            const itemPosition = item.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if (itemPosition < screenPosition) {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }
        });
    };
    
    // Initial check on page load
    animateOnScroll();
    
    // Check on scroll
    window.addEventListener('scroll', animateOnScroll);
});