document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
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

    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const searchBox = document.getElementById('gallery-search');

    const showItem = (el, show) => {
        el.style.display = show ? 'block' : 'none';
        if (show) {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }
    };

    const applyFilter = (filter) => {
        galleryItems.forEach(item => {
            const cat = item.getAttribute('data-category');
            showItem(item, filter === 'all' || cat === filter);
        });
    };

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterValue = this.getAttribute('data-filter');
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            applyFilter(filterValue);
        });
    });

    if (searchBox) {
        searchBox.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            galleryItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const desc = item.querySelector('.gallery-info p').textContent.toLowerCase();
                showItem(item, title.includes(q) || desc.includes(q));
            });
        });
    }

    const modal = document.getElementById('galleryModal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDate = document.getElementById('modal-date');
    const modalDescription = document.getElementById('modal-description');
    const closeModal = document.querySelector('.close-modal');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const viewButtons = document.querySelectorAll('.view-btn');

    const visibleItems = () => Array.from(galleryItems).filter(el => {
        return window.getComputedStyle(el).display !== 'none';
    });

    let currentIndex = 0;

    function openModal(item) {
        const img = item.querySelector('img').src;
        const title = item.querySelector('.overlay-content h3').textContent;
        const date = item.querySelector('.overlay-content p').textContent;
        const description = item.querySelector('.gallery-info p').textContent;
        modalImage.src = img; modalTitle.textContent = title;
        modalDate.textContent = date; modalDescription.textContent = description;
        modal.style.display = 'block';
    }

    function navigateGallery(dir) {
        const vis = visibleItems();
        currentIndex += dir;
        if (currentIndex < 0) currentIndex = vis.length - 1;
        if (currentIndex >= vis.length) currentIndex = 0;
        openModal(vis[currentIndex]);
    }

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.gallery-item');
            const vis = visibleItems();
            currentIndex = vis.indexOf(item);
            openModal(item);
        });
    });

    closeModal?.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });
    prevBtn?.addEventListener('click', () => navigateGallery(-1));
    nextBtn?.addEventListener('click', () => navigateGallery(1));

    // Initial
    applyFilter('all');
});