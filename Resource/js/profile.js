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
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
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

    // ========== Video profile with lazy loading ==========
    const video = document.getElementById('profileVideo');
    const playBtn = document.getElementById('videoPlayBtn');
    const videoCard = document.getElementById('videoCard');
    const loader = document.getElementById('videoLoader');

    function setLoadingState(isLoading) {
        if (!videoCard) return;
        if (isLoading) {
            videoCard.classList.add('loading');
            if (loader) loader.style.display = 'flex';
        } else {
            videoCard.classList.remove('loading');
            if (loader) loader.style.display = 'none';
        }
    }

    function loadVideoSource() {
        if (!video) return Promise.resolve();
        const srcEl = video.querySelector('source[data-src]');
        if (!srcEl) return Promise.resolve();

        // If already loaded (src present), resolve
        if (srcEl.getAttribute('src')) return Promise.resolve();

        setLoadingState(true);
        srcEl.setAttribute('src', srcEl.dataset.src);
        // Trigger load
        video.load();

        return new Promise((resolve, reject) => {
            // Use loadeddata / canplaythrough as a good sign video is ready
            const onLoaded = () => {
                cleanup();
                resolve();
            };
            const onError = (e) => {
                cleanup();
                reject(e);
            };
            const cleanup = () => {
                video.removeEventListener('loadeddata', onLoaded);
                video.removeEventListener('canplaythrough', onLoaded);
                video.removeEventListener('error', onError);
                setLoadingState(false);
            };
            video.addEventListener('loadeddata', onLoaded);
            video.addEventListener('canplaythrough', onLoaded);
            video.addEventListener('error', onError);
        });
    }

    // IntersectionObserver for lazy loading
    if (video) {
        const srcEl = video.querySelector('source[data-src]');
        const loadIfNeeded = () => {
            if (!srcEl) return;
            if (srcEl.getAttribute('src')) return; // already set
            loadVideoSource().catch(err => {
                console.warn('Gagal memuat video:', err);
                setLoadingState(false);
            });
        };

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadIfNeeded();
                        observer.disconnect();
                    }
                });
            }, { rootMargin: '200px' });
            io.observe(video);
        } else {
            // Fallback: load immediately
            loadIfNeeded();
        }
    }

    // Play button handler (ensures lazy load happens first)
    if (playBtn && video) {
        const ensureLoadedThenPlay = () => {
            const srcEl = video.querySelector('source[data-src]');
            const alreadyLoaded = srcEl && srcEl.getAttribute('src');
            if (alreadyLoaded) {
                video.setAttribute('controls', 'controls');
                video.play().catch(err => console.warn('Play prevented:', err));
                return;
            }
            // Load then play
            loadVideoSource().then(() => {
                video.setAttribute('controls', 'controls');
                video.play().catch(err => console.warn('Play prevented after load:', err));
            }).catch(err => {
                console.warn('Error loading video before play:', err);
            });
        };

        playBtn.addEventListener('click', (e) => {
            e.preventDefault();
            ensureLoadedThenPlay();
        });

        // Click on video toggles play/pause (if loaded)
        video.addEventListener('click', () => {
            // if not loaded yet, load and play
            const srcEl = video.querySelector('source[data-src]');
            const alreadyLoaded = srcEl && srcEl.getAttribute('src');
            if (!alreadyLoaded) {
                ensureLoadedThenPlay();
                return;
            }

            if (video.paused) {
                video.play().catch(err => console.warn('Play prevented:', err));
            } else {
                video.pause();
            }
        });

        // Update overlay when play/pause/ended
        const updatePlayButton = () => {
            if (video.paused || video.ended) {
                playBtn.style.display = 'flex';
            } else {
                playBtn.style.display = 'none';
            }
        };

        video.addEventListener('play', updatePlayButton);
        video.addEventListener('pause', updatePlayButton);
        video.addEventListener('ended', () => {
            video.currentTime = 0;
            video.removeAttribute('controls');
            updatePlayButton();
        });

        // When metadata becomes available, hide loader
        video.addEventListener('loadeddata', () => setLoadingState(false));
        video.addEventListener('canplaythrough', () => setLoadingState(false));
        video.addEventListener('error', () => {
            setLoadingState(false);
            console.warn('Video error occurred');
        });

        // Keyboard accessibility: spacebar toggles play/pause when video focused
        video.addEventListener('keydown', (e) => {
            if (e.code === 'Space' || e.key === ' ') {
                e.preventDefault();
                if (video.paused) video.play(); else video.pause();
            }
        });

        // Initialize play button visibility (if already loaded or paused)
        updatePlayButton();
    }
    // ========== end video lazy loading ==========
});