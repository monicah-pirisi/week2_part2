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
    $product = get_product_by_id_ctr($product_id);
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode([
            'success' => false,
            'message' => 'No image file uploaded'
        ]);
        exit();
    }
    
    $file = $_FILES['product_image'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'File upload error: ' . $file['error']
        ]);
        exit();
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        echo json_encode([
            'success' => false,
            'message' => 'File size must not exceed 5MB'
        ]);
        exit();
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'
        ]);
        exit();
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($extension, $allowed_extensions)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file extension'
        ]);
        exit();
    }
    
    // Create user-specific upload directory
    $user_id = $_SESSION['user_id'];
    $upload_base = __DIR__ . '/../uploads/';
    $user_dir = $upload_base . 'u' . $user_id . '/';
    $product_dir = $user_dir . 'p' . $product_id . '/';
    
    // Create directories if they don't exist
    if (!is_dir($upload_base)) {
        mkdir($upload_base, 0755, true);
    }
    if (!is_dir($user_dir)) {
        mkdir($user_dir, 0755, true);
    }
    if (!is_dir($product_dir)) {
        mkdir($product_dir, 0755, true);
    }
    
    // Delete old product images from this directory
    $old_files = glob($product_dir . '*');
    foreach ($old_files as $old_file) {
        if (is_file($old_file)) {
            unlink($old_file);
        }
    }
    
    // Generate unique filename
    $filename = 'product_' . $product_id . '_' . time() . '.' . $extension;
    $destination = $product_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Store relative path in database (relative to project root)
        $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
        
        // Update product image in database
        $result = update_product_image_ctr($product_id, $relative_path);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'product_id' => $product_id,
                    'image_path' => $relative_path,
                    'image_url' => '../' . $relative_path
                ]
            ]);
        } else {
            // Delete uploaded file if database update fails
            unlink($destination);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update product image in database'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>