<?php
/**
 * Database Connection Class
 * Handles all database operations and connections
 */

// Database Configuration
if (!defined("SERVER")) {
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "monicah.lekupe");
}

if (!defined("PASSWD")) {
    define("PASSWD", "Amelia@2026");
}

if (!defined("DATABASE")) {
    define("DATABASE", "ecommerce_2025A_monicah_lekupe");
}

class db_connection
{
    public $db = null;
    private $results = null;

    /**
     * Database connection
     * @return bool - True on success, False on failure
     */
    function db_connect()
    {
        try {
            // Create connection
            $this->db = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);

            // Check connection
            if ($this->db->connect_error) {
                error_log("Database Connection Failed: " . $this->db->connect_error);
                return false;
            }

            // Set charset to utf8mb4 for full Unicode support
            if (!$this->db->set_charset("utf8mb4")) {
                error_log("Error setting charset: " . $this->db->error);
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Database Connection Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get database connection
     * @return mysqli|null
     */
    function db_conn()
    {
        if ($this->db === null) {
            $this->db_connect();
        }
        return $this->db;
    }

    /**
     * Execute a write query (INSERT, UPDATE, DELETE)
     * @param string $query - SQL query to execute
     * @return bool - True on success, False on failure
     */
    function db_write_query($query)
    {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            if ($this->db === null || $this->db->connect_error) {
                error_log("Database not connected for write query");
                return false;
            }

            $result = $this->db->query($query);

            if ($result === false) {
                error_log("Query Error: " . $this->db->error . " | Query: " . $query);
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Write Query Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all rows from a SELECT query
     * @param string $query - SQL query to execute
     * @return array|false - Array of results or false on failure
     */
    function db_fetch_all($query)
    {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            if ($this->db === null || $this->db->connect_error) {
                error_log("Database not connected for fetch all query");
                return false;
            }

            $result = $this->db->query($query);

            if ($result === false) {
                error_log("Query Error: " . $this->db->error . " | Query: " . $query);
                return false;
            }

            if ($result->num_rows > 0) {
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
                return $data;
            }

            $result->free();
            return [];
        } catch (Exception $e) {
            error_log("Fetch All Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch one row from a SELECT query
     * @param string $query - SQL query to execute
     * @return array|false - Associative array or false on failure
     */
    function db_fetch_one($query)
    {
        try {
            if ($this->db === null) {
                $this->db_connect();
            }

            if ($this->db === null || $this->db->connect_error) {
                error_log("Database not connected for fetch one query");
                return false;
            }

            $result = $this->db->query($query);

            if ($result === false) {
                error_log("Query Error: " . $this->db->error . " | Query: " . $query);
                return false;
            }

            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                $result->free();
                return $data;
            }

            $result->free();
            return false;
        } catch (Exception $e) {
            error_log("Fetch One Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the last inserted ID
     * @return int - Last inserted ID
     */
    function last_insert_id()
    {
        if ($this->db === null) {
            return 0;
        }
        return $this->db->insert_id;
    }

    /**
     * Get the number of affected rows
     * @return int - Number of affected rows
     */
    function affected_rows()
    {
        if ($this->db === null) {
            return 0;
        }
        return $this->db->affected_rows;
    }

    /**
     * Close database connection
     */
    function db_close()
    {
        if ($this->db !== null) {
            $this->db->close();
            $this->db = null;
        }
    }

    /**
     * Destructor - Close connection when object is destroyed
     */
    function __destruct()
    {
        $this->db_close();
    }
}
?>