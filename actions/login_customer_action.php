<?php
/**
 * Login Customer Action - FINAL FIXED VERSION
 * Handles customer login with full error handling and valid JSON response
 */

// ====== Error Logging Setup ======

// Ensure logs folder exists
$logDir = __DIR__ . '/../logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0775, true);
}

// Set header early for JSON
    header('Content-Type: application/json; charset=UTF-8');


// ====== Start Session ======
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ====== Initialize Response ======
$response = [
    'status' => 'error',
    'message' => 'An error occurred during login',
    'debug' => []
];

try {
    // Already logged in check
    if (!empty($_SESSION['user_id'])) {
        $response = [
            'status' => 'error',
            'message' => 'You are already logged in',
            'redirect' => '../dashboard.php'
        ];
        echo json_encode($response);
        exit();
    }

    // Verify method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Invalid request method. Please use POST.';
        $response['debug']['method'] = $_SERVER['REQUEST_METHOD'];
        echo json_encode($response);
        exit();
    }

    // Check controller file
    $controller_file = __DIR__ . '/../controllers/customer_controller.php';
    if (!file_exists($controller_file)) {
        $response['message'] = 'System error: Controller file not found.';
        $response['debug']['missing_file'] = $controller_file;
        echo json_encode($response);
        exit();
    }

    require_once $controller_file;

    // Check login function
    if (!function_exists('login_customer_ctr')) {
        $response['message'] = 'System error: Login function unavailable.';
        echo json_encode($response);
        exit();
    }

    // ====== Sanitize Input ======
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = $_POST['password'] ?? '';

    $response['debug']['email_received'] = (bool)$email;
    $response['debug']['password_received'] = (bool)$password;

    // Validate inputs
    if (empty($email) || empty($password)) {
        $response['message'] = 'Please fill in all fields.';
        echo json_encode($response);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
        echo json_encode($response);
        exit();
    }

    if (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters.';
        echo json_encode($response);
        exit();
    }

    // ====== Attempt Login ======
    $login_result = login_customer_ctr($email, $password);

    $response['debug']['login_result_type'] = gettype($login_result);

    // ====== Handle Result ======
    if (is_array($login_result) && isset($login_result['status']) && $login_result['status'] === 'success') {
        session_regenerate_id(true);

        $_SESSION['user_id']      = $login_result['user_id'] ?? null;
        $_SESSION['user_name']    = $login_result['user_name'] ?? 'User';
        $_SESSION['user_email']   = $login_result['user_email'] ?? $email;
        $_SESSION['user_role']    = (int)($login_result['user_role'] ?? 0);
        $_SESSION['user_country'] = $login_result['user_country'] ?? '';
        $_SESSION['user_city']    = $login_result['user_city'] ?? '';
        $_SESSION['user_phone']   = $login_result['user_phone'] ?? '';
        $_SESSION['logged_in']    = true;
        $_SESSION['login_time']   = time();


        $response = [
            'status' => 'success',
            'message' => 'Login successful! Redirecting...',
            'user' => [
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role']
            ]
        ];

    } else {
        $response['status'] = 'error';
        $response['message'] = $login_result['message'] ?? 'Invalid email or password.';
    }

} catch (Throwable $e) {
    // Handle any fatal or uncaught errors safely

    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred. Please try again later.';
    $response['debug']['exception'] = $e->getMessage();
    $response['debug']['file'] = $e->getFile();
    $response['debug']['line'] = $e->getLine();
}

// ====== Final Output ======
// Comment out next line in production
// unset($response['debug']);

echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit();
?>
