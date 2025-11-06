<?php
/**
 * Application Configuration
 *
 * This file loads environment variables from .env file and defines
 * constants used throughout the application.
 *
 * @package Register_Lab
 * @author Taste of Africa E-commerce
 * @version 2.0
 */

// Prevent direct access
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

/**
 * Load environment variables from .env file
 *
 * @param string $key Environment variable key
 * @param mixed $default Default value if key not found
 * @return mixed Environment variable value or default
 */
function env($key, $default = null) {
    static $env_loaded = false;
    static $env_vars = [];

    // Load .env file only once
    if (!$env_loaded) {
        $env_file = __DIR__ . '/../.env';

        if (file_exists($env_file)) {
            $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse key=value pairs
                if (strpos($line, '=') !== false) {
                    list($key_name, $value) = explode('=', $line, 2);
                    $key_name = trim($key_name);
                    $value = trim($value);

                    // Remove quotes if present
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                        $value = substr($value, 1, -1);
                    }

                    $env_vars[$key_name] = $value;
                }
            }
        }

        $env_loaded = true;
    }

    // Return default if key is not set OR if value is empty
    if (!isset($env_vars[$key]) || $env_vars[$key] === '') {
        return $default;
    }

    return $env_vars[$key];
}

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================

// Parse database host and port
$db_host = env('DB_HOST', null);
$db_port = 3306; // Default MySQL port

// Check if DB_HOST is not configured
if ($db_host === null || $db_host === '') {
    // Throw an error if in development mode
    if (env('APP_ENV', 'development') === 'development') {
        die("ERROR: DB_HOST is not configured in .env file. Please set DB_HOST to your database server (e.g., 169.239.251.102:3306 or localhost)");
    } else {
        error_log("CRITICAL: DB_HOST is not configured in .env file");
        die("Database configuration error. Please contact the administrator.");
    }
}

// Check if host includes port (format: host:port)
if (strpos($db_host, ':') !== false) {
    list($db_host, $db_port) = explode(':', $db_host, 2);
    $db_port = intval($db_port);
}

// Define database constants
define("SERVER", $db_host);
define("DB_PORT", $db_port);
define("USERNAME", env('DB_USERNAME', null));
define("PASSWD", env('DB_PASSWORD', null));
define("DATABASE", env('DB_NAME', null));

// Validate required database credentials
if (USERNAME === null || DATABASE === null) {
    if (env('APP_ENV', 'development') === 'development') {
        die("ERROR: Database credentials (DB_USERNAME, DB_NAME) must be configured in .env file");
    } else {
        error_log("CRITICAL: Missing database credentials in .env file");
        die("Database configuration error. Please contact the administrator.");
    }
}

// ============================================================================
// APPLICATION CONFIGURATION
// ============================================================================

// Application environment (development, staging, production)
define("APP_ENV", env('APP_ENV', 'development'));

// Debug mode
define("APP_DEBUG", filter_var(env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN));

// Application URL
define("APP_URL", env('APP_URL', 'http://localhost/register_lab'));

// Base path
define("BASE_PATH", dirname(__DIR__));

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================

// Session timeout in seconds (default: 1 hour)
define("SESSION_TIMEOUT", intval(env('SESSION_TIMEOUT', '3600')));

// Session name
define("SESSION_NAME", env('SESSION_NAME', 'TASTE_OF_AFRICA_SESSION'));

// ============================================================================
// FILE UPLOAD CONFIGURATION
// ============================================================================

// Maximum file upload size in bytes (default: 5MB)
define("MAX_FILE_SIZE", intval(env('MAX_FILE_SIZE', '5242880')));

// Allowed file extensions for uploads
define("ALLOWED_EXTENSIONS", env('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif'));

// Upload directory
define("UPLOAD_DIR", BASE_PATH . '/uploads/');

// ============================================================================
// SECURITY CONFIGURATION
// ============================================================================

// Password minimum length
define("PASSWORD_MIN_LENGTH", 8);

// Maximum login attempts before lockout
define("MAX_LOGIN_ATTEMPTS", 5);

