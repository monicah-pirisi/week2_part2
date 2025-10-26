<?php
// Set response header to JSON first (before any output)
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
    'message' => 'An error occurred during registration'
);

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Invalid request method';
        echo json_encode($response);
        exit();
    }

    // Include controller
    require_once __DIR__ . '/../controllers/customer_controller.php';

    // Sanitize and validate name
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

    // Sanitize and validate email
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
    // For security, you might want to restrict admin registration
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

    // Check if email already exists
    if (function_exists('get_customer_by_email_ctr')) {
        $existing_customer = get_customer_by_email_ctr($email);
        if ($existing_customer) {
            $response['message'] = 'Email already exists. Please use a different email or try logging in.';
            echo json_encode($response);
            exit();
        }
    }

    // Attempt to register customer
    $user_id = register_customer_ctr($name, $email, $password, $phone_number, $country, $city, $role);

    // Check if registration was successful
    if ($user_id && is_numeric($user_id) && $user_id > 0) {
        // Registration successful
        $response['status'] = 'success';
        $response['message'] = 'Registration successful! You can now log in.';
        $response['user_id'] = (int)$user_id;
        
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
        
        // Log the error for debugging (don't expose to user)
        error_log('Registration failed for email: ' . $email . ' - User ID: ' . var_export($user_id, true));
    }

} catch (Exception $e) {
    // Catch any unexpected errors
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred. Please try again later.';
    
    // Log the error for debugging (don't expose detailed error to user)
    error_log('Registration exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
}

// Send JSON response
echo json_encode($response);
exit();
?>