<?php
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Product Model Class
 * Handles all product-related database operations using prepared statements
 */
class Product extends db_connection {

    /**
     * Fetch all products with category and brand names
     * @return array|false Array of products or false on failure
     */
    public function get_all_products() {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $sql = "SELECT p.*, c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    ORDER BY p.product_id DESC";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Get all products error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch single product by ID
     * @param int $product_id
     * @return array|false Product data or false on failure
     */
    public function get_product_by_id($product_id) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $sql = "SELECT p.*, c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_id = ?";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $result ? $result : false;
        } catch (Exception $e) {
            error_log("Get product by ID error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add new product
     * @param array $data Product data
     * @return int|false Inserted product_id or false on failure
     */
    public function add_product($data) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $product_cat = intval($data['product_cat']);
            $product_brand = intval($data['product_brand']);
            $product_title = trim($data['product_title']);
            $product_price = floatval($data['product_price']);
            $product_desc = isset($data['product_desc']) ? trim($data['product_desc']) : '';
            $product_keywords = isset($data['product_keywords']) ? trim($data['product_keywords']) : '';
            $product_image = isset($data['product_image']) ? $data['product_image'] : '';

            $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("iisdsss", $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);

            if ($stmt->execute()) {
                $insert_id = $this->db->insert_id;
                $stmt->close();
                return $insert_id;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Add product error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing product
     * @param int $product_id
     * @param array $data Product data
     * @return bool True on success, false on failure
     */
    public function update_product($product_id, $data) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $product_cat = intval($data['product_cat']);
            $product_brand = intval($data['product_brand']);
            $product_title = trim($data['product_title']);
            $product_price = floatval($data['product_price']);
            $product_desc = isset($data['product_desc']) ? trim($data['product_desc']) : '';
            $product_keywords = isset($data['product_keywords']) ? trim($data['product_keywords']) : '';

            $sql = "UPDATE products
                    SET product_cat = ?,
                        product_brand = ?,
                        product_title = ?,
                        product_price = ?,
                        product_desc = ?,
                        product_keywords = ?
                    WHERE product_id = ?";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("iisdssi", $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_keywords, $product_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update product image
     * @param int $product_id
     * @param string $image_path
     * @return bool True on success, false on failure
     */
    public function update_product_image($product_id, $image_path) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("UPDATE products SET product_image = ? WHERE product_id = ?");
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("si", $image_path, $product_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Update product image error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete product
     * @param int $product_id
     * @return bool True on success, false on failure
     */
    public function delete_product($product_id) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $product_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all categories
     * @return array|false Array of categories or false on failure
     */
    public function get_all_categories() {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY cat_name ASC");
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Get all categories error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all brands
     * @return array|false Array of brands or false on failure
     */
    public function get_all_brands() {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("SELECT * FROM brands ORDER BY brand_name ASC");
            if (!$stmt) {
                error_log("Product prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Get all brands error: " . $e->getMessage());
            return false;
        }
    }
}
?>
