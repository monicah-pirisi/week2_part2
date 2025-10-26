<?php
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Brand Model Class
 * Handles all brand-related database operations
 */
class Brand extends db_connection {
    
    /**
     * Fetch all brands
     * @return array|false Array of brands or false on failure
     */
    public function get_all_brands() {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Fetch single brand by ID
     * @param int $brand_id
     * @return array|false Brand data or false on failure
     */
    public function get_brand_by_id($brand_id) {
        $sql = "SELECT * FROM brands WHERE brand_id = " . intval($brand_id);
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Check if brand name exists (for duplicate validation)
     * @param string $brand_name
     * @param int|null $exclude_id Brand ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function brand_name_exists($brand_name, $exclude_id = null) {
        $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
        
        if ($exclude_id !== null) {
            $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND brand_id != " . intval($exclude_id);
        } else {
            $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result !== false && $result !== null;
    }
    
    /**
     * Add new brand
     * @param string $brand_name
     * @return int|false Inserted brand_id or false on failure
     */
    public function add_brand($brand_name) {
        $brand_name = mysqli_real_escape_string($this->db_conn(), trim($brand_name));
        $sql = "INSERT INTO brands (brand_name) VALUES ('$brand_name')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }
    
    /**
     * Update existing brand
     * @param int $brand_id
     * @param string $brand_name
     * @return bool True on success, false on failure
     */
    public function update_brand($brand_id, $brand_name) {
        $brand_name = mysqli_real_escape_string($this->db_conn(), trim($brand_name));
        $brand_id = intval($brand_id);
        $sql = "UPDATE brands SET brand_name = '$brand_name' WHERE brand_id = $brand_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Delete brand
     * @param int $brand_id
     * @return bool True on success, false on failure
     */
    public function delete_brand($brand_id) {
        $brand_id = intval($brand_id);
        $sql = "DELETE FROM brands WHERE brand_id = $brand_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Check if brand is used by any products
     * @param int $brand_id
     * @return bool True if brand is in use, false otherwise
     */
    public function brand_in_use($brand_id) {
        $brand_id = intval($brand_id);
        $sql = "SELECT product_id FROM products WHERE product_brand = $brand_id LIMIT 1";
        
        $result = $this->db_fetch_one($sql);
        return $result !== false && $result !== null;
    }
}
?>