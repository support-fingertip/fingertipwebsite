<?php
/**
 * Fingertip Plus - Configuration File
 * 
 * IMPORTANT: Update these settings with your GoDaddy database credentials
 * after uploading to your hosting account.
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');  // Change this
define('DB_USER', 'your_username');        // Change this
define('DB_PASS', 'your_password');        // Change this

// Admin Configuration
define('ADMIN_USERNAME', 'admin');
// Default password is 'fingertip@2024' - CHANGE THIS after first login!
define('ADMIN_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

// Site Configuration
define('SITE_URL', 'https://fingertipplus.com');  // Change to your domain
define('SITE_NAME', 'Fingertip Plus');
define('SITE_EMAIL', 'info@fingertipplus.com');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 1); // Set to 1 in production with HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

/**
 * Get database connection
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize output for HTML
 * @param string $string
 * @return string
 */
function sanitize($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Check rate limit
 * @param string $key
 * @param int $max_attempts
 * @param int $time_window seconds
 * @return bool
 */
function checkRateLimit($key, $max_attempts = 5, $time_window = 3600) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $rate_limit_key = 'rate_limit_' . $key;
    $current_time = time();
    
    if (!isset($_SESSION[$rate_limit_key])) {
        $_SESSION[$rate_limit_key] = [];
    }
    
    // Remove old attempts outside time window
    $_SESSION[$rate_limit_key] = array_filter(
        $_SESSION[$rate_limit_key],
        function($timestamp) use ($current_time, $time_window) {
            return ($current_time - $timestamp) < $time_window;
        }
    );
    
    // Check if limit exceeded
    if (count($_SESSION[$rate_limit_key]) >= $max_attempts) {
        return false;
    }
    
    // Add current attempt
    $_SESSION[$rate_limit_key][] = $current_time;
    return true;
}
