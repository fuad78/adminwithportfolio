<?php
// Database Configuration
// For Docker: use 'mysql' as host (service name)
// For Local: use 'localhost'
$db_host = getenv('DB_HOST') ?: 'mysql'; // Default to 'mysql' for Docker, change to 'localhost' for local
define('DB_HOST', $db_host);
define('DB_USER', 'fuaditme_portfolio_user');
define('DB_PASS', 'Portfolio@2024');
define('DB_NAME', 'fuaditme_portfolio_db');

// SMTP / Mail configuration (override via environment variables in production)
define('SMTP_HOST', getenv('SMTP_HOST') ?: '');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_ENCRYPTION', strtolower(getenv('SMTP_ENCRYPTION') ?: 'tls')); // tls or ssl
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@example.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Portfolio Admin');

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Get base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = dirname($script);
    return $protocol . '://' . $host . ($path !== '/' ? $path : '');
}