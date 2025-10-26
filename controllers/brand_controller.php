<?php
require_once(__DIR__ . '/../classes/brand_class.php');

/**
 * Get all brands
 * @return array|false
 */
function get_all_brands_ctr() {
    $brand = new Brand();
    return $brand->get_all_brands();
}

/**
 * Get single brand by ID
 * @param int $brand_id
 * @return array|false
 */
function get_brand_by_id_ctr($brand_id) {
    $brand = new Brand();
    return $brand->get_brand_by_id($brand_id);
}

/**
 * Check if brand name exists
 * @param string $brand_name
 * @param int|null $exclude_id
 * @return bool
 */
function brand_name_exists_ctr($brand_name, $exclude_id = null) {
    $brand = new Brand();
    return $brand->brand_name_exists($brand_name, $exclude_id);
}

/**
 * Add new brand
 * @param string $brand_name
 * @return int|false
 */
function add_brand_ctr($brand_name) {
    $brand = new Brand();
    return $brand->add_brand($brand_name);
}

/**
 * Update brand
 * @param int $brand_id
 * @param string $brand_name
 * @return bool
 */
function update_brand_ctr($brand_id, $brand_name) {
    $brand = new Brand();
    return $brand->update_brand($brand_id, $brand_name);
}

/**
 * Delete brand
 * @param int $brand_id
 * @return bool
 */
function delete_brand_ctr($brand_id) {
    $brand = new Brand();
    return $brand->delete_brand($brand_id);
}

/**
 * Check if brand is in use by products
 * @param int $brand_id
 * @return bool
 */
function brand_in_use_ctr($brand_id) {
    $brand = new Brand();
    return $brand->brand_in_use($brand_id);
}
?>