<?php
// Test file paths and includes
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>File Path Test</h2>";

echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Test core.php
$core_path = __DIR__ . '/settings/core.php';
echo "<p>Core path: " . $core_path . "</p>";
echo "<p>Core exists: " . (file_exists($core_path) ? 'YES' : 'NO') . "</p>";

if (file_exists($core_path)) {
    echo "<p>Core file size: " . filesize($core_path) . " bytes</p>";
}

// Test controller
$controller_path = __DIR__ . '/controllers/customer_controller.php';
echo "<p>Controller path: " . $controller_path . "</p>";
echo "<p>Controller exists: " . (file_exists($controller_path) ? 'YES' : 'NO') . "</p>";

// Test database class
$db_class_path = __DIR__ . '/settings/db_class.php';
echo "<p>DB class path: " . $db_class_path . "</p>";
echo "<p>DB class exists: " . (file_exists($db_class_path) ? 'YES' : 'NO') . "</p>";

// Test customer class
$customer_class_path = __DIR__ . '/classes/customer_class.php';
echo "<p>Customer class path: " . $customer_class_path . "</p>";
echo "<p>Customer class exists: " . (file_exists($customer_class_path) ? 'YES' : 'NO') . "</p>";

// List files in current directory
echo "<h3>Files in current directory:</h3>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<p>" . $file . "</p>";
    }
}
?>
