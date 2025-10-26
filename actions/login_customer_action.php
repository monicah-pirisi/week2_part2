<?php
// Set response header to JSON
header('Content-Type: application/json');

// Disable error display in production (errors should be logged, not displayed)
// Comment these out during development if you need to see errors
error_reporting(0);
ini_set('display_errors', 0);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => 'An error occurred'
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
        $response['message'] = 'Invalid request method';
        echo json_encode($response);
        exit();
    }

    // Include controller
    require_once __DIR__ . '/../controllers/customer_controller.php';

    // Get and sanitize POST data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

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

    // Attempt to login the customer
    $login_result = login_customer_ctr($email, $password);

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
        
        // Prepare success response
        $response['status'] = 'success';
        $response['message'] = 'Login successful! Redirecting...';
        $response['redirect'] = '../dashboard.php';
        
        // Optional: Add user info to response (without sensitive data)
        $response['user'] = array(
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        );
        
    } else {
        // Login failed
        $response['status'] = 'error';
        $response['message'] = isset($login_result['message']) ? $login_result['message'] : 'Invalid email or password';
    }

} catch (Exception $e) {
    // Log error for debugging (in production, log to file instead)
    error_log('Login error: ' . $e->getMessage());
    
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred. Please try again later.';
}

// Send JSON response
echo json_encode($response);
exit();
?>