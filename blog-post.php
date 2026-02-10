<?php
require_once 'config.php';

$slug = $_GET['slug'] ?? '';
$post = null;
$related_posts = [];

if (empty($slug)) {
    header('Location: /blog.php');
    exit;
}

try {
    $pdo = getDBConnection();
    
    if ($pdo) {
        // Get post
        $stmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, content, featured_image, author, created_at, updated_at
            FROM blog_posts
            WHERE slug = ? AND status = 'published'
        ");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
        
        // Get related posts
        if ($post) {
            $stmt = $pdo->prepare("
                SELECT id, title, slug, excerpt, featured_image, created_at
                FROM blog_posts
                WHERE status = 'published' AND id != ?
                ORDER BY created_at DESC
                LIMIT 3
            ");
            $stmt->execute([$post['id']]);
            $related_posts = $stmt->fetchAll();
        }
    }
} catch (Exception $e) {
    error_log("Blog post error: " . $e->getMessage());
}

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    echo "Post not found";
    exit;
}

$page_title = sanitize($post['title']) . ' - Fingertip Plus Blog';
$page_description = sanitize($post['excerpt']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php echo sanitize($post['title']); ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo sanitize($post['featured_image']); ?>">
    <meta property="og:url" content="<?php echo SITE_URL . '/blog/' . sanitize($post['slug']); ?>">
    <meta property="og:type" content="article">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo sanitize($post['title']); ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta name="twitter:image" content="<?php echo sanitize($post['featured_image']); ?>">
    
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">Fingertip<span class="logo-plus">+</span></a>
            <ul class="nav-menu">
                <li><a href="/#home">Home</a></li>
                <li><a href="/#about">About</a></li>
                <li><a href="/#services">Services</a></li>
                <li><a href="/#industries">Industries</a></li>
                <li><a href="/blog.php" class="active">Blog</a></li>
                <li><a href="/#contact">Contact</a></li>
            </ul>
            <a href="/#contact" class="cta-button">Get a Free Consultation</a>
            <button class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Blog Post -->
    <article class="blog-post-page">
        <header class="blog-post-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo sanitize($post['featured_image']); ?>')">
            <div class="container">
                <h1 class="fade-in"><?php echo sanitize($post['title']); ?></h1>
                <div class="post-meta fade-in">
                    <span class="post-author">By <?php echo sanitize($post['author'] ?? 'Fingertip Plus Team'); ?></span>
                    <span class="post-date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                </div>
            </div>
        </header>

        <div class="container">
            <div class="blog-post-content">
                <div class="post-body">
                    <?php echo $post['content']; ?>
                </div>

                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h4>Share this article:</h4>
                    <div class="share-links">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . '/blog/' . $post['slug']); ?>" target="_blank" class="share-btn linkedin">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            LinkedIn
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/blog/' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="share-btn twitter">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog/' . $post['slug']); ?>" target="_blank" class="share-btn facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                    </div>
                </div>
            </div>

            <!-- Related Posts -->
            <?php if (!empty($related_posts)): ?>
                <section class="related-posts">
                    <h2>Related Articles</h2>
                    <div class="related-posts-grid">
                        <?php foreach ($related_posts as $related): ?>
                            <article class="related-post-card">
                                <a href="/blog/<?php echo sanitize($related['slug']); ?>">
                                    <div class="related-post-image" style="background-image: url('<?php echo sanitize($related['featured_image'] ?? 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'); ?>')"></div>
                                    <h3><?php echo sanitize($related['title']); ?></h3>
                                    <p><?php echo sanitize($related['excerpt']); ?></p>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </article>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Fingertip<span class="logo-plus">+</span></h3>
                    <p>Your trusted Salesforce partner for digital transformation and business excellence.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/#about">About Us</a></li>
                        <li><a href="/#services">Services</a></li>
                        <li><a href="/#industries">Industries</a></li>
                        <li><a href="/blog.php">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li>Email: info@fingertipplus.com</li>
                        <li>Phone: +1 (555) 123-4567</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Fingertip Plus. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/js/main.js"></script>
</body>
</html>
