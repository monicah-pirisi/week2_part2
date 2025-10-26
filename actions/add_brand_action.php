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
    if (!isset($_POST['brand_name']) || empty(trim($_POST['brand_name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name is required'
        ]);
        exit();
    }
    
    $brand_name = trim($_POST['brand_name']);
    
    // Validate brand name length
    if (strlen($brand_name) < 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name must be at least 2 characters long'
        ]);
        exit();
    }
    
    if (strlen($brand_name) > 100) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name must not exceed 100 characters'
        ]);
        exit();
    }
    
    // Check for duplicate brand name
    if (brand_name_exists_ctr($brand_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name already exists. Please use a different name.'
        ]);
        exit();
    }
    
    // Add brand
    $brand_id = add_brand_ctr($brand_name);
    
    if ($brand_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Brand added successfully',
            'data' => [
                'brand_id' => $brand_id,
                'brand_name' => $brand_name
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add brand. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>