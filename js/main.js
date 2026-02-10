/* ============================================================
   FINGERTIP PLUS — Main JavaScript
   ============================================================ */

(function () {
    'use strict';

    // ---- DOM Ready ----
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
    }

    /* ==========================================================
       NAVBAR
       ========================================================== */
    function initNavbar() {
        var navbar = document.getElementById('navbar');
        var hamburger = document.getElementById('hamburger');
        var navLinks = document.getElementById('navLinks');
        var links = document.querySelectorAll('.nav-link');

        // Scroll effect
        function onScroll() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Active section highlighting
            var sections = document.querySelectorAll('section[id]');
            var scrollPos = window.scrollY + 120;

            sections.forEach(function (section) {
                var top = section.offsetTop;
                var height = section.offsetHeight;
                var id = section.getAttribute('id');

                if (scrollPos >= top && scrollPos < top + height) {
                    links.forEach(function (link) {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        // Hamburger toggle
        if (hamburger && navLinks) {
            hamburger.addEventListener('click', function () {
                hamburger.classList.toggle('active');
                navLinks.classList.toggle('open');
                document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
            });

            // Close on link click
            links.forEach(function (link) {
                link.addEventListener('click', function () {
                    hamburger.classList.remove('active');
                    navLinks.classList.remove('open');
                    document.body.style.overflow = '';
                });
            });
        }
    }

    /* ==========================================================
       SCROLL ANIMATIONS
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
            {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px',
            }
        );

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    /* ==========================================================
       COUNTER ANIMATION
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

    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        var duration = 2000;
        var start = 0;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);

            // Ease out cubic
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(eased * target);

            el.textContent = current;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target;
            }
        }

        requestAnimationFrame(step);
    }

    /* ==========================================================
       TESTIMONIALS SLIDER
       ========================================================== */
    function initTestimonialsSlider() {
        var track = document.getElementById('testimonialsTrack');
        var prevBtn = document.getElementById('prevTestimonial');
        var nextBtn = document.getElementById('nextTestimonial');
        var dotsContainer = document.getElementById('testimonialsDots');

        if (!track || !prevBtn || !nextBtn || !dotsContainer) return;

        var slides = track.children;
        var dots = dotsContainer.querySelectorAll('.dot');
        var currentIndex = 0;
        var totalSlides = slides.length;
        var autoplayInterval = null;

        function goToSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;

            track.style.transform = 'translateX(-' + currentIndex * 100 + '%)';

            dots.forEach(function (dot, i) {
                dot.classList.toggle('active', i === currentIndex);
            });
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }

        nextBtn.addEventListener('click', function () {
            nextSlide();
            startAutoplay();
        });

        prevBtn.addEventListener('click', function () {
            prevSlide();
            startAutoplay();
        });

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                goToSlide(i);
                startAutoplay();
            });
        });

        // Pause on hover
        track.addEventListener('mouseenter', stopAutoplay);
        track.addEventListener('mouseleave', startAutoplay);

        // Touch swipe support
        var touchStartX = 0;
        var touchEndX = 0;

        track.addEventListener(
            'touchstart',
            function (e) {
                touchStartX = e.changedTouches[0].screenX;
                stopAutoplay();
            },
            { passive: true }
        );

        track.addEventListener(
            'touchend',
            function (e) {
                touchEndX = e.changedTouches[0].screenX;
                var diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
                startAutoplay();
            },
            { passive: true }
        );

        startAutoplay();
    }

    /* ==========================================================
       SMOOTH SCROLLING
       ========================================================== */
    function initSmoothScroll() {
        var links = document.querySelectorAll('a[href^="#"]');

        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (href === '#') return;

                var target = document.querySelector(href);
                if (!target) return;

                e.preventDefault();

                var navbarHeight = 80;
                var targetPosition = target.offsetTop - navbarHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth',
                });
            });
        });
    }

    /* ==========================================================
       CONTACT FORM
       ========================================================== */
    function initContactForm() {
        var form = document.getElementById('contactForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Basic validation
            var firstName = form.querySelector('#firstName');
            var lastName = form.querySelector('#lastName');
            var email = form.querySelector('#email');
            var message = form.querySelector('#message');
            var valid = true;

            [firstName, lastName, email, message].forEach(function (field) {
                if (!field.value.trim()) {
                    field.style.borderColor = '#E74C3C';
                    valid = false;
                } else {
                    field.style.borderColor = '';
                }
            });

            // Email format check
            if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                email.style.borderColor = '#E74C3C';
                valid = false;
            }

            if (!valid) return;

            // Show success message
            var wrapper = form.parentElement;
            wrapper.innerHTML =
                '<div class="form-success">' +
                '<svg width="64" height="64" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M8 12l2.5 2.5L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
                '<h4>Message Sent Successfully!</h4>' +
                '<p>Thank you for reaching out. Our team will get back to you within 24 hours.</p>' +
                '</div>';
        });

        // Remove error style on focus
        var fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(function (field) {
            field.addEventListener('focus', function () {
                this.style.borderColor = '';
            });
        });
    }

    /* ==========================================================
       BACK TO TOP
       ========================================================== */
    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;

        window.addEventListener(
            'scroll',
            function () {
                if (window.scrollY > 600) {
                    btn.classList.add('visible');
                } else {
                    btn.classList.remove('visible');
                }
            },
            { passive: true }
        );

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ==========================================================
       PARTICLE EFFECT (Hero Background)
       ========================================================== */
    function initParticles() {
        var container = document.getElementById('heroParticles');
        if (!container) return;

        var canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;';
        container.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        var particles = [];
        var particleCount = 50;
        var animationId;

        function resize() {
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
        }

        function createParticle() {
            return {
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 2 + 0.5,
                speedX: (Math.random() - 0.5) * 0.5,
                speedY: (Math.random() - 0.5) * 0.5,
                opacity: Math.random() * 0.4 + 0.1,
            };
        }

        function initParticleArray() {
            particles = [];
            for (var i = 0; i < particleCount; i++) {
                particles.push(createParticle());
            }
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particles.forEach(function (p) {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(0, 161, 224, ' + p.opacity + ')';
                ctx.fill();

                p.x += p.speedX;
                p.y += p.speedY;

                if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
            });

            // Draw connections
            for (var i = 0; i < particles.length; i++) {
                for (var j = i + 1; j < particles.length; j++) {
                    var dx = particles[i].x - particles[j].x;
                    var dy = particles[i].y - particles[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < 150) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(0, 161, 224, ' + (0.06 * (1 - dist / 150)) + ')';
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }

            animationId = requestAnimationFrame(drawParticles);
        }

        resize();
        initParticleArray();
        drawParticles();

        window.addEventListener('resize', function () {
            resize();
            initParticleArray();
        });

        // Pause when not visible
        var heroObserver = new IntersectionObserver(
            function (entries) {
                if (entries[0].isIntersecting) {
                    if (!animationId) drawParticles();
                } else {
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                        animationId = null;
                    }
                }
            },
            { threshold: 0 }
        );

        heroObserver.observe(container.parentElement);
    }
})();
