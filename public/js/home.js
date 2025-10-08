/**
 * VogueVault Customer Dashboard JavaScript
 * Handles interactivity for the homepage
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeCarousel();
    initializeSearch();
    initializeTooltips();
    initializeSmoothScrolling();
});

/**
 * Initialize product carousel functionality
 */
function initializeCarousel() {
    const nextBtn = document.getElementById('productsNext');
    const prevBtn = document.getElementById('productsPrev');
    const scrollArea = document.querySelector('.products-scroll-area');
    
    if (!scrollArea) return;
    
    // Handle next button click
    const scrollByAmount = () => {
        const visibleWidth = scrollArea.clientWidth;
        return visibleWidth > 0 ? Math.max(visibleWidth * 0.8, 220) : 220;
    };

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            scrollArea.scrollBy({
                left: scrollByAmount(),
                behavior: 'smooth'
            });
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            scrollArea.scrollBy({
                left: -scrollByAmount(),
                behavior: 'smooth'
            });
        });
    }
    
    // Auto-hide/show navigation button based on scroll position
    function updateNavigation() {
        const maxScroll = scrollArea.scrollWidth - scrollArea.clientWidth;
        const currentScroll = scrollArea.scrollLeft;

        const controlsNeeded = maxScroll > 8;

        [nextBtn, prevBtn].forEach(btn => {
            if (!btn) return;
            btn.style.display = controlsNeeded ? 'flex' : 'none';
        });

        if (prevBtn) {
            prevBtn.disabled = currentScroll <= 8;
        }

        if (nextBtn) {
            nextBtn.disabled = currentScroll >= maxScroll - 8;
        }
    }
    
    // Listen for scroll events
    scrollArea.addEventListener('scroll', updateNavigation);
    window.addEventListener('resize', updateNavigation);
    
    // Initial check
    updateNavigation();
    
    // Touch/swipe support for mobile
    let startX = 0;
    let startY = 0;
    
    scrollArea.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    }, { passive: true });
    
    scrollArea.addEventListener('touchmove', function(e) {
        if (!startX || !startY) return;
        
        const xDiff = startX - e.touches[0].clientX;
        const yDiff = startY - e.touches[0].clientY;
        
        // Only prevent default if horizontal swipe is dominant
        if (Math.abs(xDiff) > Math.abs(yDiff)) {
            e.preventDefault();
        }
    }, { passive: false });
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');
    
    if (!searchForm || !searchInput) return;
    
    // Handle suggestion button clicks
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const query = this.getAttribute('data-query') || this.textContent.trim();
            searchInput.value = query;
            
            // Optional: Auto-submit the form
            searchForm.submit();
        });
    });
    
    // Handle form submission
    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        
        if (!query) {
            e.preventDefault();
            searchInput.focus();
            showSearchMessage('Please enter a search term');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('.search-submit-btn');
        const originalContent = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
        submitBtn.disabled = true;
        
        // Reset button after a delay if form doesn't submit properly
        setTimeout(() => {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }, 3000);
    });
    
    // Search input enhancements
    searchInput.addEventListener('focus', function() {
        this.parentElement.style.boxShadow = '0 4px 20px rgba(129, 154, 145, 0.3)';
    });
    
    searchInput.addEventListener('blur', function() {
        this.parentElement.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
    });
    
    // Enter key support
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.submit();
        }
    });
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    // Initialize tooltips for navigation icons
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            placement: 'bottom',
            trigger: 'hover'
        });
    });
}

/**
 * Initialize smooth scrolling for internal links
 */
function initializeSmoothScrolling() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Show search message (utility function)
 */
function showSearchMessage(message) {
    // Create or update message element
    let messageEl = document.querySelector('.search-message');
    
    if (!messageEl) {
        messageEl = document.createElement('div');
        messageEl.className = 'search-message alert alert-info mt-2';
        messageEl.style.display = 'none';
        
        const searchSection = document.querySelector('.search-section');
        if (searchSection) {
            searchSection.appendChild(messageEl);
        }
    }
    
    messageEl.textContent = message;
    messageEl.style.display = 'block';
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        messageEl.style.display = 'none';
    }, 3000);
}

/**
 * Product card hover effects
 */
function initializeProductEffects() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

/**
 * Category card hover effects
 */
function initializeCategoryEffects() {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const overlay = this.querySelector('.category-overlay');
            if (overlay) {
                overlay.style.background = 'linear-gradient(transparent, rgba(129, 154, 145, 0.8))';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const overlay = this.querySelector('.category-overlay');
            if (overlay) {
                overlay.style.background = 'linear-gradient(transparent, rgba(0, 0, 0, 0.7))';
            }
        });
    });
}

/**
 * Initialize intersection observer for animations
 */
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe sections for fade-in animation
    document.querySelectorAll('.products-section, .categories-section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
}

/**
 * Handle responsive navigation
 */
function initializeResponsiveNav() {
    const navIcons = document.querySelectorAll('.nav-icon');
    
    function updateNavDisplay() {
        const isMobile = window.innerWidth < 768;
        
        navIcons.forEach(icon => {
            const textEl = icon.querySelector('.nav-text');
            if (textEl) {
                textEl.style.display = isMobile ? 'none' : 'block';
            }
        });
    }
    
    // Initial check
    updateNavDisplay();
    
    // Listen for resize events
    window.addEventListener('resize', updateNavDisplay);
}

// Initialize additional effects when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeProductEffects();
    initializeCategoryEffects();
    initializeAnimations();
    initializeResponsiveNav();
});

// Handle search input focus states
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.style.paddingRight = '60px';
            } else {
                this.style.paddingRight = '60px';
            }
        });
    }
});
