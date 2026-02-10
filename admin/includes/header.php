<header class="admin-header">
    <div class="admin-header-left">
        <button class="sidebar-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h2>Fingertip<span class="logo-plus">+</span> Admin</h2>
    </div>
    <div class="admin-header-right">
        <a href="/" target="_blank" class="btn btn-sm">View Website</a>
        <span class="admin-user"><?php echo sanitize($_SESSION['admin_username']); ?></span>
        <a href="/admin/logout.php" class="btn btn-sm btn-secondary">Logout</a>
    </div>
</header>
