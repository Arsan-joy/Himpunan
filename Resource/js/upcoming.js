// Menunggu hingga seluruh konten HTML dimuat sebelum menjalankan skrip
document.addEventListener('DOMContentLoaded', function() {

    // Fokus: dropdown Event pada mobile (hamburger ditangani global di index.js)
document.addEventListener('DOMContentLoaded', function() {
    const dropdownTriggers = document.querySelectorAll('.dropdown > a');

    dropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('open');
                // tutup dropdown lain
                document.querySelectorAll('.dropdown').forEach(d => {
                    if (d !== parent) d.classList.remove('open');
                });
            }
        });
    });
});

    // --- Fungsionalitas untuk Dropdown Klik di Mobile ---
    const dropdowns = document.querySelectorAll('.dropdown > a');

    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            // Cek apakah tampilan saat ini adalah mobile (sesuaikan dengan breakpoint CSS Anda)
            if (window.innerWidth < 992) {
                // Mencegah link default agar tidak mengarah ke halaman lain
                event.preventDefault(); 
                
                const parentDropdown = this.parentElement;
                
                // Toggle kelas 'open' untuk dropdown yang diklik
                parentDropdown.classList.toggle('open');
                
                // (Opsional) Menutup dropdown lain yang sedang terbuka
                const allDropdowns = document.querySelectorAll('.dropdown');
                allDropdowns.forEach(d => {
                    if (d !== parentDropdown) {
                        d.classList.remove('open');
                    }
                });
            }
        });
    });

});