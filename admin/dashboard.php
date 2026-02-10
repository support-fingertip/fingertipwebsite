<?php
require_once 'auth.php';

// Get statistics
$stats = [
    'total_contacts' => 0,
    'unread_contacts' => 0,
    'total_posts' => 0,
    'published_posts' => 0
];

$recent_contacts = [];
$recent_posts = [];

try {
    $pdo = getDBConnection();
    
    if ($pdo) {
        // Contact stats
        $stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
        $stats['total_contacts'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0");
        $stats['unread_contacts'] = $stmt->fetchColumn();
        
        // Blog stats
        $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
        $stats['total_posts'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
        $stats['published_posts'] = $stmt->fetchColumn();
        
        // Recent contacts
        $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
        $recent_contacts = $stmt->fetchAll();
        
        // Recent posts
        $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5");
        $recent_posts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-page">
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo sanitize($_SESSION['admin_username']); ?>!</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #00A1E0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_contacts']; ?></h3>
                        <p>Total Contacts</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #FF6B35;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['unread_contacts']; ?></h3>
                        <p>Unread Messages</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #1CB5AC;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_posts']; ?></h3>
                        <p>Total Blog Posts</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #0D1B2A;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['published_posts']; ?></h3>
                        <p>Published Posts</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="dashboard-grid">
                <!-- Recent Contacts -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Contacts</h2>
                        <a href="/admin/contacts.php" class="btn btn-sm">View All</a>
                    </div>
                    <?php if (empty($recent_contacts)): ?>
                        <p class="no-data">No contacts yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_contacts as $contact): ?>
                                        <tr>
                                            <td><?php echo sanitize($contact['name']); ?></td>
                                            <td><?php echo sanitize($contact['email']); ?></td>
                                            <td><?php echo sanitize($contact['service_interest'] ?? 'N/A'); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($contact['created_at'])); ?></td>
                                            <td>
                                                <?php if ($contact['is_read']): ?>
                                                    <span class="badge badge-success">Read</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Unread</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Blog Posts -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Blog Posts</h2>
                        <a href="/admin/blog-list.php" class="btn btn-sm">View All</a>
                    </div>
                    <?php if (empty($recent_posts)): ?>
                        <p class="no-data">No blog posts yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_posts as $post): ?>
                                        <tr>
                                            <td><?php echo sanitize($post['title']); ?></td>
                                            <td>
                                                <?php if ($post['status'] === 'published'): ?>
                                                    <span class="badge badge-success">Published</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                            <td>
                                                <a href="/admin/blog-edit.php?id=<?php echo $post['id']; ?>" class="btn-link">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
