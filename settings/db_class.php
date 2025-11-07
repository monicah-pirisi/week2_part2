<?php
/**
 * Database Connection Class
 * Handles all database operations and connections
 */

// Load configuration from .env file
require_once __DIR__ . '/config.php';

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
            // Create connection with port support
            $port = defined('DB_PORT') ? DB_PORT : 3306;
            $this->db = new mysqli(SERVER, USERNAME, PASSWD, DATABASE, $port);

            // Check connection
            if ($this->db->connect_error) {
                return false;
            }

            // Set charset to utf8mb4 for full Unicode support
            if (!$this->db->set_charset("utf8mb4")) {
                return false;
            }

            return true;
        } catch (Exception $e) {
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
                return false;
            }

            $result = $this->db->query($query);

            if ($result === false) {
                return false;
            }

            return true;
        } catch (Exception $e) {
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
                return false;
            }

            $result = $this->db->query($query);

            if ($result === false) {
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
                return false;
            }

            $result = $this->db->query($query);

            if ($result === false) {
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