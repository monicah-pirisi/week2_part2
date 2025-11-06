<?php
/**
 * Application Configuration Loader
 * Loads environment variables from .env file
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found at: ' . $path);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            // Set as environment variable if not already set
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Get environment variable with default fallback
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }

    // Convert string booleans to actual booleans
    if (is_string($value)) {
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }
        if (strtolower($value) === 'null') {
            return null;
        }
    }

    return $value;
}

// Load .env file from project root
$envPath = __DIR__ . '/../.env';
try {
    loadEnv($envPath);
} catch (Exception $e) {
    // If .env doesn't exist, show helpful error
    die('Configuration Error: ' . $e->getMessage() . '<br>Please copy .env.example to .env and configure your settings.');
}

// Define database constants from environment variables
if (!defined("SERVER")) {
    define("SERVER", env('DB_HOST', 'localhost'));
}

if (!defined("USERNAME")) {
    define("USERNAME", env('DB_USERNAME', 'root'));
}

if (!defined("PASSWD")) {
    define("PASSWD", env('DB_PASSWORD', ''));
}

if (!defined("DATABASE")) {
    define("DATABASE", env('DB_NAME', 'dbforlab'));
}

// Application configuration
if (!defined("APP_ENV")) {
    define("APP_ENV", env('APP_ENV', 'production'));
}

if (!defined("APP_DEBUG")) {
    define("APP_DEBUG", env('APP_DEBUG', false));
}

if (!defined("APP_URL")) {
    define("APP_URL", env('APP_URL', 'http://localhost'));
}

if (!defined("SESSION_TIMEOUT")) {
    define("SESSION_TIMEOUT", env('SESSION_TIMEOUT', 3600));
}

if (!defined("MAX_FILE_SIZE")) {
    define("MAX_FILE_SIZE", env('MAX_FILE_SIZE', 5242880)); // 5MB default
}

if (!defined("ALLOWED_EXTENSIONS")) {
    define("ALLOWED_EXTENSIONS", env('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif'));
}

if (!defined("LOG_LEVEL")) {
    define("LOG_LEVEL", env('LOG_LEVEL', 'error'));
}

if (!defined("LOG_PATH")) {
    define("LOG_PATH", env('LOG_PATH', 'logs/'));
}

// Configure error reporting based on environment
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} elseif (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    // Staging
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Set error log file
$logDir = __DIR__ . '/../' . LOG_PATH;
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . 'php_errors.log');

?>
