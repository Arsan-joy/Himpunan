// Materials Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeMaterials();
});

// Filter functionality
function initializeFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const materialCards = document.querySelectorAll('.material-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active filter button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter cards
            materialCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.classList.remove('hidden');
                    // Add animation
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
}

// Initialize materials functionality
function initializeMaterials() {
    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

// PDF viewing functionality
function viewPDF(pdfPath) {
    const modal = document.getElementById('pdfModal');
    const viewer = document.getElementById('pdfViewer');
    const title = document.getElementById('pdfTitle');
    
    // Extract filename for title
    const filename = pdfPath.split('/').pop().replace('.pdf', '');
    title.textContent = filename.replace(/-/g, ' ').toUpperCase();
    
    // Set PDF source
    viewer.src = pdfPath;
    
    // Show modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Add animation
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
}

// Close PDF modal
function closePDFModal() {
    const modal = document.getElementById('pdfModal');
    const viewer = document.getElementById('pdfViewer');
    
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        viewer.src = '';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Download PDF functionality
function downloadPDF(pdfPath, filename) {
    // Show loading indicator
    const btn = event.target.closest('.btn-download');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
    btn.disabled = true;
    
    // Create download link
    const link = document.createElement('a');
    link.href = pdfPath;
    link.download = filename;
    link.style.display = 'none';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button after download
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('pdfModal');
    if (event.target === modal) {
        closePDFModal();
    }
});

// Handle escape key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePDFModal();
    }
});

// Search functionality (optional enhancement)
function searchMaterials(searchTerm) {
    const cards = document.querySelectorAll('.material-card');
    const lowerSearchTerm = searchTerm.toLowerCase();
    
    cards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('.card-description').textContent.toLowerCase();
        
        if (title.includes(lowerSearchTerm) || description.includes(lowerSearchTerm)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

// Lazy loading for PDFs (performance optimization)
function lazyLoadPDFs() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                // Pre-load PDF metadata or thumbnails here if needed
                observer.unobserve(card);
            }
        });
    });
    
    document.querySelectorAll('.material-card').forEach(card => {
        observer.observe(card);
    });
}

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', lazyLoadPDFs);