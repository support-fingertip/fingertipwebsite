/* ============================================================
   FINGERTIP - Main JavaScript
   Corporate Website Interactive Functionality
   ============================================================ */

(function () {
    'use strict';

    /* ---- DOM Ready ---- */
    document.addEventListener('DOMContentLoaded', init);

    function init() {
        initNavbar();
        initScrollAnimations();
        initCounters();
        initTestimonialsSlider();
        initSmoothScroll();
        initContactForm();
        initBackToTop();
        initParticles();
        initTypingEffect();
        initVideoLazyLoad();

        // Add js-ready class after a small delay to prevent FOUC
        // Elements with [data-animate] are only hidden after this class is added
        requestAnimationFrame(function () {
            document.body.classList.add('js-ready');
        });
    }

    /* ==========================================================
       1. NAVIGATION
       - Scroll detection for .navbar (.scrolled at scrollY > 50)
       - Active page highlighting from current URL
       - Hamburger menu toggle
       - Mega-menu dropdown handling (desktop touch + mobile)
       - Close dropdowns on outside click
       - Prevent body scroll when mobile nav is open
       ========================================================== */
    function initNavbar() {
        var navbar = document.querySelector('.navbar');
        var hamburger = document.querySelector('.hamburger');
        var navLinks = document.querySelector('.nav-links');
        var dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        var hasDropdowns = document.querySelectorAll('.has-dropdown');

        if (!navbar) return;

        /* -- Scroll detection: add .scrolled when scrollY > 50 -- */
        function handleScroll() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll(); // run once on load

        /* -- Active page highlighting from current URL -- */
        var currentPath = window.location.pathname.split('/').pop() || 'index.html';
        var navAnchors = document.querySelectorAll('.nav-links a');

        navAnchors.forEach(function (anchor) {
            var href = anchor.getAttribute('href');
            if (href) {
                var linkPage = href.split('/').pop().split('#')[0] || 'index.html';
                if (linkPage === currentPath) {
                    anchor.classList.add('active');
                }
            }
        });

        /* -- Hamburger toggle -- */
        if (hamburger && navLinks) {
            hamburger.addEventListener('click', function () {
                hamburger.classList.toggle('active');
                navLinks.classList.toggle('open');

                // Prevent body scroll when mobile nav is open
                if (navLinks.classList.contains('open')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            // Close mobile nav when clicking a nav link
            var allNavLinks = navLinks.querySelectorAll('a');
            allNavLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 992) {
                        hamburger.classList.remove('active');
                        navLinks.classList.remove('open');
                        document.body.style.overflow = '';
                    }
                });
            });
        }

        /* -- Mega-menu dropdowns -- */
        dropdownToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                var parentDropdown = toggle.closest('.has-dropdown');
                var isMobile = window.innerWidth <= 992;

                if (isMobile) {
                    // On mobile: toggle .open on parent .has-dropdown
                    e.preventDefault();
                    e.stopPropagation();

                    // Close other open dropdowns on mobile
                    hasDropdowns.forEach(function (dd) {
                        if (dd !== parentDropdown) {
                            dd.classList.remove('open');
                        }
                    });

                    parentDropdown.classList.toggle('open');
                } else {
                    // On desktop (>992px): handle click for touch devices
                    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Close other open dropdowns
                        hasDropdowns.forEach(function (dd) {
                            if (dd !== parentDropdown) {
                                dd.classList.remove('open');
                            }
                        });

                        parentDropdown.classList.toggle('open');
                    }
                }
            });
        });

        /* -- Close dropdowns when clicking outside -- */
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.has-dropdown')) {
                hasDropdowns.forEach(function (dd) {
                    dd.classList.remove('open');
                });
            }
        });
    }

    /* ==========================================================
       2. SCROLL ANIMATIONS
       - IntersectionObserver on all [data-animate] elements
       - Adds .animated class with optional data-delay
       - Unobserves after animating
       ========================================================== */
    function initScrollAnimations() {
        var elements = document.querySelectorAll('[data-animate]');

        if (!elements.length) return;

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var delay = parseInt(entry.target.getAttribute('data-delay') || '0', 10);

                        setTimeout(function () {
                            entry.target.classList.add('animated');
                        }, delay);

                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15 }
        );

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    /* ==========================================================
       3. COUNTER ANIMATION
       - Watches .stat-number[data-count] elements
       - Animates from 0 to target over 2 seconds
       - Ease-out cubic easing function
       ========================================================== */
    function initCounters() {
        var counters = document.querySelectorAll('.stat-number[data-count]');

        if (!counters.length) return;

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.5 }
        );

        counters.forEach(function (counter) {
            observer.observe(counter);
        });
    }

    /**
     * Animates a single counter element from 0 to its data-count value.
     * Uses ease-out cubic: 1 - (1 - t)^3
     */
    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        var duration = 2000;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;

            var elapsed = timestamp - startTime;
            var progress = Math.min(elapsed / duration, 1);

            // Ease-out cubic
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);

            el.textContent = current;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Ensure final value is exact
                el.textContent = target;
            }
        }

        requestAnimationFrame(step);
    }

    /* ==========================================================
       4. TESTIMONIALS SLIDER
       - Track: #testimonialsTrack with flex children
       - Prev/Next buttons, dot indicators
       - Auto-play every 5 seconds
       - Pause on hover, resume on mouse leave
       - Touch swipe support
       - Infinite looping
       ========================================================== */
    function initTestimonialsSlider() {
        var track = document.getElementById('testimonialsTrack');
        var prevBtn = document.getElementById('prevTestimonial');
        var nextBtn = document.getElementById('nextTestimonial');
        var dotsContainer = document.getElementById('testimonialsDots');

        if (!track) return;

        var slides = track.children;
        var totalSlides = slides.length;

        if (totalSlides === 0) return;

        var dots = dotsContainer ? dotsContainer.querySelectorAll('.dot') : [];
        var currentIndex = 0;
        var autoplayTimer = null;
        var touchStartX = 0;
        var touchEndX = 0;

        /**
         * Move slider to the specified index.
         * Wraps around for infinite looping.
         */
        function goToSlide(index) {
            // Infinite loop wrapping
            if (index < 0) {
                index = totalSlides - 1;
            } else if (index >= totalSlides) {
                index = 0;
            }

            currentIndex = index;
            track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';

            // Update dot indicators
            if (dots.length) {
                dots.forEach(function (dot, i) {
                    dot.classList.toggle('active', i === currentIndex);
                });
            }
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        /* -- Auto-play every 5 seconds -- */
        function startAutoplay() {
            stopAutoplay();
            autoplayTimer = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        /* -- Button click handlers -- */
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                nextSlide();
                startAutoplay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                prevSlide();
                startAutoplay();
            });
        }

        /* -- Dot click handlers -- */
        if (dots.length) {
            dots.forEach(function (dot, i) {
                dot.addEventListener('click', function () {
                    goToSlide(i);
                    startAutoplay();
                });
            });
        }

        /* -- Pause on hover, resume on mouse leave -- */
        track.addEventListener('mouseenter', stopAutoplay);
        track.addEventListener('mouseleave', startAutoplay);

        /* -- Touch swipe support -- */
        track.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
            stopAutoplay();
        }, { passive: true });

        track.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            var swipeDistance = touchStartX - touchEndX;

            if (Math.abs(swipeDistance) > 50) {
                if (swipeDistance > 0) {
                    nextSlide(); // swiped left - go next
                } else {
                    prevSlide(); // swiped right - go prev
                }
            }

            startAutoplay();
        }, { passive: true });

        // Initialize first slide and start autoplay
        goToSlide(0);
        startAutoplay();
    }

    /* ==========================================================
       5. SMOOTH SCROLLING
       - All anchor links a[href^="#"] smooth scroll
       - 80px navbar offset
       - Only works if target element exists on page
       ========================================================== */
    function initSmoothScroll() {
        var anchors = document.querySelectorAll('a[href^="#"]');

        anchors.forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var href = this.getAttribute('href');

                // Skip bare hash links
                if (href === '#' || href.length < 2) return;

                var targetEl = document.querySelector(href);

                // Only scroll if target exists on this page
                if (!targetEl) return;

                e.preventDefault();

                var navbarOffset = 80;
                var targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset - navbarOffset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            });
        });
    }

    /* ==========================================================
       6. CONTACT FORM
       - Validates required fields: firstName, lastName, email, message
       - Email regex validation
       - Red border on invalid, cleared on focus
       - Replaces form with success message on valid submit
       ========================================================== */
    function initContactForm() {
        var form = document.getElementById('contactForm');

        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var firstName = form.querySelector('#firstName');
            var lastName = form.querySelector('#lastName');
            var email = form.querySelector('#email');
            var message = form.querySelector('#message');
            var isValid = true;

            // Validate all required fields are non-empty
            var requiredFields = [firstName, lastName, email, message];

            requiredFields.forEach(function (field) {
                if (!field || !field.value.trim()) {
                    if (field) field.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });

            // Email format validation
            if (email && email.value.trim()) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value.trim())) {
                    email.style.borderColor = '#e74c3c';
                    isValid = false;
                }
            }

            if (!isValid) return;

            // On success: replace form with success message HTML
            var formParent = form.parentElement;
            formParent.innerHTML =
                '<div class="form-success">' +
                    '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
                        '<circle cx="12" cy="12" r="10"></circle>' +
                        '<path d="M8 12l2.5 2.5L16 9" stroke-linecap="round" stroke-linejoin="round"></path>' +
                    '</svg>' +
                    '<h3>Message Sent Successfully!</h3>' +
                    '<p>Thank you for reaching out. Our team will get back to you within 24 hours.</p>' +
                '</div>';
        });

        // Clear red border on focus for all form inputs
        var formFields = form.querySelectorAll('input, textarea, select');
        formFields.forEach(function (field) {
            field.addEventListener('focus', function () {
                this.style.borderColor = '';
            });
        });
    }

    /* ==========================================================
       7. BACK TO TOP
       - Button #backToTop
       - Shows (.visible class) when scrollY > 600
       - Click scrolls to top smoothly
       ========================================================== */
    function initBackToTop() {
        var btn = document.getElementById('backToTop');

        if (!btn) return;

        window.addEventListener('scroll', function () {
            if (window.scrollY > 600) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /* ==========================================================
       8. PARTICLE EFFECT (Hero Section)
       - Canvas-based particle system inside #heroParticles
       - 50 particles with random position, size, speed, opacity
       - Blue color: rgba(26, 122, 248, opacity)
       - Connection lines between particles within 150px
       - requestAnimationFrame loop
       - Resize handler
       - Pauses when hero is not visible (IntersectionObserver)
       ========================================================== */
    function initParticles() {
        var container = document.getElementById('heroParticles');

        if (!container) return;

        // Create canvas element filling the container
        var canvas = document.createElement('canvas');
        canvas.style.position = 'absolute';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.pointerEvents = 'none';
        container.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        var particles = [];
        var particleCount = 50;
        var animationId = null;
        var isVisible = true;

        /** Set canvas dimensions to match container */
        function resizeCanvas() {
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
        }

        /** Create a single particle with random properties */
        function createParticle() {
            return {
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 2 + 0.5,       // 0.5 to 2.5px
                speedX: (Math.random() - 0.5) * 0.8,
                speedY: (Math.random() - 0.5) * 0.8,
                opacity: Math.random() * 0.5 + 0.2
            };
        }

        /** Populate the particles array */
        function createParticles() {
            particles = [];
            for (var i = 0; i < particleCount; i++) {
                particles.push(createParticle());
            }
        }

        /** Main animation loop */
        function animate() {
            if (!isVisible) return;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Update and draw each particle
            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];

                // Move particle
                p.x += p.speedX;
                p.y += p.speedY;

                // Bounce off edges
                if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;

                // Keep within bounds
                p.x = Math.max(0, Math.min(p.x, canvas.width));
                p.y = Math.max(0, Math.min(p.y, canvas.height));

                // Draw particle
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(26, 122, 248, ' + p.opacity + ')';
                ctx.fill();
            }

            // Draw connections between particles within 150px distance
            for (var i = 0; i < particles.length; i++) {
                for (var j = i + 1; j < particles.length; j++) {
                    var dx = particles[i].x - particles[j].x;
                    var dy = particles[i].y - particles[j].y;
                    var distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 150) {
                        var lineOpacity = 0.12 * (1 - distance / 150);
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(26, 122, 248, ' + lineOpacity + ')';
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }

            animationId = requestAnimationFrame(animate);
        }

        /** Start the animation */
        function startAnimation() {
            if (animationId) return;
            isVisible = true;
            animate();
        }

        /** Stop the animation */
        function stopAnimation() {
            isVisible = false;
            if (animationId) {
                cancelAnimationFrame(animationId);
                animationId = null;
            }
        }

        // Initialize
        resizeCanvas();
        createParticles();
        startAnimation();

        // Resize handler
        window.addEventListener('resize', function () {
            resizeCanvas();
            createParticles();
        });

        // Pause animation when hero is not visible (performance optimization)
        var heroObserver = new IntersectionObserver(
            function (entries) {
                if (entries[0].isIntersecting) {
                    startAnimation();
                } else {
                    stopAnimation();
                }
            },
            { threshold: 0 }
        );

        heroObserver.observe(container);
    }

    /* ==========================================================
       9. TYPING EFFECT
       - Element: .typing-text with data-words (JSON array)
       - Types each word character by character
       - Pauses, then deletes character by character
       - Loops through all words continuously
       - Blinking cursor via CSS class
       ========================================================== */
    function initTypingEffect() {
        var typingEl = document.querySelector('.typing-text');

        if (!typingEl) return;

        var wordsAttr = typingEl.getAttribute('data-words');

        if (!wordsAttr) return;

        var words;
        try {
            words = JSON.parse(wordsAttr);
        } catch (e) {
            return;
        }

        if (!words.length) return;

        var wordIndex = 0;
        var charIndex = 0;
        var isDeleting = false;
        var typeSpeed = 100;
        var deleteSpeed = 60;
        var pauseAfterType = 2000;
        var pauseAfterDelete = 500;

        // Add blinking cursor class
        typingEl.classList.add('typing-cursor');

        function type() {
            var currentWord = words[wordIndex];

            if (isDeleting) {
                // Remove characters
                charIndex--;
                typingEl.textContent = currentWord.substring(0, charIndex);

                if (charIndex === 0) {
                    // Finished deleting, move to next word
                    isDeleting = false;
                    wordIndex = (wordIndex + 1) % words.length;
                    setTimeout(type, pauseAfterDelete);
                    return;
                }

                setTimeout(type, deleteSpeed);
            } else {
                // Add characters
                charIndex++;
                typingEl.textContent = currentWord.substring(0, charIndex);

                if (charIndex === currentWord.length) {
                    // Finished typing, pause then start deleting
                    isDeleting = true;
                    setTimeout(type, pauseAfterType);
                    return;
                }

                setTimeout(type, typeSpeed);
            }
        }

        // Start the typing loop
        setTimeout(type, pauseAfterDelete);
    }

    /* ==========================================================
       10. VIDEO LAZY LOADING
       - YouTube iframes with data-src load actual src on scroll
       - Uses IntersectionObserver for efficient detection
       ========================================================== */
    function initVideoLazyLoad() {
        var lazyVideos = document.querySelectorAll('iframe[data-src]');

        if (!lazyVideos.length) return;

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var iframe = entry.target;
                        var src = iframe.getAttribute('data-src');

                        if (src) {
                            iframe.setAttribute('src', src);
                            iframe.removeAttribute('data-src');
                        }

                        observer.unobserve(iframe);
                    }
                });
            },
            {
                rootMargin: '200px 0px',
                threshold: 0
            }
        );

        lazyVideos.forEach(function (video) {
            observer.observe(video);
        });
    }

})();
