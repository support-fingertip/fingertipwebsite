<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/index.php');
    exit;
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    die('Invalid security token');
}

// Rate limiting for login attempts
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!checkRateLimit('login_' . $ip, 5, 900)) {
    die('Too many login attempts. Please try again in 15 minutes.');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Simple authentication (can be extended to use database)
if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    $_SESSION['last_activity'] = time();
    $_SESSION['created'] = time();
    
    header('Location: /admin/dashboard.php');
    exit;
} else {
    // Invalid credentials
    sleep(2); // Slow down brute force attempts
    header('Location: /admin/index.php?error=1');
    exit;
}
