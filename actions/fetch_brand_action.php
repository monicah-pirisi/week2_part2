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

try {
    // Check if fetching single brand or all brands
    if (isset($_GET['brand_id']) && !empty($_GET['brand_id'])) {
        // Fetch single brand
        $brand_id = intval($_GET['brand_id']);
        $brand = get_brand_by_id_ctr($brand_id);
        
        if ($brand) {
            echo json_encode([
                'success' => true,
                'message' => 'Brand fetched successfully',
                'data' => $brand
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Brand not found'
            ]);
        }
    } else {
        // Fetch all brands
        $brands = get_all_brands_ctr();
        
        if ($brands !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Brands fetched successfully',
                'data' => $brands
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch brands'
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