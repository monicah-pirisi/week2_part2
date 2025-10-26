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
    // Fetch all categories
    $categories = get_all_categories_ctr();
    
    if ($categories !== false) {
        echo json_encode([
            'success' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch categories'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>