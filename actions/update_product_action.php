<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../settings/core.php');
require_once(__DIR__ . '/../controllers/product_controller.php');

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
    // Validate product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Product ID is required'
        ]);
        exit();
    }
    
    $product_id = intval($_POST['product_id']);
    
    // Check if product exists
    $existing_product = get_product_by_id_ctr($product_id);
    if (!$existing_product) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    // Validate required inputs
    $required_fields = ['product_title', 'product_cat', 'product_brand', 'product_price'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || (is_string($_POST[$field]) && empty(trim($_POST[$field])))) {
            echo json_encode([
                'success' => false,
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
            ]);
            exit();
        }
    }
    
    // Validate product title
    $product_title = trim($_POST['product_title']);
    if (strlen($product_title) < 3) {
        echo json_encode([
            'success' => false,
            'message' => 'Product title must be at least 3 characters long'
        ]);
        exit();
    }
    
    if (strlen($product_title) > 200) {
        echo json_encode([
            'success' => false,
            'message' => 'Product title must not exceed 200 characters'
        ]);
        exit();
    }
    
    // Validate price
    $product_price = floatval($_POST['product_price']);
    if ($product_price < 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Product price must be a positive number'
        ]);
        exit();
    }
    
    // Validate description length if provided
    if (isset($_POST['product_desc']) && strlen($_POST['product_desc']) > 500) {
        echo json_encode([
            'success' => false,
            'message' => 'Product description must not exceed 500 characters'
        ]);
        exit();
    }
    
    // Validate keywords length if provided
    if (isset($_POST['product_keywords']) && strlen($_POST['product_keywords']) > 100) {
        echo json_encode([
            'success' => false,
            'message' => 'Product keywords must not exceed 100 characters'
        ]);
        exit();
    }
    
    // Prepare product data
    $product_data = [
        'product_cat' => intval($_POST['product_cat']),
        'product_brand' => intval($_POST['product_brand']),
        'product_title' => $product_title,
        'product_price' => $product_price,
        'product_desc' => isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '',
        'product_keywords' => isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : ''
    ];
    
    // Update product
    $result = update_product_ctr($product_id, $product_data);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => [
                'product_id' => $product_id
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update product. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>