/**
 * Modern JavaScript for Fingertip Plus Website
 * Simple, clean, and efficient
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initSmoothScroll();
    initContactForm();
});

/**
 * Mobile Navigation Menu
 */
function initMobileMenu() {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.querySelector('.nav-menu');
    const menuLinks = document.querySelectorAll('.nav-menu a');
    
    if (!toggle || !menu) return;
    
    // Toggle menu
    toggle.addEventListener('click', function() {
        menu.classList.toggle('active');
        
        // Animate hamburger icon
        const spans = toggle.querySelectorAll('span');
        if (menu.classList.contains('active')) {
            spans[0].style.transform = 'rotate(45deg) translateY(7px)';
            spans[1].style.opacity = '0';
            spans[2].style.transform = 'rotate(-45deg) translateY(-7px)';
        } else {
            spans[0].style.transform = '';
            spans[1].style.opacity = '';
            spans[2].style.transform = '';
        }
    });
    
    // Close menu when clicking a link
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            menu.classList.remove('active');
            const spans = toggle.querySelectorAll('span');
            spans[0].style.transform = '';
            spans[1].style.opacity = '';
            spans[2].style.transform = '';
        });
    });
}

/**
 * Smooth Scrolling for Anchor Links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Ignore empty anchors
            if (href === '#' || href === '#!') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const offset = 80; // Account for fixed nav
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Contact Form Handling
 */
function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('formMessage');
        const originalBtnText = submitBtn.textContent;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        messageDiv.className = 'form-message';
        messageDiv.textContent = '';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('contact-form-handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                messageDiv.className = 'form-message success';
                messageDiv.textContent = data.message || 'Thank you! We will get back to you shortly.';
                form.reset();
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = data.message || 'Something went wrong. Please try again.';
            }
        } catch (error) {
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'An error occurred. Please try again or contact us directly.';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });
}

/**
 * Add active class to navigation on scroll
 */
window.addEventListener('scroll', function() {
    const nav = document.querySelector('.nav');
    if (window.scrollY > 50) {
        nav.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
    } else {
        nav.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.08)';
    }
});
