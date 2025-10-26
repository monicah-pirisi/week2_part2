<?php
require_once(__DIR__ . '/../classes/product_class.php');

/**
 * Get all products
 * @return array|false
 */
function get_all_products_ctr() {
    $product = new Product();
    return $product->get_all_products();
}

/**
 * Get single product by ID
 * @param int $product_id
 * @return array|false
 */
function get_product_by_id_ctr($product_id) {
    $product = new Product();
    return $product->get_product_by_id($product_id);
}

/**
 * Add new product
 * @param array $data
 * @return int|false
 */
function add_product_ctr($data) {
    $product = new Product();
    return $product->add_product($data);
}

/**
 * Update product
 * @param int $product_id
 * @param array $data
 * @return bool
 */
function update_product_ctr($product_id, $data) {
    $product = new Product();
    return $product->update_product($product_id, $data);
}

/**
 * Update product image
 * @param int $product_id
 * @param string $image_path
 * @return bool
 */
function update_product_image_ctr($product_id, $image_path) {
    $product = new Product();
    return $product->update_product_image($product_id, $image_path);
}

/**
 * Delete product
 * @param int $product_id
 * @return bool
 */
function delete_product_ctr($product_id) {
    $product = new Product();
    return $product->delete_product($product_id);
}

/**
 * Get all categories
 * @return array|false
 */
function get_all_categories_ctr() {
    $product = new Product();
    return $product->get_all_categories();
}

?>