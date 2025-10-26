<?php
// Debug version of login action - remove after fixing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response header to JSON
header('Content-Type: application/json');

echo "Debug: Starting login process...\n";

try {
    // Check if core.php exists and can be included
    $core_path = __DIR__ . '/../settings/core.php';
    echo "Debug: Core path: " . $core_path . "\n";
    
    if (!file_exists($core_path)) {
        throw new Exception("Core file not found at: " . $core_path);
    }
    
    echo "Debug: Including core.php...\n";
    require_once $core_path;
    echo "Debug: Core.php included successfully\n";
    
    // Check if controller exists
    $controller_path = __DIR__ . '/../controllers/customer_controller.php';
    echo "Debug: Controller path: " . $controller_path . "\n";
    
    if (!file_exists($controller_path)) {
        throw new Exception("Controller file not found at: " . $controller_path);
    }
    
    echo "Debug: Including controller...\n";
    require_once $controller_path;
    echo "Debug: Controller included successfully\n";
    
    // Check if user is logged in and is admin
    echo "Debug: Checking authentication...\n";
    if (!function_exists('isAdmin')) {
        throw new Exception("isAdmin function not available");
    }
    
    if (!isAdmin()) {
        echo "Debug: User not admin, checking if logged in...\n";
        if (!function_exists('isLoggedIn')) {
            throw new Exception("isLoggedIn function not available");
        }
        if (!isLoggedIn()) {
            echo "Debug: User not logged in\n";
        } else {
            echo "Debug: User is logged in but not admin\n";
        }
    } else {
        echo "Debug: User is admin\n";
    }
    
    // Check if request method is POST
    echo "Debug: Request method: " . $_SERVER['REQUEST_METHOD'] . "\n";
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Debug: Not a POST request\n";
    }
    
    // Check POST data
    echo "Debug: POST data received:\n";
    print_r($_POST);
    
    // Test database connection
    echo "Debug: Testing database connection...\n";
    $db_path = __DIR__ . '/../settings/db_class.php';
    if (!file_exists($db_path)) {
        throw new Exception("Database class file not found at: " . $db_path);
    }
    
    require_once $db_path;
    echo "Debug: Database class included\n";
    
    $db = new db_connection();
    if ($db->db_connect()) {
        echo "Debug: Database connection successful\n";
    } else {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }
    
    echo "Debug: All checks passed successfully\n";
    
    // Return success for debugging
    echo json_encode([
        'status' => 'debug',
        'message' => 'Debug completed successfully - all components working',
        'debug_info' => [
            'core_file_exists' => file_exists($core_path),
            'controller_file_exists' => file_exists($controller_path),
            'db_class_exists' => file_exists($db_path),
            'isLoggedIn_function' => function_exists('isLoggedIn'),
            'isAdmin_function' => function_exists('isAdmin'),
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST
        ]
    ]);
    
} catch (Exception $e) {
    echo "Debug Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Debug error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
