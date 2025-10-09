// Jaring pengaman: jika ada skrip yang menyembunyikan [data-aos], paksa tampil.
// Juga buka semua konten divisi secara default agar tidak terlihat kosong.
document.addEventListener('DOMContentLoaded', () => {
  // Force show AOS-marked elements
  document.querySelectorAll('[data-aos]').forEach(el => {
    el.style.opacity = '1';
    el.style.transform = 'none';
  });

  // Open all division contents by default
  document.querySelectorAll('.division-content').forEach(c => {
    c.classList.add('open');
    c.style.maxHeight = 'none';
    c.style.opacity = '1';
    c.style.transform = 'none';
  });
});