/**
 * Fingertip Plus - Main JavaScript
 * Handles all interactive features on the website
 */

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initAnimations();
    initCounters();
    initTestimonials();
    initContactForm();
    initBlogPreview();
    initTypingEffect();
});

// Navigation
function initNavigation() {
    const navbar = document.querySelector('.navbar');
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    // Sticky navigation on scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }
    
    // Close mobile menu when clicking nav links
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offset = 80;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

// Scroll Animations
function initAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                
                // For stagger animations
                if (entry.target.classList.contains('stagger-container')) {
                    const children = entry.target.querySelectorAll('.stagger-item');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.classList.add('visible');
                        }, index * 100);
                    });
                }
            }
        });
    }, observerOptions);
    
    // Observe all animated elements
    document.querySelectorAll('.fade-in, .slide-up, .slide-left, .slide-right, .scale-in').forEach(el => {
        observer.observe(el);
    });
    
    document.querySelectorAll('.stagger-container').forEach(el => {
        observer.observe(el);
    });
}

// Animated Counters
function initCounters() {
    const counters = document.querySelectorAll('.counter');
    const speed = 200;
    
    const observerOptions = {
        threshold: 0.5
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');
                const increment = target / speed;
                
                const updateCounter = () => {
                    const current = +counter.innerText.replace(/[^0-9]/g, '');
                    if (current < target) {
                        counter.innerText = Math.ceil(current + increment) + '+';
                        setTimeout(updateCounter, 10);
                    } else {
                        counter.innerText = target + '+';
                    }
                };
                
                updateCounter();
                observer.unobserve(counter);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => observer.observe(counter));
}

// Testimonials Carousel
function initTestimonials() {
    const carousel = document.querySelector('.testimonials-carousel');
    if (!carousel) return;
    
    const items = carousel.querySelectorAll('.testimonial-item');
    const dots = carousel.querySelectorAll('.carousel-dot');
    let currentIndex = 0;
    let autoplayInterval;
    
    function showTestimonial(index) {
        items.forEach(item => item.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        items[index].classList.add('active');
        dots[index].classList.add('active');
        currentIndex = index;
    }
    
    function nextTestimonial() {
        const next = (currentIndex + 1) % items.length;
        showTestimonial(next);
    }
    
    function prevTestimonial() {
        const prev = (currentIndex - 1 + items.length) % items.length;
        showTestimonial(prev);
    }
    
    function startAutoplay() {
        autoplayInterval = setInterval(nextTestimonial, 5000);
    }
    
    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }
    
    // Navigation buttons
    const prevBtn = carousel.querySelector('.carousel-prev');
    const nextBtn = carousel.querySelector('.carousel-next');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevTestimonial();
            stopAutoplay();
            startAutoplay();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextTestimonial();
            stopAutoplay();
            startAutoplay();
        });
    }
    
    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showTestimonial(index);
            stopAutoplay();
            startAutoplay();
        });
    });
    
    // Start autoplay
    startAutoplay();
    
    // Pause on hover
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
}

// Contact Form
function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        const messageDiv = document.getElementById('formMessage');
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        messageDiv.className = 'form-message';
        messageDiv.textContent = '';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('/contact-handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                messageDiv.className = 'form-message success';
                messageDiv.textContent = data.message;
                form.reset();
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = data.message;
            }
        } catch (error) {
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'An error occurred. Please try again or contact us directly.';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

// Blog Preview - Load latest posts
function initBlogPreview() {
    const blogGrid = document.querySelector('.blog-preview-grid');
    if (!blogGrid) return;
    
    fetch('/blog-api.php?limit=3')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.posts.length > 0) {
                blogGrid.innerHTML = '';
                data.posts.forEach(post => {
                    const card = createBlogCard(post);
                    blogGrid.appendChild(card);
                });
            }
        })
        .catch(error => {
            console.error('Error loading blog posts:', error);
            // Fallback content is already in HTML
        });
}

function createBlogCard(post) {
    const article = document.createElement('article');
    article.className = 'blog-card fade-in';
    
    const imageUrl = post.featured_image || 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800';
    
    article.innerHTML = `
        <a href="/blog/${post.slug}">
            <div class="blog-card-image" style="background-image: url('${imageUrl}')"></div>
            <div class="blog-card-content">
                <div class="blog-meta">
                    <span class="blog-date">${post.created_at}</span>
                </div>
                <h3>${post.title}</h3>
                <p>${post.excerpt}</p>
                <span class="read-more">Read More →</span>
            </div>
        </a>
    `;
    
    return article;
}

// Typing Effect for Hero
function initTypingEffect() {
    const typingElement = document.querySelector('.typing-text');
    if (!typingElement) return;
    
    const texts = [
        'Transform Your Business with Salesforce Excellence',
        'Innovate with Custom CRM Solutions',
        'Scale Your Operations with Cloud Technology',
        'Empower Your Team with Automation'
    ];
    
    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typingSpeed = 100;
    
    function type() {
        const currentText = texts[textIndex];
        
        if (isDeleting) {
            typingElement.textContent = currentText.substring(0, charIndex - 1);
            charIndex--;
            typingSpeed = 50;
        } else {
            typingElement.textContent = currentText.substring(0, charIndex + 1);
            charIndex++;
            typingSpeed = 100;
        }
        
        if (!isDeleting && charIndex === currentText.length) {
            // Pause at end
            typingSpeed = 2000;
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
            typingSpeed = 500;
        }
        
        setTimeout(type, typingSpeed);
    }
    
    // Start typing effect
    setTimeout(type, 1000);
}

// Utility function for form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Utility function for throttling scroll events
function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
