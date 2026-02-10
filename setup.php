<?php
/**
 * Fingertip Plus - Database Setup Script
 * 
 * Run this file ONCE after uploading to create the required database tables.
 * Then DELETE this file for security.
 */

require_once 'config.php';

// Check if already run
$setup_marker = __DIR__ . '/.setup_complete';
if (file_exists($setup_marker)) {
    die('Setup has already been completed. Delete .setup_complete file to run again.');
}

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        throw new Exception('Could not connect to database. Please check your config.php settings.');
    }
    
    // Create contacts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        company VARCHAR(100),
        service_interest VARCHAR(100),
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_is_read (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Create blog_posts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        excerpt TEXT,
        content LONGTEXT NOT NULL,
        featured_image VARCHAR(500),
        author VARCHAR(100) DEFAULT 'Fingertip Plus Team',
        status ENUM('draft', 'published') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_slug (slug),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Create admin_users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Insert default admin user
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE username=username");
    $stmt->execute([ADMIN_USERNAME, ADMIN_PASSWORD_HASH]);
    
    // Insert sample blog posts
    $sample_posts = [
        [
            'title' => 'Salesforce Lightning: The Future of CRM',
            'slug' => 'salesforce-lightning-future-of-crm',
            'excerpt' => 'Discover how Salesforce Lightning is transforming customer relationship management with its modern interface and powerful features.',
            'content' => '<h2>Introduction to Salesforce Lightning</h2><p>Salesforce Lightning represents a significant leap forward in CRM technology. With its component-based architecture and modern user interface, Lightning provides businesses with tools to work faster and smarter.</p><h3>Key Benefits</h3><ul><li>Enhanced user experience with intuitive interface</li><li>Faster performance and improved productivity</li><li>Powerful Lightning App Builder for customization</li><li>Mobile-first design for on-the-go access</li></ul><h3>Why Migrate to Lightning?</h3><p>Organizations that have migrated to Lightning Experience report increased user adoption, improved sales productivity, and better customer satisfaction. The modern interface makes it easier for teams to collaborate and access the information they need.</p><p>At Fingertip Plus, we specialize in Lightning migration and development, helping businesses make the transition smoothly and efficiently.</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200',
            'status' => 'published'
        ],
        [
            'title' => '5 Ways Salesforce Integration Transforms Your Business',
            'slug' => '5-ways-salesforce-integration-transforms-business',
            'excerpt' => 'Learn how integrating Salesforce with your existing systems can streamline operations and boost productivity.',
            'content' => '<h2>The Power of Integration</h2><p>In today\'s connected world, businesses rely on multiple systems and applications. Integrating Salesforce with your existing tools creates a unified ecosystem that enhances efficiency and data accuracy.</p><h3>Top 5 Integration Benefits</h3><ol><li><strong>Unified Data View:</strong> Access all customer information in one place</li><li><strong>Automated Workflows:</strong> Eliminate manual data entry and reduce errors</li><li><strong>Real-time Synchronization:</strong> Keep data consistent across all platforms</li><li><strong>Enhanced Analytics:</strong> Make better decisions with comprehensive data insights</li><li><strong>Improved Customer Experience:</strong> Provide seamless service across all touchpoints</li></ol><h3>Popular Integration Scenarios</h3><p>Common integrations include ERP systems, marketing automation platforms, customer support tools, and accounting software. Each integration brings unique value to your organization.</p><p>Contact Fingertip Plus to discuss your integration needs and discover how we can connect your systems for maximum efficiency.</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200',
            'status' => 'published'
        ],
        [
            'title' => 'Custom Salesforce App Development: A Complete Guide',
            'slug' => 'custom-salesforce-app-development-guide',
            'excerpt' => 'Everything you need to know about building custom applications on the Salesforce platform.',
            'content' => '<h2>Why Build Custom Salesforce Apps?</h2><p>While Salesforce provides extensive out-of-the-box functionality, every business has unique needs. Custom app development allows you to tailor Salesforce to your specific requirements.</p><h3>Development Options</h3><p>Salesforce offers multiple development approaches:</p><ul><li><strong>Lightning Components:</strong> Build modern, responsive UI components</li><li><strong>Visualforce:</strong> Create custom pages and interfaces</li><li><strong>Apex:</strong> Develop server-side business logic</li><li><strong>Heroku:</strong> Extend functionality with external applications</li></ul><h3>Best Practices</h3><p>Successful custom development requires careful planning, following Salesforce best practices, and maintaining code quality. Our team at Fingertip Plus has extensive experience building robust, scalable applications on the Salesforce platform.</p><h3>From Concept to Deployment</h3><p>The development process includes requirements gathering, design, development, testing, and deployment. We work closely with clients to ensure the final product meets their needs and exceeds expectations.</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1200',
            'status' => 'published'
        ]
    ];
    
    foreach ($sample_posts as $post) {
        $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $post['title'],
            $post['slug'],
            $post['excerpt'],
            $post['content'],
            $post['featured_image'],
            $post['status']
        ]);
    }
    
    // Mark setup as complete
    file_put_contents($setup_marker, date('Y-m-d H:i:s'));
    
    $success_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Setup Complete - Fingertip Plus</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 5px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 20px; border-radius: 5px; margin-top: 20px; }
            h1 { color: #00A1E0; }
            code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>✅ Setup Complete!</h1>
        <div class='success'>
            <h2>Database tables created successfully:</h2>
            <ul>
                <li>contacts</li>
                <li>blog_posts (with 3 sample posts)</li>
                <li>admin_users</li>
            </ul>
        </div>
        <div class='warning'>
            <h3>⚠️ Important Security Steps:</h3>
            <ol>
                <li><strong>DELETE this file (setup.php) immediately!</strong></li>
                <li>Change your admin password after first login</li>
                <li>Update config.php with your production credentials</li>
                <li>Ensure uploads directory is writable (chmod 755)</li>
            </ol>
        </div>
        <h3>Next Steps:</h3>
        <ul>
            <li>Admin panel: <a href='/admin/'>/admin/</a></li>
            <li>Default username: <code>admin</code></li>
            <li>Default password: <code>fingertip@2024</code></li>
            <li>Main website: <a href='/'>View Homepage</a></li>
        </ul>
    </body>
    </html>
    ";
    
    echo $success_message;
    
} catch (Exception $e) {
    $error_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Setup Error - Fingertip Plus</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 5px; }
            h1 { color: #dc3545; }
            code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; display: block; margin: 10px 0; }
        </style>
    </head>
    <body>
        <h1>❌ Setup Error</h1>
        <div class='error'>
            <h3>Error Message:</h3>
            <code>" . htmlspecialchars($e->getMessage()) . "</code>
            <h3>Troubleshooting:</h3>
            <ol>
                <li>Check your database credentials in config.php</li>
                <li>Ensure your database exists on your hosting account</li>
                <li>Verify your MySQL user has proper permissions</li>
                <li>Contact your hosting provider if issues persist</li>
            </ol>
        </div>
    </body>
    </html>
    ";
    
    echo $error_message;
}
