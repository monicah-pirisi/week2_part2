<?php
/**
 * Core Settings and Functions
 * Handles sessions, authentication, sanitization, and common utilities
 */

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database class (ensure path is correct)
require_once __DIR__ . '/db_class.php';

// Set default timezone
date_default_timezone_set('UTC');

/**
 * Check session timeout
 * @return void
 */
function checkSessionTimeout()
{
    // Only check if user is logged in
    if (empty($_SESSION['user_id'])) {
        return;
    }

    // Initialize login_time if not set
    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = time();
        return;
    }

    // Get timeout (default: 1 hour)
    $timeout = defined('SESSION_TIMEOUT') ? (int)SESSION_TIMEOUT : 3600;

    // If expired → logout and redirect
    if ((time() - $_SESSION['login_time']) > $timeout) {
        logout();

        // Store message and redirect user
        $_SESSION['timeout_message'] = 'Your session has expired. Please log in again.';
        header('Location: ../login/login.php'); // ✅ fixed path for correct redirection
        exit();
    }

    // Update last active time
    $_SESSION['login_time'] = time();
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    checkSessionTimeout();
    return !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin()
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? null) == 1;
}

/**
 * Check if user is customer
 */
function isCustomer()
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? null) == 0;
}

/**
 * Check if user is restaurant owner
 */
function isRestaurantOwner()
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? null) == 2;
}

/**
 * Get current user details
 */
function getCurrentUserId()     { return $_SESSION['user_id']     ?? null; }
function getCurrentUserName()   { return $_SESSION['user_name']   ?? null; }
function getCurrentUserEmail()  { return $_SESSION['user_email']  ?? null; }
function getCurrentUserRole()   { return $_SESSION['user_role']   ?? null; }

/**
 * Logout user
 */
function logout()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

/**
 * Redirect helper
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

/**
 * Sanitize input
 */
function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * CSRF Protection
 */
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get client IP address
 */
function getClientIP()
{
    return $_SERVER['HTTP_CLIENT_IP'] ??
           $_SERVER['HTTP_X_FORWARDED_FOR'] ??
           $_SERVER['REMOTE_ADDR'] ??
           '0.0.0.0';
}

/**
 * Log custom errors
 */
function logError($message, $file = '', $line = 0)
{
    $log_message = sprintf(
        "[%s] ERROR: %s%s%s\n",
        date('Y-m-d H:i:s'),
        $message,
        $file ? " in $file" : '',
        $line ? " on line $line" : ''
    );
    error_log($log_message);
}

// Recommended: Centralized error logging (keeps consistency with config.php)
if (defined('LOG_PATH')) {
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'error.log');
} else {
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

//  Disable display_errors in production (uncomment before deployment)
// ini_set('display_errors', 0);
// error_reporting(E_ALL);

?>
