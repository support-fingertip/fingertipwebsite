<?php
/**
 * Contact Form Handler
 * Receives form submissions, validates, stores in database, and sends email
 */

require_once 'config.php';

header('Content-Type: application/json');

// Start session for CSRF and rate limiting
session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Rate limiting - max 5 submissions per IP per hour
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!checkRateLimit('contact_' . $client_ip, 5, 3600)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many submissions. Please try again later.']);
    exit;
}

// Validate and sanitize input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$company = trim($_POST['company'] ?? '');
$service_interest = trim($_POST['service_interest'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

// Validation rules
if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email address is required';
}

if (!empty($phone) && !preg_match('/^[0-9\s\-\+\(\)]{7,20}$/', $phone)) {
    $errors[] = 'Invalid phone number format';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, phone, company, service_interest, message, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $name,
        $email,
        $phone,
        $company,
        $service_interest,
        $message
    ]);
    
    // Send email notification
    $to = SITE_EMAIL;
    $subject = 'New Contact Form Submission - ' . SITE_NAME;
    
    $email_body = "New contact form submission received:\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Phone: $phone\n";
    $email_body .= "Company: $company\n";
    $email_body .= "Service Interest: $service_interest\n";
    $email_body .= "Message:\n$message\n\n";
    $email_body .= "Submitted at: " . date('Y-m-d H:i:s') . "\n";
    $email_body .= "IP Address: $client_ip\n";
    
    $headers = "From: noreply@" . parse_url(SITE_URL, PHP_URL_HOST) . "\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Attempt to send email (may not work on all hosting environments)
    @mail($to, $subject, $email_body, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you shortly.'
    ]);
    
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again or contact us directly at ' . SITE_EMAIL
    ]);
}
