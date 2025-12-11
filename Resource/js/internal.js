// Menunggu hingga seluruh halaman HTML dimuat sebelum menjalankan skrip
document.addEventListener('DOMContentLoaded', () => {

    // --- FUNGSI UNTUK MENU MOBILE (HAMBURGER) ---
    // Kode menu mobile Anda yang sudah ada di sini
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    if (mobileMenuBtn) {
        const toggleMenu = () => {
            mainNav.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
        };
        mobileMenuBtn.addEventListener('click', toggleMenu);
        mobileMenuOverlay.addEventListener('click', toggleMenu);
    }
    
    // --- FUNGSI ACCORDION (PERBAIKAN FINAL) ---
    // Logika ini lebih stabil dan mencegah konten terpotong
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        const content = item.querySelector('.accordion-content');

        header.addEventListener('click', () => {
            const isItemActive = item.classList.contains('active');

            // Pertama, tutup semua item lain
            accordionItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.accordion-content').style.maxHeight = null;
                }
            });

            // Kemudian, buka atau tutup item yang diklik
            if (!isItemActive) {
                item.classList.add('active');
                // Atur maxHeight sesuai dengan tinggi scroll konten
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                item.classList.remove('active');
                content.style.maxHeight = null;
            }
        });
    });
});