// Login lockout duration in seconds (default: 15 minutes)
define("LOGIN_LOCKOUT_DURATION", 900);

// CSRF token expiration in seconds (default: 2 hours)
define("CSRF_TOKEN_EXPIRY", 7200);

// ============================================================================
// LOGGING CONFIGURATION
// ============================================================================

// Log level (debug, info, warning, error)
define("LOG_LEVEL", env('LOG_LEVEL', 'debug'));

// Log path
define("LOG_PATH", BASE_PATH . '/' . env('LOG_PATH', 'logs/'));

// Enable error logging
define("ENABLE_ERROR_LOG", true);

// ============================================================================
// USER ROLES
// ============================================================================

// Define user role constants
define("ROLE_CUSTOMER", 0);
define("ROLE_ADMIN", 1);
define("ROLE_RESTAURANT_OWNER", 2);

// ============================================================================
// PAGINATION
// ============================================================================

// Default items per page
define("ITEMS_PER_PAGE", 12);

// ============================================================================
// ERROR REPORTING
// ============================================================================

// Set error reporting based on environment
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

// Custom error handler
if (ENABLE_ERROR_LOG) {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        $error_message = date('[Y-m-d H:i:s]') . " Error [$errno]: $errstr in $errfile on line $errline\n";

        // Create logs directory if it doesn't exist
        if (!file_exists(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }

        // Write to error log
        $log_file = LOG_PATH . 'error_' . date('Y-m-d') . '.log';
        error_log($error_message, 3, $log_file);

        // Display error in development mode
        if (APP_DEBUG) {
            echo "<b>Error:</b> [$errno] $errstr in <b>$errfile</b> on line <b>$errline</b><br>";
        }

        return false; // Let PHP's internal error handler run as well
    });
}

// ============================================================================
// TIMEZONE CONFIGURATION
// ============================================================================

// Set default timezone
date_default_timezone_set(env('TIMEZONE', 'Africa/Accra'));

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Check if application is in debug mode
 * @return bool
 */
function is_debug_mode() {
    return APP_DEBUG;
}

/**
 * Check if application is in production
 * @return bool
 */
function is_production() {
    return APP_ENV === 'production';
}

/**
 * Get application base URL
 * @return string
 */
function base_url($path = '') {
    $url = rtrim(APP_URL, '/');
    if ($path) {
        $url .= '/' . ltrim($path, '/');
    }
    return $url;
}

/**
 * Log a message to file
 *
 * @param string $message Message to log
 * @param string $level Log level (debug, info, warning, error)
 */
function log_message($message, $level = 'info') {
    // Create logs directory if it doesn't exist
    if (!file_exists(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }

    $log_file = LOG_PATH . 'app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message\n";

    error_log($log_entry, 3, $log_file);
}

/**
 * Sanitize user input
 *
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate CSRF token
 *
 * @return string CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION)) {
        session_start();
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();

    return $token;
}

/**
 * Verify CSRF token
 *
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }

    // Check if token has expired
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRY) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect to a URL
 *
 * @param string $url URL to redirect to
 * @param int $status_code HTTP status code
 */
function redirect($url, $status_code = 302) {
    header("Location: " . $url, true, $status_code);
    exit();
}

/**
 * Check if request is POST
 *
 * @return bool
 */
function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is AJAX
 *
 * @return bool
 */
function is_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get POST data
 *
 * @param string $key Key to get from POST
 * @param mixed $default Default value if key not found
 * @return mixed
 */
function post($key, $default = null) {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Get GET data
 *
 * @param string $key Key to get from GET
 * @param mixed $default Default value if key not found
 * @return mixed
 */
function get($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// ============================================================================
// AUTO-START SESSION
// ============================================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// ============================================================================
// INITIALIZATION COMPLETE
// ============================================================================

// Log initialization in debug mode
if (APP_DEBUG) {
    log_message("Configuration loaded successfully", "info");
    log_message("Environment: " . APP_ENV, "debug");
    log_message("Database: " . SERVER . ":" . DB_PORT . "/" . DATABASE, "debug");
}

?>
