<?php
session_start();

// ── Configuration ──────────────────────────────────────────────────────────
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'fingertip2026');

// ── CSRF Token ─────────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Handle Login ───────────────────────────────────────────────────────────
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $login_error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === ADMIN_USER && $password === ADMIN_PASS) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $username;
            $_SESSION['login_time'] = time();
            header('Location: index.php');
            exit;
        } else {
            $login_error = 'Invalid username or password.';
        }
    }
}

// ── Handle Logout ──────────────────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// ── Check Auth ─────────────────────────────────────────────────────────────
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// ── Load Stats ─────────────────────────────────────────────────────────────
$blogs = [];
$works = [];
if ($is_logged_in) {
    $blog_file = __DIR__ . '/data/blogs.json';
    $work_file = __DIR__ . '/data/works.json';
    if (file_exists($blog_file)) {
        $blogs = json_decode(file_get_contents($blog_file), true) ?: [];
    }
    if (file_exists($work_file)) {
        $works = json_decode(file_get_contents($work_file), true) ?: [];
    }
}

$total_blogs = count($blogs);
$published_blogs = count(array_filter($blogs, fn($b) => ($b['status'] ?? '') === 'published'));
$draft_blogs = $total_blogs - $published_blogs;
$total_works = count($works);

// Categories breakdown
$blog_categories = [];
foreach ($blogs as $b) {
    $cat = $b['category'] ?? 'Uncategorized';
    $blog_categories[$cat] = ($blog_categories[$cat] ?? 0) + 1;
}
$work_categories = [];
foreach ($works as $w) {
    $cat = $w['category'] ?? 'Uncategorized';
    $work_categories[$cat] = ($work_categories[$cat] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_logged_in ? 'Dashboard' : 'Login'; ?> - Fingertip Admin</title>
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

        /* ── Login Screen ─────────────────────────────────────────────── */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0C2340 0%, #1A7AF8 100%);
            padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0C2340;
        }
        .login-logo h1 span { color: #1A7AF8; }
        .login-logo p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-group input:focus {
            border-color: #1A7AF8;
        }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: #1A7AF8;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-btn:hover { background: #1565d8; }
        .login-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        /* ── Admin Layout ─────────────────────────────────────────────── */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ──────────────────────────────────────────────────── */
        .sidebar {
            width: 260px;
            background: #0C2340;
            color: #fff;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
        }
        .sidebar-header {
            padding: 24px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h2 {
            font-size: 22px;
            font-weight: 800;
        }
        .sidebar-header h2 span { color: #1A7AF8; }
        .sidebar-header p {
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            margin-top: 2px;
        }
        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .sidebar-nav a.active {
            background: #1A7AF8;
            color: #fff;
        }
        .sidebar-nav a svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-footer a:hover {
            background: rgba(220,38,38,0.15);
            color: #f87171;
        }

        /* ── Main Content ─────────────────────────────────────────────── */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }
        .page-header {
            margin-bottom: 32px;
        }
        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0C2340;
        }
        .page-header p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }

        /* ── Stats Grid ───────────────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .stat-card .stat-icon svg {
            width: 24px;
            height: 24px;
        }
        .stat-card .stat-icon.blue { background: #EBF5FF; color: #1A7AF8; }
        .stat-card .stat-icon.green { background: #ECFDF5; color: #059669; }
        .stat-card .stat-icon.amber { background: #FFFBEB; color: #D97706; }
        .stat-card .stat-icon.purple { background: #F3E8FF; color: #7C3AED; }
        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #0C2340;
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ── Content Cards ────────────────────────────────────────────── */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 24px;
        }
        .content-card {
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
        }
        .content-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #0C2340;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #F0F4F8;
        }
        .category-list {
            list-style: none;
        }
        .category-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .category-list li:last-child { border-bottom: none; }
        .category-list li span.cat-name { color: #374151; font-weight: 500; }
        .category-list li span.cat-count {
            background: #EBF5FF;
            color: #1A7AF8;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .recent-item {
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .recent-item:last-child { border-bottom: none; }
        .recent-item h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .recent-item .meta {
            font-size: 12px;
            color: #9ca3af;
        }
        .recent-item .meta span {
            margin-right: 12px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-published { background: #ECFDF5; color: #059669; }
        .badge-draft { background: #FFFBEB; color: #D97706; }

        .quick-links { display: flex; gap: 12px; margin-top: 8px; }
        .quick-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: #1A7AF8;
            color: #fff;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .quick-link:hover { background: #1565d8; }
        .quick-link.secondary {
            background: #0C2340;
        }
        .quick-link.secondary:hover { background: #0a1c33; }

        /* ── Mobile Toggle ────────────────────────────────────────────── */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 200;
            background: #0C2340;
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 44px;
            height: 44px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 90;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; padding: 24px 16px; padding-top: 72px; }
            .mobile-toggle { display: flex; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .content-grid { grid-template-columns: 1fr; }
            .login-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
<?php if (!$is_logged_in): ?>
    <!-- ════════════ LOGIN SCREEN ════════════ -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <h1>Finger<span>tip</span></h1>
                <p>Admin Panel</p>
            </div>
            <?php if ($login_error): ?>
                <div class="login-error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username" placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </div>
                <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- ════════════ ADMIN DASHBOARD ════════════ -->
    <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open'); document.querySelector('.sidebar-overlay').classList.toggle('open');">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h14M3 10h14M3 14h14"/></svg>
    </button>
    <div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open'); this.classList.remove('open');"></div>

    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Finger<span>tip</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="active">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="blog.php">
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

        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_user']); ?>. Here is an overview of your content.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <div class="stat-number"><?php echo $total_blogs; ?></div>
                    <div class="stat-label">Total Blog Posts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="stat-number"><?php echo $published_blogs; ?></div>
                    <div class="stat-label">Published Posts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div class="stat-number"><?php echo $draft_blogs; ?></div>
                    <div class="stat-label">Draft Posts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="stat-number"><?php echo $total_works; ?></div>
                    <div class="stat-label">Work / Portfolio Items</div>
                </div>
            </div>

            <div class="quick-links" style="margin-bottom: 32px;">
                <a href="blog.php?action=new" class="quick-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    New Blog Post
                </a>
                <a href="work.php?action=new" class="quick-link secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    New Work Item
                </a>
            </div>

            <div class="content-grid">
                <!-- Recent Blog Posts -->
                <div class="content-card">
                    <h3>Recent Blog Posts</h3>
                    <?php
                    $recent_blogs = array_slice($blogs, 0, 5);
                    if (empty($recent_blogs)): ?>
                        <p style="color:#9ca3af; font-size:14px;">No blog posts yet. Create your first post.</p>
                    <?php else:
                        foreach ($recent_blogs as $blog): ?>
                        <div class="recent-item">
                            <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($blog['category']); ?></span>
                                <span><?php echo htmlspecialchars($blog['publish_date'] ?? ''); ?></span>
                                <span class="badge <?php echo ($blog['status'] ?? 'draft') === 'published' ? 'badge-published' : 'badge-draft'; ?>">
                                    <?php echo htmlspecialchars($blog['status'] ?? 'draft'); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach;
                    endif; ?>
                </div>

                <!-- Blog Categories -->
                <div class="content-card">
                    <h3>Blog Categories</h3>
                    <?php if (empty($blog_categories)): ?>
                        <p style="color:#9ca3af; font-size:14px;">No categories yet.</p>
                    <?php else: ?>
                        <ul class="category-list">
                            <?php foreach ($blog_categories as $name => $count): ?>
                            <li>
                                <span class="cat-name"><?php echo htmlspecialchars($name); ?></span>
                                <span class="cat-count"><?php echo $count; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Recent Work Items -->
                <div class="content-card">
                    <h3>Recent Work Items</h3>
                    <?php
                    $recent_works = array_slice($works, 0, 5);
                    if (empty($recent_works)): ?>
                        <p style="color:#9ca3af; font-size:14px;">No work items yet. Add your first project.</p>
                    <?php else:
                        foreach ($recent_works as $work): ?>
                        <div class="recent-item">
                            <h4><?php echo htmlspecialchars($work['title']); ?></h4>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($work['client'] ?? ''); ?></span>
                                <span><?php echo htmlspecialchars($work['category'] ?? ''); ?></span>
                                <span><?php echo htmlspecialchars($work['timeline'] ?? ''); ?></span>
                            </div>
                        </div>
                    <?php endforeach;
                    endif; ?>
                </div>

                <!-- Work Categories -->
                <div class="content-card">
                    <h3>Work Categories</h3>
                    <?php if (empty($work_categories)): ?>
                        <p style="color:#9ca3af; font-size:14px;">No categories yet.</p>
                    <?php else: ?>
                        <ul class="category-list">
                            <?php foreach ($work_categories as $name => $count): ?>
                            <li>
                                <span class="cat-name"><?php echo htmlspecialchars($name); ?></span>
                                <span class="cat-count"><?php echo $count; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
<?php endif; ?>
</body>
</html>
