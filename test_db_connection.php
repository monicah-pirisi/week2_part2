<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

try {
    // Include database credentials
    require_once 'settings/db_cred.php';
    
    echo "<p>Server: " . SERVER . "</p>";
    echo "<p>Username: " . USERNAME . "</p>";
    echo "<p>Password: " . (PASSWD ? '[SET]' : '[EMPTY]') . "</p>";
    echo "<p>Database: " . DATABASE . "</p>";
    
    // Test connection
    $connection = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    
    if ($connection) {
        echo "<p style='color: green;'>✓ Database connection successful!</p>";
        
        // Test query
        $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM customer");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p>Number of customers: " . $row['count'] . "</p>";
        }
        
        mysqli_close($connection);
    } else {
        echo "<p style='color: red;'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}
?>
