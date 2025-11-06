<?php
/**
 * Application Configuration
 * -------------------------------------------------------------
 * Loads environment variables from the .env file and defines
 * constants used across the application.
 *
 * @author Taste of Africa
 * @version 2.1
 */

// Prevent direct access
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

/**
 * Load environment variables from .env file
 */
function env($key, $default = null) {
    static $env_loaded = false;
    static $env_vars = [];

    if (!$env_loaded) {
        $env_file = __DIR__ . '/../.env';
        if (file_exists($env_file)) {
            $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value, " \t\n\r\0\x0B\"'");
                    $env_vars[$name] = $value;
                }
            }
        }
        $env_loaded = true;
    }

    return isset($env_vars[$key]) && $env_vars[$key] !== '' ? $env_vars[$key] : $default;
}

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================
$db_host = env('DB_HOST', 'localhost');
$db_port = 3306;

if (strpos($db_host, ':') !== false) {
    list($db_host, $db_port) = explode(':', $db_host, 2);
    $db_port = intval($db_port);
}

define('SERVER', $db_host);
define('DB_PORT', $db_port);
define('USERNAME', env('DB_USERNAME', 'root'));
define('PASSWD', env('DB_PASSWORD', ''));
define('DATABASE', env('DB_NAME', 'ecommerce_2025A_monicah_lekupe'));

// Simple validation
if (!USERNAME || !DATABASE) {
    error_log("Missing DB credentials in .env");
    if (env('APP_ENV', 'development') === 'development') {
        die("ERROR: Missing database credentials in .env");
    } else {
        die("Database connection error. Please contact admin.");
    }
}

// ============================================================================
// APPLICATION CONFIGURATION
// ============================================================================
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN));

// Base path
define('BASE_PATH', dirname(__DIR__));

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================
define('SESSION_TIMEOUT', intval(env('SESSION_TIMEOUT', 3600)));
define('SESSION_NAME', 'TASTE_OF_AFRICA_SESSION');

// Auto-start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// ============================================================================
// FILE UPLOAD CONFIGURATION
// ============================================================================
define('MAX_FILE_SIZE', intval(env('MAX_FILE_SIZE', 5242880))); // 5MB
define('ALLOWED_EXTENSIONS', env('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif'));
define('UPLOAD_DIR', BASE_PATH . '/uploads/');

// ============================================================================
// LOGGING
// ============================================================================
define('LOG_LEVEL', env('LOG_LEVEL', 'debug'));
define('LOG_PATH', BASE_PATH . '/' . env('LOG_PATH', 'logs/'));

if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

function log_message($message, $level = 'info') {
    $log_file = LOG_PATH . 'app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] [$level] $message\n", 3, $log_file);
}

// ============================================================================
// ERROR REPORTING
// ============================================================================
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_message = date('[Y-m-d H:i:s]') . " Error [$errno]: $errstr in $errfile:$errline\n";
    $log_file = LOG_PATH . 'error_' . date('Y-m-d') . '.log';
    error_log($error_message, 3, $log_file);

    if (APP_DEBUG) {
        echo "<b>Error:</b> [$errno] $errstr in <b>$errfile</b> on line <b>$errline</b><br>";
    }
});

// ============================================================================
// SECURITY
// ============================================================================
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_DURATION', 900);
define('CSRF_TOKEN_EXPIRY', 7200);

date_default_timezone_set('Africa/Accra');

// ============================================================================
// HELPER FUNCTIONS (sanitization, CSRF, redirect, etc.)
// ============================================================================
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $token) &&
           (time() - $_SESSION['csrf_token_time'] <= CSRF_TOKEN_EXPIRY);
}

function redirect($url, $status_code = 302) {
    header("Location: $url", true, $status_code);
    exit();
}

function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

// ============================================================================
// FINAL INIT LOG
// ============================================================================
if (APP_DEBUG) {
    log_message('Configuration loaded successfully', 'info');
    log_message("Environment: " . APP_ENV, 'debug');
    log_message("Database: " . SERVER . ":" . DB_PORT . "/" . DATABASE, 'debug');
}
?>
