<?php
require_once(__DIR__ . '/../classes/brand_class.php');

/**
 * Get all brands
 * @return array
 */
function get_all_brands_ctr() {
    try {
        $brand = new Brand();
        $result = $brand->get_all_brands();
        return [
            'success' => true,
            'brands' => $result !== false ? $result : []
        ];
    } catch (Exception $e) {
        error_log("Get all brands controller error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to retrieve brands'
        ];
    }
}

/**
 * Get single brand by ID
 * @param int $brand_id
 * @return array
 */
function get_brand_by_id_ctr($brand_id) {
    // Validate input
    if (!is_numeric($brand_id) || $brand_id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid brand ID'
        ];
    }

    try {
        $brand = new Brand();
        $result = $brand->get_brand_by_id($brand_id);

        if ($result) {
            return [
                'success' => true,
                'brand' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Brand not found'
            ];
        }
    } catch (Exception $e) {
        error_log("Get brand by ID controller error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to retrieve brand'
        ];
    }
}

/**
 * Check if brand name exists
 * @param string $brand_name
 * @param int|null $exclude_id
 * @return bool
 */
function brand_name_exists_ctr($brand_name, $exclude_id = null) {
    if (empty($brand_name)) {
        return false;
    }

    $brand = new Brand();
    return $brand->brand_name_exists($brand_name, $exclude_id);
}

/**
 * Validate brand data
 * @param array $data
 * @return array
 */
function validate_brand_data_ctr($data) {
    $errors = [];

    // Validate brand name
    if (empty($data['brand_name'])) {
        $errors[] = 'Brand name is required';
    } elseif (strlen(trim($data['brand_name'])) < 2) {
        $errors[] = 'Brand name must be at least 2 characters long';
    } elseif (strlen(trim($data['brand_name'])) > 100) {
        $errors[] = 'Brand name must not exceed 100 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9\s\-&.]+$/', $data['brand_name'])) {
        $errors[] = 'Brand name contains invalid characters';
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Add new brand
 * @param string $brand_name
 * @return array
 */
function add_brand_ctr($brand_name) {
    // Validate input
    $validation = validate_brand_data_ctr(['brand_name' => $brand_name]);
    if (!$validation['valid']) {
        return [
            'success' => false,
            'message' => implode(', ', $validation['errors'])
        ];
    }

    // Check for duplicates
    if (brand_name_exists_ctr($brand_name)) {
        return [
            'success' => false,
            'message' => 'Brand name already exists'
        ];
    }

    try {
        $brand = new Brand();
        $brand_id = $brand->add_brand($brand_name);

        if ($brand_id) {
            return [
                'success' => true,
                'message' => 'Brand added successfully',
                'brand_id' => $brand_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to add brand'
            ];
        }
    } catch (Exception $e) {
        error_log("Add brand controller error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred while adding the brand'
        ];
    }
}

/**
 * Update brand
 * @param int $brand_id
 * @param string $brand_name
 * @return array
 */
function update_brand_ctr($brand_id, $brand_name) {
    // Validate brand ID
    if (!is_numeric($brand_id) || $brand_id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid brand ID'
        ];
    }

    // Validate brand name
    $validation = validate_brand_data_ctr(['brand_name' => $brand_name]);
    if (!$validation['valid']) {
        return [
            'success' => false,
            'message' => implode(', ', $validation['errors'])
        ];
    }

    // Check for duplicates (excluding current brand)
    if (brand_name_exists_ctr($brand_name, $brand_id)) {
        return [
            'success' => false,
            'message' => 'Brand name already exists'
        ];
    }

    try {
        $brand = new Brand();
        $result = $brand->update_brand($brand_id, $brand_name);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Brand updated successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update brand'
            ];
        }
    } catch (Exception $e) {
        error_log("Update brand controller error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred while updating the brand'
        ];
    }
}

/**
 * Delete brand
 * @param int $brand_id
 * @return array
 */
function delete_brand_ctr($brand_id) {
    // Validate brand ID
    if (!is_numeric($brand_id) || $brand_id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid brand ID'
        ];
    }

    // Check if brand is in use
    if (brand_in_use_ctr($brand_id)) {
        return [
            'success' => false,
            'message' => 'Cannot delete brand. It is being used by one or more products'
        ];
    }

    try {
        $brand = new Brand();
        $result = $brand->delete_brand($brand_id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Brand deleted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete brand'
            ];
        }
    } catch (Exception $e) {
        error_log("Delete brand controller error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred while deleting the brand'
        ];
    }
}

/**
 * Check if brand is in use by products
 * @param int $brand_id
 * @return bool
 */
function brand_in_use_ctr($brand_id) {
    if (!is_numeric($brand_id) || $brand_id <= 0) {
        return false;
    }

    $brand = new Brand();
    return $brand->brand_in_use($brand_id);
}
?>
