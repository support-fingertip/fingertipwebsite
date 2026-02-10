<?php
require_once 'auth.php';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid security token');
    }
    
    $pdo = getDBConnection();
    if ($pdo && isset($_POST['id'])) {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
    header('Location: /admin/blog-list.php');
    exit;
}

// Get filters
$status = $_GET['status'] ?? 'all';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$posts = [];
$total_posts = 0;

try {
    $pdo = getDBConnection();
    
    if ($pdo) {
        // Build query
        $where = $status !== 'all' ? "WHERE status = ?" : "";
        $params = $status !== 'all' ? [$status] : [];
        
        // Get total count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts $where");
        $stmt->execute($params);
        $total_posts = $stmt->fetchColumn();
        
        // Get posts
        $stmt = $pdo->prepare("
            SELECT * FROM blog_posts
            $where
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute(array_merge($params, [$per_page, $offset]));
        $posts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Blog list error: " . $e->getMessage());
}

$total_pages = ceil($total_posts / $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts - Admin Panel</title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-page">
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="page-header">
                <h1>Blog Posts</h1>
                <div class="page-actions">
                    <a href="/admin/blog-create.php" class="btn btn-primary">Create New Post</a>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?php echo $status === 'all' ? 'active' : ''; ?>">
                    All Posts
                </a>
                <a href="?status=published" class="filter-tab <?php echo $status === 'published' ? 'active' : ''; ?>">
                    Published
                </a>
                <a href="?status=draft" class="filter-tab <?php echo $status === 'draft' ? 'active' : ''; ?>">
                    Drafts
                </a>
            </div>
            
            <!-- Posts Table -->
            <?php if (empty($posts)): ?>
                <div class="no-data">
                    <p>No blog posts found.</p>
                    <a href="/admin/blog-create.php" class="btn btn-primary">Create Your First Post</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo sanitize($post['title']); ?></strong>
                                        <?php if ($post['status'] === 'published'): ?>
                                            <br><small><a href="/blog/<?php echo sanitize($post['slug']); ?>" target="_blank">View Post →</a></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo sanitize($post['author']); ?></td>
                                    <td>
                                        <?php if ($post['status'] === 'published'): ?>
                                            <span class="badge badge-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($post['updated_at'])); ?></td>
                                    <td class="actions-cell">
                                        <a href="/admin/blog-edit.php?id=<?php echo $post['id']; ?>" class="btn-link">Edit</a>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-link text-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>" class="pagination-btn">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>" 
                               class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>" class="pagination-btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
