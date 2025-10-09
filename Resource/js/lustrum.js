// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const dropdowns = document.querySelectorAll('.dropdown');
  
    // Toggle mobile menu
    mobileMenuBtn.addEventListener('click', function() {
      mainNav.classList.toggle('active');
      mobileMenuOverlay.classList.toggle('active');
      
      // Change menu icon
      const icon = mobileMenuBtn.querySelector('i');
      if (mainNav.classList.contains('active')) {
        icon.className = 'fas fa-times';
      } else {
        icon.className = 'fas fa-bars';
      }
    });
  
    // Close menu when clicking outside
    mobileMenuOverlay.addEventListener('click', function() {
      mainNav.classList.remove('active');
      mobileMenuOverlay.classList.remove('active');
      mobileMenuBtn.querySelector('i').className = 'fas fa-bars';
    });
  
    // Handle dropdown clicks on mobile
    dropdowns.forEach(dropdown => {
      const link = dropdown.querySelector('a');
      link.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          dropdown.classList.toggle('active');
        }
      });
    });
  
    // Milestone Slider
    const milestonesSlider = document.getElementById('milestonesSlider');
    if (milestonesSlider) {
      const milestoneSlides = milestonesSlider.querySelectorAll('.milestone-slide');
      const milestoneDots = document.getElementById('milestoneDots');
      const prevBtn = document.getElementById('prevMilestone');
      const nextBtn = document.getElementById('nextMilestone');
      
      let currentSlide = 0;
      
      // Show initial slide
      showSlide(currentSlide, milestoneSlides);
      
      // Previous button
      prevBtn.addEventListener('click', function() {
        currentSlide = (currentSlide - 1 + milestoneSlides.length) % milestoneSlides.length;
        showSlide(currentSlide, milestoneSlides);
      });
      
      // Next button
      nextBtn.addEventListener('click', function() {
        currentSlide = (currentSlide + 1) % milestoneSlides.length;
        showSlide(currentSlide, milestoneSlides);
      });
      
      // Dot navigation
      const dots = milestoneDots.querySelectorAll('.dot');
      dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
          currentSlide = index;
          showSlide(currentSlide, milestoneSlides);
        });
      });
      
      // Auto sliding
      setInterval(function() {
        currentSlide = (currentSlide + 1) % milestoneSlides.length;
        showSlide(currentSlide, milestoneSlides);
      }, 5000);
    }
  
    // Testimonial Slider
    const testimonialSlider = document.getElementById('testimonialSlider');
    if (testimonialSlider) {
      const testimonialSlides = testimonialSlider.querySelectorAll('.testimonial-slide');
      const testimonialDots = document.getElementById('testimonialDots');
      const prevBtn = document.getElementById('prevTestimonial');
      const nextBtn = document.getElementById('nextTestimonial');
      
      let currentSlide = 0;
      
      // Show initial slide
      showSlide(currentSlide, testimonialSlides);
      
      // Previous button
      prevBtn.addEventListener('click', function() {
        currentSlide = (currentSlide - 1 + testimonialSlides.length) % testimonialSlides.length;
        showSlide(currentSlide, testimonialSlides);
      });
      
      // Next button
      nextBtn.addEventListener('click', function() {
        currentSlide = (currentSlide + 1) % testimonialSlides.length;
        showSlide(currentSlide, testimonialSlides);
      });
      
      // Dot navigation
      const dots = testimonialDots.querySelectorAll('.dot');
      dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
          currentSlide = index;
          showSlide(currentSlide, testimonialSlides);
        });
      });
      
      // Auto sliding
      setInterval(function() {
        currentSlide = (currentSlide + 1) % testimonialSlides.length;
        showSlide(currentSlide, testimonialSlides);
      }, 7000);
    }
  
    // Function to show slide
    function showSlide(n, slides) {
      // Hide all slides
      slides.forEach(slide => {
        slide.style.display = 'none';
        slide.classList.remove('active');
      });
      
      // Show the current slide
      slides[n].style.display = 'block';
      slides[n].classList.add('active');
      
      // Update dots
      const slider = slides[0].closest('.milestones-slider, .testimonial-slider');
      const dotsContainer = slider.id === 'milestonesSlider' ? document.getElementById('milestoneDots') : document.getElementById('testimonialDots');
      const dots = dotsContainer.querySelectorAll('.dot');
      
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === n);
      });
    }
  
    // Countdown Timer
    const countdownElement = document.getElementById('lustrumCountdown');
    if (countdownElement) {
      const targetDate = new Date('September 20, 2029 00:00:00').getTime();
      
      // Update countdown every 1 second
      const countdownInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetDate - now;
        
        // Calculate days, hours, minutes, seconds
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Update countdown
        document.querySelector('.days').textContent = days;
        document.querySelector('.hours').textContent = hours;
        document.querySelector('.minutes').textContent = minutes;
        document.querySelector('.seconds').textContent = seconds;
        
        // If countdown is over
        if (distance < 0) {
          clearInterval(countdownInterval);
          countdownElement.innerHTML = '<h3>Lustrum II has arrived!</h3>';
        }
      }, 1000);
    }
  
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: 'smooth'
          });
        }
      });
    });
  
    // Initialize AOS (Animate On Scroll) if available
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
      });
    }
  
    // Gallery lightbox (if needed)
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
      item.addEventListener('click', function() {
        const imgSrc = this.querySelector('img').src;
        const caption = this.querySelector('.gallery-caption').textContent;
        
        // Create lightbox elements (can be implemented if needed)
        // This is a placeholder for a more complex lightbox implementation
        console.log('Lightbox would show: ' + imgSrc + ' - ' + caption);
      });
    });
  });