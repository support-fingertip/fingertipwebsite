<?php
/**
 * Contact Form Handler - Simplified for Static HTML
 * Receives form submissions, validates, and sends email
 */

header('Content-Type: application/json');

// Start session for rate limiting
session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting - max 5 submissions per IP per hour
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!isset($_SESSION['rate_limit_contact'])) {
    $_SESSION['rate_limit_contact'] = [];
}

// Clean old attempts
$current_time = time();
$_SESSION['rate_limit_contact'] = array_filter(
    $_SESSION['rate_limit_contact'],
    function($timestamp) use ($current_time) {
        return ($current_time - $timestamp) < 3600;
    }
);

// Check rate limit
if (count($_SESSION['rate_limit_contact']) >= 5) {
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
    // Add to rate limit tracking
    $_SESSION['rate_limit_contact'][] = $current_time;
    
    // Send email notification
    $to = 'info@fingertipplus.com'; // Change this to your email
    $subject = 'New Contact Form Submission - Fingertip Plus';
    
    $email_body = "New contact form submission received:\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Phone: $phone\n";
    $email_body .= "Company: $company\n";
    $email_body .= "Service Interest: $service_interest\n";
    $email_body .= "Message:\n$message\n\n";
    $email_body .= "Submitted at: " . date('Y-m-d H:i:s') . "\n";
    $email_body .= "IP Address: $client_ip\n";
    
    $headers = "From: noreply@fingertipplus.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Attempt to send email
    $mail_sent = @mail($to, $subject, $email_body, $headers);
    
    // You can also save to a database here if needed
    // For now, we'll just send the email
    
    if ($mail_sent) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your message! We will get back to you shortly.'
        ]);
    } else {
        // Even if email fails, acknowledge the submission
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your message! We have received your inquiry.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again or contact us directly at info@fingertipplus.com'
    ]);
}
