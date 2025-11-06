<?php
/**
 * Register User Action - FIXED VERSION
 * Handles user registration with improved error handling
 */

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/register_errors.log');

// Set response header to JSON first (before any output)
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => 'An error occurred during registration',
    'debug' => array() // Add debug info in development
);

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Invalid request method. Please use POST.';
        $response['debug']['method'] = $_SERVER['REQUEST_METHOD'];
        echo json_encode($response);
        exit();
    }

    // Check if required files exist
    $controller_file = __DIR__ . '/../controllers/customer_controller.php';
    if (!file_exists($controller_file)) {
        $response['message'] = 'System error: Controller file not found';
        $response['debug']['missing_file'] = $controller_file;
        error_log("Registration error: Controller file not found at " . $controller_file);
        echo json_encode($response);
        exit();
    }

    // Include controller
    require_once $controller_file;

    // Check if the register function exists
    if (!function_exists('register_customer_ctr')) {
        $response['message'] = 'System error: Registration function not available';
        error_log("Registration error: register_customer_ctr function does not exist");
        echo json_encode($response);
        exit();
    }

    // Get and validate name
    if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
        $response['message'] = 'Name is required';
        echo json_encode($response);
        exit();
    }
    $name = trim($_POST['name']);
    
    if (strlen($name) < 2) {
        $response['message'] = 'Name must be at least 2 characters long';
        echo json_encode($response);
        exit();
    }
    
    if (strlen($name) > 100) {
        $response['message'] = 'Name must not exceed 100 characters';
        echo json_encode($response);
        exit();
    }

    // Get and validate email
    if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
        $response['message'] = 'Email is required';
        echo json_encode($response);
        exit();
    }
    $email = trim($_POST['email']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address';
        echo json_encode($response);
        exit();
    }

    // Validate password
    if (!isset($_POST['password']) || empty($_POST['password'])) {
        $response['message'] = 'Password is required';
        echo json_encode($response);
        exit();
    }
    $password = $_POST['password'];
    
    if (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters long';
        echo json_encode($response);
        exit();
    }
    
    if (strlen($password) > 255) {
        $response['message'] = 'Password is too long';
        echo json_encode($response);
        exit();
    }

    // Validate phone number
    if (!isset($_POST['phone_number']) || empty(trim($_POST['phone_number']))) {
        $response['message'] = 'Phone number is required';
        echo json_encode($response);
        exit();
    }
    $phone_number = trim($_POST['phone_number']);
    
    // Basic phone validation (digits, spaces, hyphens, parentheses, plus sign)
    if (!preg_match('/^[\d\s\-\(\)\+]+$/', $phone_number)) {
        $response['message'] = 'Please enter a valid phone number';
        echo json_encode($response);
        exit();
    }
    
    if (strlen($phone_number) < 10 || strlen($phone_number) > 15) {
        $response['message'] = 'Phone number must be between 10 and 15 digits';
        echo json_encode($response);
        exit();
    }

    // Validate country
    if (!isset($_POST['country']) || empty(trim($_POST['country']))) {
        $response['message'] = 'Country is required';
        echo json_encode($response);
        exit();
    }
    $country = trim($_POST['country']);
    
    if (strlen($country) < 2 || strlen($country) > 30) {
        $response['message'] = 'Country name must be between 2 and 30 characters';
        echo json_encode($response);
        exit();
    }

    // Validate city
    if (!isset($_POST['city']) || empty(trim($_POST['city']))) {
        $response['message'] = 'City is required';
        echo json_encode($response);
        exit();
    }
    $city = trim($_POST['city']);
    
    if (strlen($city) < 2 || strlen($city) > 30) {
        $response['message'] = 'City name must be between 2 and 30 characters';
        echo json_encode($response);
        exit();
    }

    // Validate and sanitize role
    if (!isset($_POST['role']) || empty($_POST['role'])) {
        $response['message'] = 'Role is required';
        echo json_encode($response);
        exit();
    }
    $role = (int)$_POST['role'];
    
    // Validate role is either 0 (customer), 1 (admin), or 2 (restaurant owner)
    if (!in_array($role, [0, 1, 2])) {
        $response['message'] = 'Invalid role selected';
        echo json_encode($response);
        exit();
    }
    
    // Security: Prevent users from registering as admin (role = 1)
    // Remove this check if you want to allow admin registration
    if ($role == 1) {
        $response['message'] = 'Admin accounts cannot be created through registration';
        echo json_encode($response);
        exit();
    }

    // Check if email already exists (if function is available)
    if (function_exists('get_customer_by_email_ctr')) {
        $existing_customer = get_customer_by_email_ctr($email);
        if ($existing_customer) {
            $response['message'] = 'Email already exists. Please use a different email or try logging in.';
            error_log("Registration failed: Email already exists - " . $email);
            echo json_encode($response);
            exit();
        }
    }

    // Log registration attempt
    error_log("Registration attempt for email: " . $email);

    // Attempt to register customer
    $user_id = register_customer_ctr($name, $email, $password, $phone_number, $country, $city, $role);

    // Debug: Log the registration result
    error_log("Registration result: " . var_export($user_id, true));
    $response['debug']['user_id'] = $user_id;
    $response['debug']['user_id_type'] = gettype($user_id);

    // Check if registration was successful
    if ($user_id && is_numeric($user_id) && $user_id > 0) {
        // Registration successful
        $response['status'] = 'success';
        $response['message'] = 'Registration successful! You can now log in.';
        $response['user_id'] = (int)$user_id;
        
        // Log successful registration
        error_log("Registration successful for user ID: " . $user_id);
        
        // Remove debug info for successful registrations
        unset($response['debug']);
        
        // Optional: Auto-login after registration
        // Uncomment the lines below if you want users to be logged in automatically
        /*
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_country'] = $country;
        $_SESSION['user_city'] = $city;
        $_SESSION['user_phone'] = $phone_number;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        $response['redirect'] = '../dashboard.php';
        */
        
    } else {
        // Registration failed
        $response['status'] = 'error';
        $response['message'] = 'Failed to register. Please try again later.';
        
        // Log the error for debugging
        error_log('Registration failed for email: ' . $email . ' - User ID returned: ' . var_export($user_id, true));
    }

} catch (Exception $e) {
    // Catch any unexpected errors
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred. Please try again later.';
    $response['debug']['exception'] = $e->getMessage();
    $response['debug']['file'] = $e->getFile();
    $response['debug']['line'] = $e->getLine();
    
    // Log the error for debugging
    error_log('Registration exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
}

// Remove debug info in production
// Uncomment the line below when deploying to production
// unset($response['debug']);

// Send JSON response
echo json_encode($response);
exit();
?>