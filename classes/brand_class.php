<?php
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Brand Model Class
 * Handles all brand-related database operations using prepared statements
 */
class Brand extends db_connection {

    /**
     * Fetch all brands
     * @return array|false Array of brands or false on failure
     */
    public function get_all_brands() {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("SELECT * FROM brands ORDER BY brand_name ASC");
            if (!$stmt) {
                error_log("Brand prepare failed: " . $this->db->error);
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

    /**
     * Fetch single brand by ID
     * @param int $brand_id
     * @return array|false Brand data or false on failure
     */
    public function get_brand_by_id($brand_id) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
            if (!$stmt) {
                error_log("Brand prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $brand_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $result ? $result : false;
        } catch (Exception $e) {
            error_log("Get brand by ID error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if brand name exists (for duplicate validation)
     * @param string $brand_name
     * @param int|null $exclude_id Brand ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function brand_name_exists($brand_name, $exclude_id = null) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            if ($exclude_id !== null) {
                $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND brand_id != ?");
                if (!$stmt) {
                    error_log("Brand prepare failed: " . $this->db->error);
                    return false;
                }
                $stmt->bind_param("si", $brand_name, $exclude_id);
            } else {
                $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ?");
                if (!$stmt) {
                    error_log("Brand prepare failed: " . $this->db->error);
                    return false;
                }
                $stmt->bind_param("s", $brand_name);
            }

            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $result !== false && $result !== null;
        } catch (Exception $e) {
            error_log("Brand name exists error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add new brand
     * @param string $brand_name
     * @return int|false Inserted brand_id or false on failure
     */
    public function add_brand($brand_name) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $brand_name = trim($brand_name);
            $stmt = $this->db->prepare("INSERT INTO brands (brand_name) VALUES (?)");
            if (!$stmt) {
                error_log("Brand prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("s", $brand_name);

            if ($stmt->execute()) {
                $insert_id = $this->db->insert_id;
                $stmt->close();
                return $insert_id;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Add brand error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing brand
     * @param int $brand_id
     * @param string $brand_name
     * @return bool True on success, false on failure
     */
    public function update_brand($brand_id, $brand_name) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $brand_name = trim($brand_name);
            $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ?");
            if (!$stmt) {
                error_log("Brand prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("si", $brand_name, $brand_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Update brand error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete brand
     * @param int $brand_id
     * @return bool True on success, false on failure
     */
    public function delete_brand($brand_id) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ?");
            if (!$stmt) {
                error_log("Brand prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $brand_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Delete brand error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if brand is used by any products
     * @param int $brand_id
     * @return bool True if brand is in use, false otherwise
     */
    public function brand_in_use($brand_id) {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            $stmt = $this->db->prepare("SELECT product_id FROM products WHERE product_brand = ? LIMIT 1");
            if (!$stmt) {
                error_log("Brand prepare failed: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $brand_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $result !== false && $result !== null;
        } catch (Exception $e) {
            error_log("Brand in use error: " . $e->getMessage());
            return false;
        }
    }
}
?>
