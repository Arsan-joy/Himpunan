document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuBtn = document.getElementById("mobileMenuBtn");
    const nav = document.getElementById("mainNav");
    const overlay = document.getElementById("mobileMenuOverlay");
    
    if (menuBtn && nav && overlay) {
      menuBtn.addEventListener("click", () => {
        nav.classList.toggle("active");
        overlay.classList.toggle("active");
        // Toggle ARIA expanded attribute for accessibility
        const isExpanded = nav.classList.contains("active");
        menuBtn.setAttribute("aria-expanded", isExpanded);
      });
      
      overlay.addEventListener("click", () => {
        nav.classList.remove("active");
        overlay.classList.remove("active");
        menuBtn.setAttribute("aria-expanded", "false");
      });
    }
    
    // Handle dropdown menus for mobile
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    
    dropdownBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const parent = this.parentElement;
        // Close all other dropdowns
        document.querySelectorAll('.dropdown').forEach(item => {
          if (item !== parent) {
            item.classList.remove('active');
          }
        });
        // Toggle current dropdown
        parent.classList.toggle('active');
      });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown').forEach(item => {
          item.classList.remove('active');
        });
      }
    });
  });