<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../settings/core.php');
require_once(__DIR__ . '/../controllers/brand_controller.php');

// Check if user is logged in and is admin
if (!isAdmin()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Admin privileges required.'
    ]);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

try {
    // Validate input
    if (!isset($_POST['brand_id']) || empty($_POST['brand_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand ID is required'
        ]);
        exit();
    }
    
    $brand_id = intval($_POST['brand_id']);
    
    // Check if brand exists
    $brand = get_brand_by_id_ctr($brand_id);
    if (!$brand) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand not found'
        ]);
        exit();
    }
    
    // Check if brand is in use by any products
    if (brand_in_use_ctr($brand_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete brand. It is currently being used by one or more products.'
        ]);
        exit();
    }
    
    // Delete brand
    $result = delete_brand_ctr($brand_id);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Brand deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete brand. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>