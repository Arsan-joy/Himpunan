// Pastikan file ini termuat. Cek di DevTools > Network tidak 404.
document.addEventListener('DOMContentLoaded', () => {
  // Counter animation
  const counters = document.querySelectorAll('.stat-number[data-count]');
  counters.forEach(el => {
    const target = parseInt(el.dataset.count || '0', 10);
    let current = 0;
    const step = Math.max(1, Math.round(target / 60));
    const tick = () => {
      current += step;
      if (current >= target) {
        el.textContent = target.toString();
      } else {
        el.textContent = current.toString();
        requestAnimationFrame(tick);
      }
    };
    requestAnimationFrame(tick);
  });

  // Filter divisions
  const filterBtns = document.querySelectorAll('.division-filter .filter-btn');
  const cards = document.querySelectorAll('.division-card');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.filter || 'all';
      cards.forEach(card => {
        const c = card.dataset.category || 'other';
        card.style.display = (cat === 'all' || cat === c) ? '' : 'none';
      });
    });
  });

  // Toggle division content (accordion)
  const toggles = document.querySelectorAll('.toggle-btn');
  toggles.forEach(t => {
    t.addEventListener('click', () => {
      const id = t.dataset.target;
      const content = document.getElementById(id);
      if (!content) return;
      const open = content.classList.toggle('open');
      t.classList.toggle('open', open);
      // Accessibility
      t.setAttribute('aria-expanded', String(open));
      content.style.maxHeight = open ? content.scrollHeight + 'px' : '0px';
    });
  });

  // Pre-expand all by default (seperti behavior pada HTML preview Anda)
  document.querySelectorAll('.division-content').forEach(c => {
    c.classList.add('open');
    c.style.maxHeight = c.scrollHeight + 'px';
  });
});