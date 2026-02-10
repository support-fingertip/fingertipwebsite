<?php
/**
 * Admin Authentication Middleware
 * Include this at the top of all admin pages that require authentication
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/index.php');
    exit;
}

// Check session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: /admin/index.php?timeout=1');
    exit;
}

$_SESSION['last_activity'] = time();

// Regenerate session ID periodically
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 600) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

require_once __DIR__ . '/../config.php';
