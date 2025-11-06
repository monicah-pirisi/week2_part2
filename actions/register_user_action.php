<?php
/**
 * Register User Action - Final Fixed Version
 */

declare(strict_types=1);

// ✅ Remove any accidental BOM/whitespace before this line!

// Enable strict error reporting
error_reporting(E_ALL);
ini_set('log_errors', 1);

// ✅ Load main config first — defines LOG_PATH, DB vars, etc.
require_once __DIR__ . '/../config/config.php';

// ✅ Use consistent error log path
$logPath = defined('LOG_PATH') ? LOG_PATH . 'register_errors.log' : __DIR__ . '/../logs/register_errors.log';
ini_set('error_log', $logPath);

// ✅ JSON header before any output
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize response
$response = [
    'status' => 'error',
    'message' => 'An error occurred during registration',
    'debug'   => []
];

try {
    // --- Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Expected POST.');
    }

    // --- Include controller file
    $controller_file = __DIR__ . '/../controllers/customer_controller.php';
    if (!file_exists($controller_file)) {
        throw new Exception("Controller file not found at: {$controller_file}");
    }
    require_once $controller_file;

    // --- Check controller functions
    if (!function_exists('register_customer_ctr')) {
        throw new Exception('register_customer_ctr() function not defined.');
    }

    // --- Validate inputs using helper
    $required = ['name', 'email', 'password', 'phone_number', 'country', 'city', 'role'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required.');
        }
    }

    $name         = trim($_POST['name']);
    $email        = trim($_POST['email']);
    $password     = $_POST['password'];
    $phone_number = trim($_POST['phone_number']);
    $country      = trim($_POST['country']);
    $city         = trim($_POST['city']);
    $role         = (int)$_POST['role'];

    // --- Simple sanitization
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $country = htmlspecialchars($country, ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');

    // --- Extra validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }
    if ($role === 1) {
        throw new Exception('Admin accounts cannot be created through registration.');
    }

    // --- Check for duplicate email
    if (function_exists('get_customer_by_email_ctr')) {
        $existing = get_customer_by_email_ctr($email);
        if ($existing) {
            throw new Exception('Email already exists. Please use a different email.');
        }
    }

    // --- Attempt registration
    $user_id = register_customer_ctr($name, $email, $password, $phone_number, $country, $city, $role);

    if (!$user_id || !is_numeric($user_id)) {
        throw new Exception('Registration failed. Please try again.');
    }

    // --- Registration success
    $response['status'] = 'success';
    $response['message'] = 'Registration successful! You can now log in.';
    $response['user_id'] = (int)$user_id;

    error_log("✅ Registration successful for {$email} (User ID: {$user_id})");

} catch (Throwable $e) {
    // --- Catch all errors (Exception + Fatal)
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();

    if (defined('APP_ENV') && APP_ENV === 'development') {
        $response['debug'] = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
    }

    error_log("❌ Registration error: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}");
}

// --- Hide debug info in production
if (defined('APP_ENV') && APP_ENV !== 'development') {
    unset($response['debug']);
}

// --- Return JSON
echo json_encode($response);
exit();
