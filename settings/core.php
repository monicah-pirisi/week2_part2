<?php
/**
 * Core Settings and Functions
 * Contains essential functions for authentication and session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database class
require_once(__DIR__ . '/db_class.php');

// Set default timezone
date_default_timezone_set('UTC');

/**
 * Check if user is logged in
 * @return bool - True if logged in, False otherwise
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool - True if admin, False otherwise
 */
function isAdmin()
{
    // Check if user is logged in first
    if (!isLoggedIn()) {
        return false;
    }
    
    // Check if user_role is set and equals 1 (admin)
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

/**
 * Check if user is a customer
 * @return bool - True if customer, False otherwise
 */
function isCustomer()
{
    // Check if user is logged in first
    if (!isLoggedIn()) {
        return false;
    }
    
    // Check if user_role is set and equals 0 (customer)
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0;
}

/**
 * Check if user is a restaurant owner
 * @return bool - True if restaurant owner, False otherwise
 */
function isRestaurantOwner()
{
    // Check if user is logged in first
    if (!isLoggedIn()) {
        return false;
    }
    
    // Check if user_role is set and equals 2 (restaurant owner)
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2;
}

/**
 * Get current user ID
 * @return int|null - User ID or null if not logged in
 */
function getCurrentUserId()
{
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user name
 * @return string|null - User name or null if not logged in
 */
function getCurrentUserName()
{
    return isLoggedIn() ? ($_SESSION['user_name'] ?? null) : null;
}

/**
 * Get current user email
 * @return string|null - User email or null if not logged in
 */
function getCurrentUserEmail()
{
    return isLoggedIn() ? ($_SESSION['user_email'] ?? null) : null;
}

/**
 * Get current user role
 * @return int|null - User role (0=customer, 1=admin, 2=restaurant owner) or null if not logged in
 */
function getCurrentUserRole()
{
    return isLoggedIn() ? ($_SESSION['user_role'] ?? null) : null;
}

/**
 * Logout user
 * @return void
 */
function logout()
{
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Redirect to a specific page
 * @param string $url - URL to redirect to
 * @return void
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

/**
 * Sanitize input data
 * @param string $data - Data to sanitize
 * @return string - Sanitized data
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email address
 * @param string $email - Email to validate
 * @return bool - True if valid, False otherwise
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 * @return string - CSRF token
 */
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token - Token to verify
 * @return bool - True if valid, False otherwise
 */
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if request is AJAX
 * @return bool - True if AJAX, False otherwise
 */
function isAjaxRequest()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get client IP address
 * @return string - Client IP address
 */
function getClientIP()
{
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    return $ip;
}

/**
 * Log error message
 * @param string $message - Error message
 * @param string $file - File where error occurred
 * @param int $line - Line number where error occurred
 * @return void
 */
function logError($message, $file = '', $line = 0)
{
    $log_message = date('Y-m-d H:i:s') . " - Error: " . $message;
    
    if (!empty($file)) {
        $log_message .= " in " . $file;
    }
    
    if ($line > 0) {
        $log_message .= " on line " . $line;
    }
    
    error_log($log_message);
}

// Error handling for production
// Uncomment these in production environment
// ini_set('display_errors', 0);
// error_reporting(E_ALL);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../logs/error.log');
?>