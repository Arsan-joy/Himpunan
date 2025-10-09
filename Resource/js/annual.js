document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mainNav = document.getElementById('mainNav');
    const dropdowns = document.querySelectorAll('.dropdown');
  
    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        mobileMenuOverlay.style.display = mainNav.classList.contains('active') ? 'block' : 'none';
        document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
      });
    }
  
    if (mobileMenuOverlay) {
      mobileMenuOverlay.addEventListener('click', function() {
        mainNav.classList.remove('active');
        mobileMenuOverlay.style.display = 'none';
        document.body.style.overflow = '';
      });
    }
  
    // Mobile Dropdown Toggle
    dropdowns.forEach(dropdown => {
      const dropdownLink = dropdown.querySelector('a');
      
      if (window.innerWidth <= 768) {
        dropdownLink.addEventListener('click', function(e) {
          e.preventDefault();
          dropdown.classList.toggle('active');
          
          // Close other open dropdowns
          dropdowns.forEach(otherDropdown => {
            if (otherDropdown !== dropdown && otherDropdown.classList.contains('active')) {
              otherDropdown.classList.remove('active');
            }
          });
        });
      }
    });
  
    // Testimonial Slider
    const testimonialSlider = document.getElementById('testimonialSlider');
    
    if (testimonialSlider) {
      const slides = testimonialSlider.querySelectorAll('.testimonial-slide');
      const dots = document.getElementById('sliderDots').querySelectorAll('.dot');
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      
      let currentSlide = 0;
      
      // Initialize first slide
      slides[0].classList.add('active');
      
      // Function to show slide
      function showSlide(n) {
        // Hide all slides
        slides.forEach(slide => {
          slide.classList.remove('active');
        });
        
        // Remove active class from dots
        dots.forEach(dot => {
          dot.classList.remove('active');
        });
        
        // Show current slide
        slides[n].classList.add('active');
        dots[n].classList.add('active');
      }
      
      // Next slide
      function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
      }
      
      // Previous slide
      function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
      }
      
      // Event listeners
      if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
      }
      
      if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
      }
      
      // Dot navigation
      dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
          currentSlide = index;
          showSlide(currentSlide);
        });
      });
      
      // Auto slide (optional)
      let slideInterval = setInterval(nextSlide, 5000);
      
      // Pause auto slide on hover
      testimonialSlider.addEventListener('mouseenter', function() {
        clearInterval(slideInterval);
      });
      
      testimonialSlider.addEventListener('mouseleave', function() {
        slideInterval = setInterval(nextSlide, 5000);
      });
    }
  
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        if (this.getAttribute('href') !== '#') {
          e.preventDefault();
          const targetId = this.getAttribute('href');
          const targetElement = document.querySelector(targetId);
          
          if (targetElement) {
            const headerHeight = document.querySelector('header').offsetHeight;
            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
            
            window.scrollTo({
              top: targetPosition,
              behavior: 'smooth'
            });
            
            // Close mobile menu if open
            if (mainNav.classList.contains('active')) {
              mainNav.classList.remove('active');
              mobileMenuOverlay.style.display = 'none';
              document.body.style.overflow = '';
            }
          }
        }
      });
    });
  
    // Animate sections on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
  
    const observer = new IntersectionObserver(function(entries, observer) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);
  
    // Observe elements with animation classes
    document.querySelectorAll('.program-card, .timeline-item, .testimonial-content, .cta').forEach(el => {
      observer.observe(el);
    });
  
    // Add animation classes to elements
    document.querySelectorAll('.program-card').forEach((el, index) => {
      el.style.animationDelay = `${index * 0.2}s`;
    });
  
    document.querySelectorAll('.timeline-item').forEach((el, index) => {
      el.style.animationDelay = `${index * 0.1}s`;
    });
  
    // Form validation (if needed)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let valid = true;
        
        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            valid = false;
            field.classList.add('error');
          } else {
            field.classList.remove('error');
          }
        });
        
        if (!valid) {
          e.preventDefault();
          alert('Please fill all required fields');
        }
      });
    });
  
    // Add CSS class for animating elements when in viewport
    const addAnimationClass = function() {
      const animatedElements = document.querySelectorAll('.program-card, .timeline-item, .testimonial-content');
      
      animatedElements.forEach(el => {
        const elementPosition = el.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementPosition < windowHeight - 50) {
          el.classList.add('animate');
        }
      });
    };
  
    // Initial check for elements in viewport
    addAnimationClass();
    
    // Check on scroll
    window.addEventListener('scroll', addAnimationClass);
  
    // Add styling for active link based on current page
    const currentPage = window.location.href;
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
      if (link.href === currentPage) {
        link.classList.add('active-link');
      }
    });
  });