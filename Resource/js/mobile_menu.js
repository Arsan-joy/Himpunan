document.addEventListener('DOMContentLoaded', function() {
  // Mobile Menu Toggle
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mainNav = document.getElementById('mainNav');
  const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function() {
      mainNav.classList.toggle('active');
      mobileMenuOverlay.classList.toggle('active');
    });
  }

  if (mobileMenuOverlay) {
    mobileMenuOverlay.addEventListener('click', function() {
      mainNav.classList.remove('active');
      mobileMenuOverlay.classList.remove('active');
    });
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href !== '#' && href.length > 1) {
        e.preventDefault();
        const targetElement = document.querySelector(href);
        if (targetElement) {
          targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });

  // Animate on scroll
  function animateOnScroll() {
    const elements = document.querySelectorAll('[data-aos]');
    elements.forEach(element => {
      const rect = element.getBoundingClientRect();
      if (rect.top < window.innerHeight - 100 && rect.bottom > 0) {
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
      }
    });
  }

  // Initialize animated elements
  document.querySelectorAll('[data-aos]').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
  });

  window.addEventListener('scroll', animateOnScroll);
  animateOnScroll();

  // Hover effect init
  document.querySelectorAll('.team-card, .program-card, .responsibility-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
    });
  });

  // Parallax header
  window.addEventListener('scroll', function() {
    const header = document.querySelector('.department-header');
    if (header) {
      const scrolled = window.pageYOffset;
      header.style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
    }
  });

  // Simple page fade-in
  window.addEventListener('load', function() {
    document.body.style.opacity = '0';
    setTimeout(function() {
      document.body.style.transition = 'opacity 0.5s ease';
      document.body.style.opacity = '1';
    }, 100);
  });

  // Dropdown for mobile
  const dropdowns = document.querySelectorAll('.dropdown');
  dropdowns.forEach(dropdown => {
    const link = dropdown.querySelector(':scope > a');
    const dropdownContent = dropdown.querySelector('.dropdown-content');
    if (link && dropdownContent) {
      link.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          dropdown.classList.toggle('active');
          dropdowns.forEach(other => {
            if (other !== dropdown) other.classList.remove('active');
          });
        }
      });
    }
  });

  // Stagger animation delay
  const stagger = document.querySelectorAll('.team-grid > *, .programs-grid > *, .responsibilities-grid > *');
  stagger.forEach((item, index) => {
    item.style.animationDelay = `${index * 0.1}s`;
  });
});

// Optional cursor-based tilt
document.addEventListener('mousemove', function(e) {
  const cards = document.querySelectorAll('.team-card, .program-card');
  cards.forEach(card => {
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    if (x >= 0 && x <= rect.width && y >= 0 && y <= rect.height) {
      const dx = (x - rect.width / 2) / (rect.width / 2);
      const dy = (y - rect.height / 2) / (rect.height / 2);
      card.style.transform = `perspective(1000px) rotateY(${dx * 5}deg) rotateX(${-dy * 5}deg) translateY(-10px)`;
    } else {
      card.style.transform = '';
    }
  });
});