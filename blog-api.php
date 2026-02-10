<?php
/**
 * Blog API - Returns latest blog posts as JSON
 * Used by the homepage to dynamically load blog previews
 */

require_once 'config.php';

header('Content-Type: application/json');

// Get limit parameter (default 3 for homepage)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;
$limit = max(1, min($limit, 12)); // Between 1 and 12

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        // Fallback to static content if database not available
        $fallback_posts = [
            [
                'id' => 1,
                'title' => 'Salesforce Lightning: The Future of CRM',
                'slug' => 'salesforce-lightning-future-of-crm',
                'excerpt' => 'Discover how Salesforce Lightning is transforming customer relationship management with its modern interface and powerful features.',
                'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
            ],
            [
                'id' => 2,
                'title' => '5 Ways Salesforce Integration Transforms Your Business',
                'slug' => '5-ways-salesforce-integration-transforms-business',
                'excerpt' => 'Learn how integrating Salesforce with your existing systems can streamline operations and boost productivity.',
                'featured_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
                'created_at' => date('Y-m-d H:i:s', strtotime('-14 days'))
            ],
            [
                'id' => 3,
                'title' => 'Custom Salesforce App Development: A Complete Guide',
                'slug' => 'custom-salesforce-app-development-guide',
                'excerpt' => 'Everything you need to know about building custom applications on the Salesforce platform.',
                'featured_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800',
                'created_at' => date('Y-m-d H:i:s', strtotime('-21 days'))
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'posts' => array_slice($fallback_posts, 0, $limit)
        ]);
        exit;
    }
    
    // Fetch from database
    $stmt = $pdo->prepare("
        SELECT id, title, slug, excerpt, featured_image, author, created_at
        FROM blog_posts
        WHERE status = 'published'
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    $posts = $stmt->fetchAll();
    
    // Format dates
    foreach ($posts as &$post) {
        $post['created_at'] = date('F j, Y', strtotime($post['created_at']));
    }
    
    echo json_encode([
        'success' => true,
        'posts' => $posts
    ]);
    
} catch (Exception $e) {
    error_log("Blog API error: " . $e->getMessage());
    
    // Return fallback content on error
    $fallback_posts = [
        [
            'id' => 1,
            'title' => 'Salesforce Lightning: The Future of CRM',
            'slug' => 'salesforce-lightning-future-of-crm',
            'excerpt' => 'Discover how Salesforce Lightning is transforming customer relationship management.',
            'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
            'created_at' => 'Recent'
        ],
        [
            'id' => 2,
            'title' => '5 Ways Salesforce Integration Transforms Your Business',
            'slug' => '5-ways-salesforce-integration-transforms-business',
            'excerpt' => 'Learn how integrating Salesforce can streamline your operations.',
            'featured_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
            'created_at' => 'Recent'
        ],
        [
            'id' => 3,
            'title' => 'Custom Salesforce App Development Guide',
            'slug' => 'custom-salesforce-app-development-guide',
            'excerpt' => 'Everything you need to know about custom Salesforce development.',
            'featured_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800',
            'created_at' => 'Recent'
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'posts' => array_slice($fallback_posts, 0, $limit)
    ]);
}
