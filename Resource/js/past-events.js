// Filter + Search untuk halaman Past Events
// Catatan: Hamburger ditangani global oleh Resource/js/index.js
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('searchInput');
    const eventCards = document.querySelectorAll('.event-card');

    const filterAndSearch = () => {
        const term = (searchInput?.value || '').toLowerCase();
        const activeBtn = document.querySelector('.filter-btn.active');
        const activeCategory = activeBtn ? activeBtn.dataset.category : 'semua';

        eventCards.forEach(card => {
            const cardCategory = card.dataset.category || 'lainnya';
            const title = (card.querySelector('h3')?.textContent || '').toLowerCase();
            const desc  = (card.querySelector('p')?.textContent || '').toLowerCase();

            const categoryMatch = (activeCategory === 'semua') || (cardCategory === activeCategory);
            const searchMatch   = title.includes(term) || desc.includes(term);

            card.style.display = (categoryMatch && searchMatch) ? 'block' : 'none';
        });
    };

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterAndSearch();
        });
    });

    if (searchInput) searchInput.addEventListener('input', filterAndSearch);

    // Inisialisasi awal
    filterAndSearch();
});