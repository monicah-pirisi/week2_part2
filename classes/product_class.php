<?php
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Product Model Class
 * Handles all product-related database operations
 */
class Product extends db_connection {
    
    /**
     * Fetch all products with category and brand names
     * @return array|false Array of products or false on failure
     */
    public function get_all_products() {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Fetch single product by ID
     * @param int $product_id
     * @return array|false Product data or false on failure
     */
    public function get_product_by_id($product_id) {
        $product_id = intval($product_id);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = $product_id";
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Add new product
     * @param array $data Product data
     * @return int|false Inserted product_id or false on failure
     */
    public function add_product($data) {
        $conn = $this->db_conn();
        
        $product_cat = intval($data['product_cat']);
        $product_brand = intval($data['product_brand']);
        $product_title = mysqli_real_escape_string($conn, trim($data['product_title']));
        $product_price = floatval($data['product_price']);
        $product_desc = isset($data['product_desc']) ? mysqli_real_escape_string($conn, trim($data['product_desc'])) : '';
        $product_keywords = isset($data['product_keywords']) ? mysqli_real_escape_string($conn, trim($data['product_keywords'])) : '';
        $product_image = isset($data['product_image']) ? mysqli_real_escape_string($conn, $data['product_image']) : '';
        
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                VALUES ($product_cat, $product_brand, '$product_title', $product_price, '$product_desc', '$product_image', '$product_keywords')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }
    
    /**
     * Update existing product
     * @param int $product_id
     * @param array $data Product data
     * @return bool True on success, false on failure
     */
    public function update_product($product_id, $data) {
        $conn = $this->db_conn();
        $product_id = intval($product_id);
        
        $product_cat = intval($data['product_cat']);
        $product_brand = intval($data['product_brand']);
        $product_title = mysqli_real_escape_string($conn, trim($data['product_title']));
        $product_price = floatval($data['product_price']);
        $product_desc = isset($data['product_desc']) ? mysqli_real_escape_string($conn, trim($data['product_desc'])) : '';
        $product_keywords = isset($data['product_keywords']) ? mysqli_real_escape_string($conn, trim($data['product_keywords'])) : '';
        
        $sql = "UPDATE products 
                SET product_cat = $product_cat, 
                    product_brand = $product_brand, 
                    product_title = '$product_title', 
                    product_price = $product_price, 
                    product_desc = '$product_desc', 
                    product_keywords = '$product_keywords' 
                WHERE product_id = $product_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Update product image
     * @param int $product_id
     * @param string $image_path
     * @return bool True on success, false on failure
     */
    public function update_product_image($product_id, $image_path) {
        $conn = $this->db_conn();
        $product_id = intval($product_id);
        $image_path = mysqli_real_escape_string($conn, $image_path);
        
        $sql = "UPDATE products SET product_image = '$image_path' WHERE product_id = $product_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Delete product
     * @param int $product_id
     * @return bool True on success, false on failure
     */
    public function delete_product($product_id) {
        $product_id = intval($product_id);
        $sql = "DELETE FROM products WHERE product_id = $product_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Get all categories
     * @return array|false Array of categories or false on failure
     */
    public function get_all_categories() {
        $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get all brands
     * @return array|false Array of brands or false on failure
     */
    public function get_all_brands() {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        return $this->db_fetch_all($sql);
    }
}
?>