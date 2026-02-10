<?php
/**
 * Image Upload Handler
 * Handles image uploads for blog posts
 */

session_start();

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['image'];

// Validate file
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$max_size = MAX_UPLOAD_SIZE; // 5MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
    exit;
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('blog_', true) . '.' . $extension;
$upload_path = UPLOAD_DIR . $filename;

// Ensure upload directory exists
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Set proper permissions
    chmod($upload_path, 0644);
    
    $url = UPLOAD_URL . $filename;
    
    echo json_encode([
        'success' => true,
        'url' => $url,
        'filename' => $filename
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
