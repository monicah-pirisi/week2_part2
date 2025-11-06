<?php
/**
 * Database Connection Test Script
 * Use this to verify your database connection is working
 */

// Enable error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";
echo "<hr>";

// Test 1: Check if files exist
echo "<h2>Test 1: Checking Files</h2>";
$files_to_check = [
    '../settings/db_class.php',
    '../settings/core.php',
    '../classes/customer_class.php',
    '../controllers/customer_controller.php'
];

foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✓ <span style='color: green;'>File exists: $file</span><br>";
    } else {
        echo "✗ <span style='color: red;'>File NOT found: $file</span><br>";
    }
}

echo "<hr>";

// Test 2: Include required files
echo "<h2>Test 2: Including Required Files</h2>";
try {
    require_once(__DIR__ . '/../settings/db_class.php');
    echo "✓ <span style='color: green;'>db_class.php included successfully</span><br>";
} catch (Exception $e) {
    echo "✗ <span style='color: red;'>Error including db_class.php: " . $e->getMessage() . "</span><br>";
    exit();
}

echo "<hr>";

// Test 3: Check database constants
echo "<h2>Test 3: Database Configuration</h2>";
echo "Server: <strong>" . SERVER . "</strong><br>";
echo "Username: <strong>" . USERNAME . "</strong><br>";
echo "Password: <strong>" . (PASSWD ? str_repeat('*', strlen(PASSWD)) : 'NOT SET') . "</strong><br>";
echo "Database: <strong>" . DATABASE . "</strong><br>";

echo "<hr>";

// Test 4: Test database connection
echo "<h2>Test 4: Testing Database Connection</h2>";
try {
    $db = new db_connection();
    $connected = $db->db_connect();
    
    if ($connected) {
        echo "✓ <span style='color: green;'>Database connection successful!</span><br>";
        
        // Test 5: Check if customer table exists
        echo "<hr>";
        echo "<h2>Test 5: Checking Database Tables</h2>";
        
        $tables = ['customer', 'brands', 'categories', 'products', 'orders', 'cart'];
        foreach ($tables as $table) {
            $query = "SHOW TABLES LIKE '$table'";
            $result = $db->db_fetch_one($query);
            
            if ($result) {
                echo "✓ <span style='color: green;'>Table exists: $table</span><br>";
                
                // Count records
                $count_query = "SELECT COUNT(*) as total FROM $table";
                $count_result = $db->db_fetch_one($count_query);
                if ($count_result) {
                    echo "&nbsp;&nbsp;&nbsp;→ Records: " . $count_result['total'] . "<br>";
                }
            } else {
                echo "✗ <span style='color: red;'>Table NOT found: $table</span><br>";
            }
        }
        
        // Test 6: Check customer table structure
        echo "<hr>";
        echo "<h2>Test 6: Customer Table Structure</h2>";
        $structure_query = "DESCRIBE customer";
        $structure = $db->db_fetch_all($structure_query);
        
        if ($structure) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            foreach ($structure as $field) {
                echo "<tr>";
                echo "<td>" . $field['Field'] . "</td>";
                echo "<td>" . $field['Type'] . "</td>";
                echo "<td>" . $field['Null'] . "</td>";
                echo "<td>" . $field['Key'] . "</td>";
                echo "<td>" . ($field['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "✗ <span style='color: red;'>Database connection failed!</span><br>";
        echo "Error: " . mysqli_connect_error() . "<br>";
    }
} catch (Exception $e) {
    echo "✗ <span style='color: red;'>Exception: " . $e->getMessage() . "</span><br>";
}

echo "<hr>";
echo "<h2>Test Complete</h2>";
echo "<p>If all tests passed, your database connection is working correctly!</p>";
?>