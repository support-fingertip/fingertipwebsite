<?php
session_start();

// ── Auth Check ─────────────────────────────────────────────────────────────
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// ── CSRF Token ─────────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Load Data ──────────────────────────────────────────────────────────────
$blog_file = __DIR__ . '/data/blogs.json';
$blogs = [];
if (file_exists($blog_file)) {
    $blogs = json_decode(file_get_contents($blog_file), true) ?: [];
}

// ── Flash Message ──────────────────────────────────────────────────────────
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── Current View ───────────────────────────────────────────────────────────
$action = $_GET['action'] ?? 'list';
$edit_id = intval($_GET['id'] ?? 0);

// Load editing blog
$edit_blog = null;
if ($action === 'edit' && $edit_id > 0) {
    foreach ($blogs as $b) {
        if ($b['id'] === $edit_id) {
            $edit_blog = $b;
            break;
        }
    }
    if (!$edit_blog) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Blog post not found.'];
        header('Location: blog.php');
        exit;
    }
}

$show_form = ($action === 'new' || $action === 'edit');

// ── Categories ─────────────────────────────────────────────────────────────
$categories = ['Salesforce', 'Web Development', 'Mobile', 'AI/ML', 'Cloud', 'Security', 'UX Design'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts - Fingertip Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ── Reset & Base ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F0F4F8;
            color: #1a1a2e;
            min-height: 100vh;
            line-height: 1.6;
        }
        a { text-decoration: none; color: inherit; }

        /* ── Admin Layout ─────────────────────────────────────────────── */
        .admin-layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ──────────────────────────────────────────────────── */
        .sidebar {
            width: 260px;
            background: #0C2340;
            color: #fff;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
        }
        .sidebar-header {
            padding: 24px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h2 { font-size: 22px; font-weight: 800; }
        .sidebar-header h2 span { color: #1A7AF8; }
        .sidebar-header p { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px; }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 10px;
            color: rgba(255,255,255,0.7); font-size: 14px; font-weight: 500;
            transition: all 0.2s; margin-bottom: 4px;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-nav a.active { background: #1A7AF8; color: #fff; }
        .sidebar-nav a svg { width: 20px; height: 20px; flex-shrink: 0; }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 10px;
            color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-footer a:hover { background: rgba(220,38,38,0.15); color: #f87171; }

        /* ── Main Content ─────────────────────────────────────────────── */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .page-header h1 { font-size: 28px; font-weight: 800; color: #0C2340; }
        .page-header p { color: #6b7280; font-size: 14px; margin-top: 2px; }

        /* ── Buttons ──────────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border-radius: 10px;
            font-size: 13px; font-weight: 600; font-family: inherit;
            cursor: pointer; border: none; transition: all 0.2s;
        }
        .btn-primary { background: #1A7AF8; color: #fff; }
        .btn-primary:hover { background: #1565d8; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-danger { background: #fef2f2; color: #dc2626; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-success { background: #ECFDF5; color: #059669; }
        .btn-success:hover { background: #D1FAE5; }
        .btn-warning { background: #FFFBEB; color: #D97706; }
        .btn-warning:hover { background: #FEF3C7; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

        /* ── Flash Messages ───────────────────────────────────────────── */
        .flash {
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .flash-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .flash-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }

        /* ── Card ─────────────────────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h3 { font-size: 16px; font-weight: 700; color: #0C2340; }
        .card-body { padding: 24px; }

        /* ── Table ────────────────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        thead th {
            text-align: left;
            padding: 12px 16px;
            background: #F9FAFB;
            color: #6b7280;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }
        tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        tbody tr:hover { background: #F9FAFB; }
        tbody tr:last-child td { border-bottom: none; }
        .td-title {
            font-weight: 600;
            color: #0C2340;
            max-width: 300px;
        }
        .td-title a:hover { color: #1A7AF8; }
        .td-actions {
            display: flex;
            gap: 6px;
            white-space: nowrap;
        }

        /* ── Badges ───────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-published { background: #ECFDF5; color: #059669; }
        .badge-draft { background: #FFFBEB; color: #D97706; }
        .badge-cat {
            background: #EBF5FF;
            color: #1A7AF8;
            text-transform: none;
            letter-spacing: 0;
        }

        /* ── Form Styles ─────────────────────────────────────────────── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group { margin-bottom: 0; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-group label .required { color: #dc2626; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
            outline: none;
            background: #fff;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #1A7AF8;
        }
        .form-group textarea { resize: vertical; min-height: 120px; line-height: 1.6; }
        .form-group .help-text {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid #F0F4F8;
        }

        /* ── Empty State ──────────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        .empty-state svg { margin-bottom: 16px; }
        .empty-state h3 { font-size: 18px; font-weight: 700; color: #6b7280; margin-bottom: 8px; }
        .empty-state p { font-size: 14px; margin-bottom: 20px; }

        /* ── Mobile ───────────────────────────────────────────────────── */
        .mobile-toggle {
            display: none;
            position: fixed; top: 16px; left: 16px; z-index: 200;
            background: #0C2340; color: #fff; border: none; border-radius: 10px;
            width: 44px; height: 44px; cursor: pointer;
            align-items: center; justify-content: center;
        }
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 90;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; padding: 24px 16px; padding-top: 72px; }
            .mobile-toggle { display: flex; }
            .form-grid { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .td-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open'); document.querySelector('.sidebar-overlay').classList.toggle('open');">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h14M3 10h14M3 14h14"/></svg>
    </button>
    <div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open'); this.classList.remove('open');"></div>

    <div class="admin-layout">
        <!-- ── Sidebar ────────────────────────────────────────────────── -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Finger<span>tip</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="blog.php" class="active">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    Blog Posts
                </a>
                <a href="work.php">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Work / Portfolio
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="index.php?logout=1">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </div>
        </aside>

        <!-- ── Main Content ───────────────────────────────────────────── -->
        <main class="main-content">
            <?php if ($flash): ?>
                <div class="flash flash-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
                    <?php if ($flash['type'] === 'success'): ?>
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php else: ?>
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <?php if ($show_form): ?>
                <!-- ════════════ BLOG FORM ════════════ -->
                <div class="page-header">
                    <div>
                        <h1><?php echo $edit_blog ? 'Edit Blog Post' : 'New Blog Post'; ?></h1>
                        <p><?php echo $edit_blog ? 'Update the details below.' : 'Fill in the details to create a new blog post.'; ?></p>
                    </div>
                    <a href="blog.php" class="btn btn-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to List
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="api.php?action=save_blog" id="blogForm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <?php if ($edit_blog): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_blog['id']; ?>">
                            <?php endif; ?>

                            <div class="form-grid">
                                <div class="form-group full">
                                    <label for="title">Title <span class="required">*</span></label>
                                    <input type="text" id="title" name="title" required
                                           value="<?php echo htmlspecialchars($edit_blog['title'] ?? ''); ?>"
                                           placeholder="Enter the blog post title">
                                </div>

                                <div class="form-group">
                                    <label for="category">Category <span class="required">*</span></label>
                                    <select id="category" name="category" required>
                                        <option value="">Select a category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>"
                                                <?php echo ($edit_blog['category'] ?? '') === $cat ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="author">Author <span class="required">*</span></label>
                                    <input type="text" id="author" name="author" required
                                           value="<?php echo htmlspecialchars($edit_blog['author'] ?? ''); ?>"
                                           placeholder="Author name">
                                </div>

                                <div class="form-group">
                                    <label for="featured_image">Featured Image URL</label>
                                    <input type="url" id="featured_image" name="featured_image"
                                           value="<?php echo htmlspecialchars($edit_blog['featured_image'] ?? ''); ?>"
                                           placeholder="https://example.com/image.jpg">
                                </div>

                                <div class="form-group">
                                    <label for="publish_date">Publish Date</label>
                                    <input type="date" id="publish_date" name="publish_date"
                                           value="<?php echo htmlspecialchars($edit_blog['publish_date'] ?? date('Y-m-d')); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <input type="text" id="tags" name="tags"
                                           value="<?php echo htmlspecialchars(implode(', ', $edit_blog['tags'] ?? [])); ?>"
                                           placeholder="Tag1, Tag2, Tag3">
                                    <div class="help-text">Separate tags with commas</div>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status">
                                        <option value="draft" <?php echo ($edit_blog['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?php echo ($edit_blog['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                    </select>
                                </div>

                                <div class="form-group full">
                                    <label for="content">Content <span class="required">*</span></label>
                                    <textarea id="content" name="content" required rows="16"
                                              placeholder="Write your blog content here. You can use markdown-style formatting:&#10;&#10;## Heading&#10;**Bold text**&#10;- List item&#10;&#10;Regular paragraph text..."><?php echo htmlspecialchars($edit_blog['content'] ?? ''); ?></textarea>
                                    <div class="help-text">Supports markdown-style formatting: ## headings, **bold**, *italic*, - lists</div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    <?php echo $edit_blog ? 'Update Post' : 'Create Post'; ?>
                                </button>
                                <a href="blog.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- ════════════ BLOG LIST ════════════ -->
                <div class="page-header">
                    <div>
                        <h1>Blog Posts</h1>
                        <p>Manage your blog content. <?php echo count($blogs); ?> total post<?php echo count($blogs) !== 1 ? 's' : ''; ?>.</p>
                    </div>
                    <a href="blog.php?action=new" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                        New Blog Post
                    </a>
                </div>

                <div class="card">
                    <?php if (empty($blogs)): ?>
                        <div class="empty-state">
                            <svg width="48" height="48" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <h3>No blog posts yet</h3>
                            <p>Get started by creating your first blog post.</p>
                            <a href="blog.php?action=new" class="btn btn-primary">Create First Post</a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Author</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blogs as $blog): ?>
                                    <tr>
                                        <td class="td-title">
                                            <a href="blog.php?action=edit&id=<?php echo $blog['id']; ?>">
                                                <?php echo htmlspecialchars($blog['title']); ?>
                                            </a>
                                        </td>
                                        <td><span class="badge badge-cat"><?php echo htmlspecialchars($blog['category']); ?></span></td>
                                        <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                        <td style="white-space:nowrap;"><?php echo htmlspecialchars($blog['publish_date'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($blog['status'] ?? 'draft') === 'published' ? 'badge-published' : 'badge-draft'; ?>">
                                                <?php echo htmlspecialchars($blog['status'] ?? 'draft'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="td-actions">
                                                <a href="blog.php?action=edit&id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-secondary" title="Edit">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </a>
                                                <form method="POST" action="api.php?action=toggle_blog_status&id=<?php echo $blog['id']; ?>" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                                    <button type="submit" class="btn btn-sm <?php echo ($blog['status'] ?? 'draft') === 'published' ? 'btn-warning' : 'btn-success'; ?>" title="Toggle status">
                                                        <?php echo ($blog['status'] ?? 'draft') === 'published' ? 'Unpublish' : 'Publish'; ?>
                                                    </button>
                                                </form>
                                                <form method="POST" action="api.php?action=delete_blog&id=<?php echo $blog['id']; ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this blog post? This action cannot be undone.');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
