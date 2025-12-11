document.addEventListener('DOMContentLoaded', () => {
  // FILTER DIVISI
  const filterBtns = document.querySelectorAll('.division-filter .filter-btn');
  const divisionCards = document.querySelectorAll('.division-card');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.filter || 'all';
      divisionCards.forEach(card => {
        const cc = card.dataset.category || '';
        card.style.display = (cat === 'all' || cat === cc) ? '' : 'none';
      });
    });
  });

  // TOGGLE DIVISION CONTENT
  document.querySelectorAll('.toggle-btn').forEach(t => {
    t.addEventListener('click', () => {
      const id = t.dataset.target;
      const el = document.getElementById(id);
      if (!el) return;
      const open = el.classList.toggle('open');
      t.setAttribute('aria-expanded', String(open));
      el.style.maxHeight = open ? el.scrollHeight + 'px' : '0px';
      const icon = t.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-chevron-down', !open);
        icon.classList.toggle('fa-chevron-up', open);
      }
    });
  });

  // Expand all on initial load
  document.querySelectorAll('.division-content').forEach(el => {
    el.classList.add('open');
    el.style.maxHeight = el.scrollHeight + 'px';
  });
});

/* NOTE:
   Kode hamburger DIHAPUS dari sini.
   Gunakan Resource/js/index.js yang sudah global.
*/