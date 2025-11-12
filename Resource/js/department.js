document.addEventListener('DOMContentLoaded', () => {
  // Filter
  const filterBtns = document.querySelectorAll('.division-filter .filter-btn');
  const cards = document.querySelectorAll('.division-card');
  filterBtns.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      filterBtns.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.filter || 'all';
      cards.forEach(c=>{
        const cc = c.dataset.category || '';
        c.style.display = (cat==='all'||cat===cc) ? '' : 'none';
      });
    });
  });

  // Toggle (accordion)
  document.querySelectorAll('.toggle-btn').forEach(t=>{
    t.addEventListener('click',()=>{
      const id = t.dataset.target;
      const el = document.getElementById(id);
      if(!el) return;
      const open = el.classList.toggle('open');
      t.setAttribute('aria-expanded', String(open));
      el.style.maxHeight = open ? el.scrollHeight+'px' : '0px';
      t.querySelector('i')?.classList.toggle('fa-chevron-down', !open);
      t.querySelector('i')?.classList.toggle('fa-chevron-up', open);
    });
  });

  // Expand all awal
  document.querySelectorAll('.division-content').forEach(el=>{
    el.classList.add('open');
    el.style.maxHeight = el.scrollHeight + 'px';
  });
});