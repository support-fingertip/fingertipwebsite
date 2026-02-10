<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /admin/dashboard.php');
    exit;
}

require_once __DIR__ . '/../config.php';

$error = '';
$timeout = isset($_GET['timeout']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Fingertip Plus</title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>Fingertip<span class="logo-plus">+</span></h1>
                <p>Admin Panel</p>
            </div>
            
            <?php if ($timeout): ?>
                <div class="alert alert-warning">
                    Your session has expired. Please login again.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/admin/login.php" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="login-footer">
                <p><a href="/">← Back to Website</a></p>
            </div>
        </div>
    </div>
</body>
</html>
