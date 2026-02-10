<?php
require_once 'config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$per_page = 9;
$offset = ($page - 1) * $per_page;

$posts = [];
$total_posts = 0;

try {
    $pdo = getDBConnection();
    
    if ($pdo) {
        // Get total count
        $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
        $total_posts = $stmt->fetchColumn();
        
        // Get posts
        $stmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, featured_image, author, created_at
            FROM blog_posts
            WHERE status = 'published'
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$per_page, $offset]);
        $posts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Blog error: " . $e->getMessage());
}

$total_pages = ceil($total_posts / $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Fingertip Plus | Salesforce Insights & Expertise</title>
    <meta name="description" content="Explore Salesforce insights, best practices, and industry trends from the experts at Fingertip Plus.">
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

    <!-- Blog Header -->
    <section class="blog-header">
        <div class="container">
            <h1 class="fade-in">Insights & Expertise</h1>
            <p class="fade-in">Explore the latest in Salesforce, CRM, and digital transformation</p>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="blog-grid-section">
        <div class="container">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <p>No blog posts available at the moment. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="blog-card fade-in">
                            <a href="/blog/<?php echo sanitize($post['slug']); ?>">
                                <div class="blog-card-image" style="background-image: url('<?php echo sanitize($post['featured_image'] ?? 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800'); ?>')"></div>
                                <div class="blog-card-content">
                                    <div class="blog-meta">
                                        <span class="blog-date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                        <span class="blog-author"><?php echo sanitize($post['author'] ?? 'Fingertip Plus Team'); ?></span>
                                    </div>
                                    <h3><?php echo sanitize($post['title']); ?></h3>
                                    <p><?php echo sanitize($post['excerpt']); ?></p>
                                    <span class="read-more">Read More →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

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
