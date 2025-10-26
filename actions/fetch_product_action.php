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

try {
    // Check if fetching single product or all products
    if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
        // Fetch single product
        $product_id = intval($_GET['product_id']);
        $product = get_product_by_id_ctr($product_id);
        
        if ($product) {
            echo json_encode([
                'success' => true,
                'message' => 'Product fetched successfully',
                'data' => $product
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
    } else {
        // Fetch all products
        $products = get_all_products_ctr();
        
        if ($products !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Products fetched successfully',
                'data' => $products
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch products'
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>