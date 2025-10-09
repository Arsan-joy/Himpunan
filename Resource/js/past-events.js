// Menunggu hingga seluruh konten halaman siap sebelum menjalankan script
document.addEventListener('DOMContentLoaded', () => {

    // Ambil semua elemen yang dibutuhkan
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('searchInput');
    const eventCards = document.querySelectorAll('.event-card');

    // Fungsi untuk memfilter kartu berdasarkan kategori dan pencarian
    const filterAndSearch = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const activeCategory = document.querySelector('.filter-btn.active').dataset.category;

        eventCards.forEach(card => {
            const cardCategory = card.dataset.category;
            const cardTitle = card.querySelector('h3').textContent.toLowerCase();
            const cardDescription = card.querySelector('p').textContent.toLowerCase();

            // Cek kondisi kategori
            const categoryMatch = (activeCategory === 'semua') || (cardCategory === activeCategory);
            
            // Cek kondisi pencarian (judul atau deskripsi)
            const searchMatch = cardTitle.includes(searchTerm) || cardDescription.includes(searchTerm);

            // Tampilkan atau sembunyikan kartu berdasarkan kedua kondisi
            if (categoryMatch && searchMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    };

    // Tambahkan event listener untuk setiap tombol filter
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Hapus kelas 'active' dari semua tombol
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Tambahkan kelas 'active' ke tombol yang diklik
            button.classList.add('active');
            
            // Panggil fungsi filter utama
            filterAndSearch();
        });
    });

    // Tambahkan event listener untuk input pencarian
    searchInput.addEventListener('input', filterAndSearch);

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
