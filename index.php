<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fingertip Plus - Your trusted Salesforce partner delivering innovative CRM solutions, custom development, and digital transformation services across 12+ industries.">
    <meta name="keywords" content="Salesforce, CRM, Salesforce Consulting, Salesforce Implementation, Custom Development, Digital Transformation">
    <meta name="author" content="Fingertip Plus">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="Fingertip Plus - Salesforce & Digital Solutions">
    <meta property="og:description" content="Transform your business with expert Salesforce consulting, custom development, and comprehensive digital solutions.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://fingertipplus.com">
    <meta property="og:image" content="https://fingertipplus.com/images/og-image.jpg">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Fingertip Plus - Salesforce & Digital Solutions">
    <meta name="twitter:description" content="Transform your business with expert Salesforce consulting, custom development, and comprehensive digital solutions.">
    <meta name="twitter:image" content="https://fingertipplus.com/images/og-image.jpg">
    
    <title>Fingertip Plus - Salesforce & Digital Solutions</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="/">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="18" fill="#00A1E0"/>
                            <path d="M20 8L28 16L20 24L12 16L20 8Z" fill="white"/>
                            <circle cx="20" cy="28" r="4" fill="#FF6B35"/>
                        </svg>
                        <span>Fingertip Plus</span>
                    </a>
                </div>
                <div class="nav-menu">
                    <ul class="nav-links">
                        <li><a href="#home" class="active">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#industries">Industries</a></li>
                        <li><a href="/blog.php">Blog</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    <a href="#contact" class="btn btn-primary nav-cta">Get Started</a>
                </div>
                <button class="mobile-menu-toggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-background"></div>
        <div class="container">
            <div class="hero-content fade-in">
                <h1 class="hero-title">
                    Transform Your Business with
                    <span class="typing-text" data-words='["Salesforce Excellence", "Digital Innovation", "Custom Solutions", "Expert Consulting"]'></span>
                </h1>
                <p class="hero-subtitle">Empowering businesses with cutting-edge Salesforce solutions and comprehensive digital services</p>
                <div class="hero-cta">
                    <a href="#contact" class="btn btn-primary btn-large">Get Started</a>
                    <a href="#services" class="btn btn-secondary btn-large">Our Services</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item slide-up">
                        <div class="stat-number counter" data-target="200">0</div>
                        <div class="stat-label">Customers</div>
                    </div>
                    <div class="stat-item slide-up">
                        <div class="stat-number counter" data-target="260">0</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="stat-item slide-up">
                        <div class="stat-number counter" data-target="50">0</div>
                        <div class="stat-label">Experts</div>
                    </div>
                    <div class="stat-item slide-up">
                        <div class="stat-number counter" data-target="12">0</div>
                        <div class="stat-label">Industries</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">About Fingertip Plus</h2>
                <p class="section-subtitle">Your Trusted Partner in Digital Transformation</p>
            </div>
            <div class="about-content">
                <div class="about-text fade-in">
                    <p>Fingertip Plus is a leading Salesforce consulting and digital solutions company dedicated to transforming businesses through innovative technology. With over a decade of experience, we've helped 200+ organizations across 12+ industries achieve their digital transformation goals.</p>
                    <p>Our team of 50+ certified experts brings deep technical expertise and industry knowledge to deliver end-to-end solutions that drive real business value. From Salesforce implementation to custom application development, we're committed to excellence in everything we do.</p>
                </div>
                <div class="differentiators">
                    <div class="diff-card slide-up">
                        <div class="diff-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#00A1E0"/>
                            </svg>
                        </div>
                        <h3>Certified Experts</h3>
                        <p>Team of 50+ Salesforce certified professionals with deep technical expertise</p>
                    </div>
                    <div class="diff-card slide-up">
                        <div class="diff-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#1CB5AC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Proven Track Record</h3>
                        <p>260+ successful projects delivered across multiple industries worldwide</p>
                    </div>
                    <div class="diff-card slide-up">
                        <div class="diff-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="#FF6B35" stroke-width="2"/>
                                <path d="M12 6V18M3 12H21" stroke="#FF6B35" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3>End-to-End Solutions</h3>
                        <p>Comprehensive services from strategy to implementation and ongoing support</p>
                    </div>
                    <div class="diff-card slide-up">
                        <div class="diff-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0D1B2A" stroke-width="2"/>
                                <path d="M12 6V12L16 14" stroke="#0D1B2A" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>24/7 Support</h3>
                        <p>Round-the-clock support to ensure your systems run smoothly without interruption</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Our Services</h2>
                <p class="section-subtitle">Comprehensive Solutions for Your Business Needs</p>
            </div>
            <div class="services-grid">
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3H21V21H3V3Z" stroke="#00A1E0" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M9 9H15M9 13H15M9 17H12" stroke="#00A1E0" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>Salesforce Implementation</h3>
                    <p>Complete Salesforce setup and configuration tailored to your business processes and requirements.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#1CB5AC" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="#1CB5AC" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Consulting</h3>
                    <p>Expert guidance on Salesforce strategy, best practices, and optimization for maximum ROI.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="#FF6B35" stroke-width="2"/>
                            <path d="M14 2V8H20M12 18V12M9 15L12 12L15 15" stroke="#FF6B35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Custom App Development</h3>
                    <p>Build custom applications on Salesforce platform to meet your unique business needs.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" fill="#00A1E0"/>
                        </svg>
                    </div>
                    <h3>Lightning Development</h3>
                    <p>Modern, responsive interfaces using Salesforce Lightning framework and components.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 8L21 12M21 12L17 16M21 12H3" stroke="#1CB5AC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 16L3 12M3 12L7 8" stroke="#1CB5AC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Integration</h3>
                    <p>Seamlessly connect Salesforce with your existing systems and third-party applications.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6L9 17L4 12" stroke="#FF6B35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 17L20 6M9 17L4 12" stroke="#FF6B35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Data Migration</h3>
                    <p>Secure and efficient migration of your data to Salesforce with zero data loss.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 14L12 14.01M8 21V15C8 13.8954 8.89543 13 10 13H14C15.1046 13 16 13.8954 16 15V21M5 11H19C20.1046 11 21 10.1046 21 9V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V9C3 10.1046 3.89543 11 5 11Z" stroke="#0D1B2A" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>Training & Support</h3>
                    <p>Comprehensive training programs and ongoing support to maximize user adoption.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="#00A1E0" stroke-width="2"/>
                            <circle cx="12" cy="12" r="2" fill="#00A1E0"/>
                        </svg>
                    </div>
                    <h3>Web Development</h3>
                    <p>Custom web applications and responsive websites built with modern technologies.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="2" width="14" height="20" rx="2" stroke="#1CB5AC" stroke-width="2"/>
                            <path d="M12 18H12.01" stroke="#1CB5AC" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>Mobile App Development</h3>
                    <p>Native and hybrid mobile applications for iOS and Android platforms.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3V16C3 17.1046 3.89543 18 5 18H21M7 14L12 9L16 13L21 8" stroke="#FF6B35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Tableau & Analytics</h3>
                    <p>Advanced data visualization and analytics solutions for data-driven decision making.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="#0D1B2A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>WhatsApp API Integration</h3>
                    <p>Integrate WhatsApp Business API for enhanced customer communication and engagement.</p>
                </div>
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 8L10.89 13.26C11.5395 13.6728 12.4605 13.6728 13.11 13.26L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="#00A1E0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Marketing Cloud</h3>
                    <p>Salesforce Marketing Cloud implementation and email marketing automation solutions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Industries Section -->
    <section id="industries" class="industries-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Industries We Serve</h2>
                <p class="section-subtitle">Expert Solutions Across Diverse Sectors</p>
            </div>
            <div class="industries-grid">
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Tourism</h3>
                        <p>Enhance guest experiences and streamline operations</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Insurance</h3>
                        <p>Modernize policy management and claims processing</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Technology</h3>
                        <p>Accelerate innovation and product development</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Healthcare</h3>
                        <p>Improve patient care and operational efficiency</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Manufacturing</h3>
                        <p>Optimize production and supply chain management</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Finance</h3>
                        <p>Transform banking and financial services</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Automotive</h3>
                        <p>Drive sales and enhance customer relationships</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Retail</h3>
                        <p>Create seamless omnichannel experiences</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>FMCG</h3>
                        <p>Accelerate go-to-market and distribution</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Logistics</h3>
                        <p>Streamline operations and tracking systems</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Real Estate</h3>
                        <p>Modernize property management and sales</p>
                    </div>
                </div>
                <div class="industry-card slide-up" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&q=80');">
                    <div class="industry-overlay"></div>
                    <div class="industry-content">
                        <h3>Education</h3>
                        <p>Transform learning and student engagement</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Our Process</h2>
                <p class="section-subtitle">A Proven Methodology for Success</p>
            </div>
            <div class="process-timeline">
                <div class="timeline-item slide-up">
                    <div class="timeline-number">01</div>
                    <div class="timeline-content">
                        <h3>Discovery</h3>
                        <p>We analyze your business requirements, challenges, and goals to create a comprehensive project roadmap.</p>
                    </div>
                </div>
                <div class="timeline-item slide-up">
                    <div class="timeline-number">02</div>
                    <div class="timeline-content">
                        <h3>Strategy</h3>
                        <p>Develop a detailed implementation strategy aligned with your business objectives and best practices.</p>
                    </div>
                </div>
                <div class="timeline-item slide-up">
                    <div class="timeline-number">03</div>
                    <div class="timeline-content">
                        <h3>Development</h3>
                        <p>Our expert team builds and configures your solution with quality code and robust architecture.</p>
                    </div>
                </div>
                <div class="timeline-item slide-up">
                    <div class="timeline-number">04</div>
                    <div class="timeline-content">
                        <h3>Testing</h3>
                        <p>Rigorous testing ensures your solution works flawlessly before deployment to production.</p>
                    </div>
                </div>
                <div class="timeline-item slide-up">
                    <div class="timeline-number">05</div>
                    <div class="timeline-content">
                        <h3>Deployment</h3>
                        <p>Smooth rollout with minimal disruption to your business operations and comprehensive training.</p>
                    </div>
                </div>
                <div class="timeline-item slide-up">
                    <div class="timeline-number">06</div>
                    <div class="timeline-content">
                        <h3>Ongoing Support</h3>
                        <p>24/7 support and continuous optimization to ensure long-term success and maximum ROI.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">What Our Clients Say</h2>
                <p class="section-subtitle">Success Stories from Around the World</p>
            </div>
            <div class="testimonials-carousel">
                <div class="testimonial-track">
                    <div class="testimonial-slide active">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 8C10 8 9 9 9 11C9 13 10 14 12 14C14 14 15 13 15 11C15 9 14 8 14 8C14 8 13 9 12 9C11 9 10 8 10 8ZM3 8C3 8 2 9 2 11C2 13 3 14 5 14C7 14 8 13 8 11C8 9 7 8 7 8C7 8 6 9 5 9C4 9 3 8 3 8Z" fill="#00A1E0"/>
                                </svg>
                            </div>
                            <p class="testimonial-text">"Fingertip Plus transformed our sales process with their Salesforce implementation. Our team productivity increased by 45% within the first quarter. Their expertise and support are unmatched."</p>
                            <div class="testimonial-author">
                                <h4>Sarah Johnson</h4>
                                <p>VP of Sales, TechCorp Solutions</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 8C10 8 9 9 9 11C9 13 10 14 12 14C14 14 15 13 15 11C15 9 14 8 14 8C14 8 13 9 12 9C11 9 10 8 10 8ZM3 8C3 8 2 9 2 11C2 13 3 14 5 14C7 14 8 13 8 11C8 9 7 8 7 8C7 8 6 9 5 9C4 9 3 8 3 8Z" fill="#00A1E0"/>
                                </svg>
                            </div>
                            <p class="testimonial-text">"The custom mobile app developed by Fingertip Plus has revolutionized how we connect with our customers. The team delivered beyond our expectations with exceptional quality."</p>
                            <div class="testimonial-author">
                                <h4>Michael Chen</h4>
                                <p>CTO, RetailMax Group</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 8C10 8 9 9 9 11C9 13 10 14 12 14C14 14 15 13 15 11C15 9 14 8 14 8C14 8 13 9 12 9C11 9 10 8 10 8ZM3 8C3 8 2 9 2 11C2 13 3 14 5 14C7 14 8 13 8 11C8 9 7 8 7 8C7 8 6 9 5 9C4 9 3 8 3 8Z" fill="#00A1E0"/>
                                </svg>
                            </div>
                            <p class="testimonial-text">"Working with Fingertip Plus on our data migration was seamless. They handled our complex requirements with professionalism and delivered on time without any data loss."</p>
                            <div class="testimonial-author">
                                <h4>Emily Rodriguez</h4>
                                <p>Director of IT, HealthFirst Medical</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 8C10 8 9 9 9 11C9 13 10 14 12 14C14 14 15 13 15 11C15 9 14 8 14 8C14 8 13 9 12 9C11 9 10 8 10 8ZM3 8C3 8 2 9 2 11C2 13 3 14 5 14C7 14 8 13 8 11C8 9 7 8 7 8C7 8 6 9 5 9C4 9 3 8 3 8Z" fill="#00A1E0"/>
                                </svg>
                            </div>
                            <p class="testimonial-text">"The Tableau analytics dashboard created by Fingertip Plus gave us insights we never had before. Data-driven decision making is now at the core of our strategy."</p>
                            <div class="testimonial-author">
                                <h4>David Martinez</h4>
                                <p>CEO, Analytics Pro Inc</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 8C10 8 9 9 9 11C9 13 10 14 12 14C14 14 15 13 15 11C15 9 14 8 14 8C14 8 13 9 12 9C11 9 10 8 10 8ZM3 8C3 8 2 9 2 11C2 13 3 14 5 14C7 14 8 13 8 11C8 9 7 8 7 8C7 8 6 9 5 9C4 9 3 8 3 8Z" fill="#00A1E0"/>
                                </svg>
                            </div>
                            <p class="testimonial-text">"Outstanding service and support! The WhatsApp integration has improved our customer engagement significantly. Highly recommend Fingertip Plus for any digital transformation needs."</p>
                            <div class="testimonial-author">
                                <h4>Lisa Anderson</h4>
                                <p>Marketing Director, Global Ventures</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-controls">
                    <button class="testimonial-prev" aria-label="Previous testimonial">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="testimonial-dots">
                        <span class="dot active" data-slide="0"></span>
                        <span class="dot" data-slide="1"></span>
                        <span class="dot" data-slide="2"></span>
                        <span class="dot" data-slide="3"></span>
                        <span class="dot" data-slide="4"></span>
                    </div>
                    <button class="testimonial-next" aria-label="Next testimonial">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Latest Insights</h2>
                <p class="section-subtitle">Expert Articles and Industry Trends</p>
            </div>
            <div class="blog-grid" id="latest-blog-posts">
                <!-- Blog posts will be loaded via JavaScript from blog-api.php -->
                <div class="blog-loading">Loading latest posts...</div>
            </div>
            <div class="blog-cta">
                <a href="/blog.php" class="btn btn-primary">View All Posts</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Let's Discuss Your Project</p>
            </div>
            <div class="contact-wrapper">
                <div class="contact-form-container slide-up">
                    <form class="contact-form" method="POST" action="contact-handler.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required placeholder="john@example.com">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="+1 234 567 8900">
                        </div>
                        <div class="form-group">
                            <label for="company">Company Name</label>
                            <input type="text" id="company" name="company" placeholder="Your Company">
                        </div>
                        <div class="form-group">
                            <label for="service">Service Interested In *</label>
                            <select id="service_interest" name="service_interest" required>
                                <option value="">Select a service</option>
                                <option value="salesforce-implementation">Salesforce Implementation</option>
                                <option value="consulting">Consulting</option>
                                <option value="custom-development">Custom App Development</option>
                                <option value="lightning-development">Lightning Development</option>
                                <option value="integration">Integration</option>
                                <option value="data-migration">Data Migration</option>
                                <option value="training-support">Training & Support</option>
                                <option value="web-development">Web Development</option>
                                <option value="mobile-development">Mobile App Development</option>
                                <option value="analytics">Tableau & Analytics</option>
                                <option value="whatsapp-integration">WhatsApp API Integration</option>
                                <option value="marketing-cloud">Marketing Cloud</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="5" required placeholder="Tell us about your project..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-large">Send Message</button>
                    </form>
                </div>
                <div class="contact-info-container slide-up">
                    <div class="contact-info-card">
                        <h3>Contact Information</h3>
                        <div class="contact-info-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 5.02944 7.02944 1 12 1C16.9706 1 21 5.02944 21 10Z" stroke="#00A1E0" stroke-width="2"/>
                                <circle cx="12" cy="10" r="3" stroke="#00A1E0" stroke-width="2"/>
                            </svg>
                            <div>
                                <h4>Address</h4>
                                <p>123 Business Park, Tech City<br>Innovation District, CA 94000</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z" stroke="#00A1E0" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <h4>Phone</h4>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 8L10.89 13.26C11.5395 13.6728 12.4605 13.6728 13.11 13.26L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="#00A1E0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <h4>Email</h4>
                                <p>info@fingertipplus.com</p>
                            </div>
                        </div>
                        <div class="contact-social">
                            <h4>Follow Us</h4>
                            <div class="social-links">
                                <a href="#" aria-label="Facebook">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 2H15C13.6739 2 12.4021 2.52678 11.4645 3.46447C10.5268 4.40215 10 5.67392 10 7V10H7V14H10V22H14V14H17L18 10H14V7C14 6.73478 14.1054 6.48043 14.2929 6.29289C14.4804 6.10536 14.7348 6 15 6H18V2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <a href="#" aria-label="Twitter">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23 3C22.0424 3.67548 20.9821 4.19211 19.86 4.53C19.2577 3.83751 18.4573 3.34669 17.567 3.12393C16.6767 2.90116 15.7395 2.95718 14.8821 3.28445C14.0247 3.61173 13.2884 4.1944 12.773 4.95372C12.2575 5.71303 11.9877 6.61234 12 7.53V8.53C10.2426 8.57557 8.50127 8.18581 6.93101 7.39545C5.36074 6.60508 4.01032 5.43864 3 4C3 4 -1 13 8 17C5.94053 18.398 3.48716 19.0989 1 19C10 24 21 19 21 7.5C20.9991 7.22145 20.9723 6.94359 20.92 6.67C21.9406 5.66349 22.6608 4.39271 23 3V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <a href="#" aria-label="LinkedIn">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 8C17.5913 8 19.1174 8.63214 20.2426 9.75736C21.3679 10.8826 22 12.4087 22 14V21H18V14C18 13.4696 17.7893 12.9609 17.4142 12.5858C17.0391 12.2107 16.5304 12 16 12C15.4696 12 14.9609 12.2107 14.5858 12.5858C14.2107 12.9609 14 13.4696 14 14V21H10V14C10 12.4087 10.6321 10.8826 11.7574 9.75736C12.8826 8.63214 14.4087 8 16 8V8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 9H2V21H6V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 6C5.10457 6 6 5.10457 6 4C6 2.89543 5.10457 2 4 2C2.89543 2 2 2.89543 2 4C2 5.10457 2.89543 6 4 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <a href="#" aria-label="Instagram">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="18" fill="#00A1E0"/>
                            <path d="M20 8L28 16L20 24L12 16L20 8Z" fill="white"/>
                            <circle cx="20" cy="28" r="4" fill="#FF6B35"/>
                        </svg>
                        <span>Fingertip Plus</span>
                    </div>
                    <p class="footer-description">Your trusted partner for Salesforce consulting, custom development, and comprehensive digital transformation solutions.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M18 2H15C13.6739 2 12.4021 2.52678 11.4645 3.46447C10.5268 4.40215 10 5.67392 10 7V10H7V14H10V22H14V14H17L18 10H14V7C14 6.73478 14.1054 6.48043 14.2929 6.29289C14.4804 6.10536 14.7348 6 15 6H18V2Z" stroke="currentColor" stroke-width="2"/></svg></a>
                        <a href="#" aria-label="Twitter"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M23 3C22.0424 3.67548 20.9821 4.19211 19.86 4.53C19.2577 3.83751 18.4573 3.34669 17.567 3.12393C16.6767 2.90116 15.7395 2.95718 14.8821 3.28445C14.0247 3.61173 13.2884 4.1944 12.773 4.95372C12.2575 5.71303 11.9877 6.61234 12 7.53V8.53C10.2426 8.57557 8.50127 8.18581 6.93101 7.39545C5.36074 6.60508 4.01032 5.43864 3 4C3 4 -1 13 8 17C5.94053 18.398 3.48716 19.0989 1 19C10 24 21 19 21 7.5C20.9991 7.22145 20.9723 6.94359 20.92 6.67C21.9406 5.66349 22.6608 4.39271 23 3V3Z" stroke="currentColor" stroke-width="2"/></svg></a>
                        <a href="#" aria-label="LinkedIn"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M16 8C17.5913 8 19.1174 8.63214 20.2426 9.75736C21.3679 10.8826 22 12.4087 22 14V21H18V14C18 13.4696 17.7893 12.9609 17.4142 12.5858C17.0391 12.2107 16.5304 12 16 12C15.4696 12 14.9609 12.2107 14.5858 12.5858C14.2107 12.9609 14 13.4696 14 14V21H10V14C10 12.4087 10.6321 10.8826 11.7574 9.75736C12.8826 8.63214 14.4087 8 16 8V8Z" stroke="currentColor" stroke-width="2"/><path d="M6 9H2V21H6V9Z" stroke="currentColor" stroke-width="2"/><path d="M4 6C5.10457 6 6 5.10457 6 4C6 2.89543 5.10457 2 4 2C2.89543 2 2 2.89543 2 4C2 5.10457 2.89543 6 4 6Z" stroke="currentColor" stroke-width="2"/></svg></a>
                        <a href="#" aria-label="Instagram"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/></svg></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#industries">Industries</a></li>
                        <li><a href="/blog.php">Blog</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Services</h4>
                    <ul class="footer-links">
                        <li><a href="#services">Salesforce Implementation</a></li>
                        <li><a href="#services">Consulting</a></li>
                        <li><a href="#services">Custom Development</a></li>
                        <li><a href="#services">Integration</a></li>
                        <li><a href="#services">Data Migration</a></li>
                        <li><a href="#services">Training & Support</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Contact Us</h4>
                    <ul class="footer-contact">
                        <li>123 Business Park, Tech City<br>Innovation District, CA 94000</li>
                        <li>Phone: +1 (555) 123-4567</li>
                        <li>Email: info@fingertipplus.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Fingertip Plus. All rights reserved.</p>
                <div class="footer-legal">
                    <a href="/privacy-policy.html">Privacy Policy</a>
                    <a href="/terms-of-service.html">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="/js/main.js"></script>
</body>
</html>
