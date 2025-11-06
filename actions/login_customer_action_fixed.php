<?php
/**
 * Login Customer Action - FIXED VERSION
 * Handles customer login with improved error handling
 */

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/login_errors.log');

// Set response header to JSON first (before any output)
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => 'An error occurred during login',
    'debug' => array() // Add debug info in development
);

try {
    // Check if the user is already logged in
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'You are already logged in';
        $response['redirect'] = '../dashboard.php';
        echo json_encode($response);
        exit();
    }

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
        error_log("Login error: Controller file not found at " . $controller_file);
        echo json_encode($response);
        exit();
    }

    // Include controller
    require_once $controller_file;

    // Check if the login function exists
    if (!function_exists('login_customer_ctr')) {
        $response['message'] = 'System error: Login function not available';
        error_log("Login error: login_customer_ctr function does not exist");
        echo json_encode($response);
        exit();
    }

    // Get and sanitize POST data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $response['debug']['email_received'] = !empty($email);
    $response['debug']['password_received'] = !empty($password);

    // Validate input - Check if fields are empty
    if (empty($email) || empty($password)) {
        $response['message'] = 'Please fill in all fields';
        echo json_encode($response);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address';
        echo json_encode($response);
        exit();
    }

    // Validate password length
    if (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters long';
        echo json_encode($response);
        exit();
    }

    // Log login attempt
    error_log("Login attempt for email: " . $email);

    // Attempt to login the customer
    $login_result = login_customer_ctr($email, $password);

    // Debug: Log the login result
    error_log("Login result: " . json_encode($login_result));
    $response['debug']['login_result_type'] = gettype($login_result);

    // Check if login was successful
    if ($login_result && isset($login_result['status']) && $login_result['status'] === 'success') {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);
        
        // Set session variables with proper validation
        $_SESSION['user_id'] = isset($login_result['user_id']) ? $login_result['user_id'] : null;
        $_SESSION['user_name'] = isset($login_result['user_name']) ? $login_result['user_name'] : 'User';
        $_SESSION['user_email'] = isset($login_result['user_email']) ? $login_result['user_email'] : $email;
        $_SESSION['user_role'] = isset($login_result['user_role']) ? (int)$login_result['user_role'] : 0;
        $_SESSION['user_country'] = isset($login_result['user_country']) ? $login_result['user_country'] : '';
        $_SESSION['user_city'] = isset($login_result['user_city']) ? $login_result['user_city'] : '';
        $_SESSION['user_phone'] = isset($login_result['user_phone']) ? $login_result['user_phone'] : '';
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Log successful login
        error_log("Login successful for user ID: " . $_SESSION['user_id']);
        
        // Prepare success response
        $response['status'] = 'success';
        $response['message'] = 'Login successful! Redirecting...';
        $response['redirect'] = '../dashboard.php';
        
        // Optional: Add user info to response (without sensitive data)
        $response['user'] = array(
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        );
        
        // Remove debug info for successful logins
        unset($response['debug']);
        
    } else {
        // Login failed
        $response['status'] = 'error';
        $response['message'] = isset($login_result['message']) ? $login_result['message'] : 'Invalid email or password';
        
        // Log failed login
        error_log("Login failed for email: " . $email . " - " . $response['message']);
    }

} catch (Exception $e) {
    // Log error for debugging
    error_log('Login exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred. Please try again later.';
    $response['debug']['exception'] = $e->getMessage();
    $response['debug']['file'] = $e->getFile();
    $response['debug']['line'] = $e->getLine();
}

// Remove debug info in production
// Uncomment the line below when deploying to production
// unset($response['debug']);

// Send JSON response
echo json_encode($response);
exit();
?>