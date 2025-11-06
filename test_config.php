<?php
// Test configuration loading
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Configuration Test</h2>";

// Check if .env file exists
$envPath = __DIR__ . '/.env';
echo "<p><strong>.env file path:</strong> " . $envPath . "</p>";
echo "<p><strong>.env exists:</strong> " . (file_exists($envPath) ? 'YES' : 'NO') . "</p>";

if (file_exists($envPath)) {
    echo "<p><strong>.env is readable:</strong> " . (is_readable($envPath) ? 'YES' : 'NO') . "</p>";
}

// Load config
require_once __DIR__ . '/settings/config.php';

echo "<h3>Database Configuration:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Constant</th><th>Defined?</th><th>Value</th></tr>";

$constants = ['SERVER', 'USERNAME', 'PASSWD', 'DATABASE'];
foreach ($constants as $const) {
    $isDefined = defined($const);
    $value = $isDefined ? constant($const) : 'NOT DEFINED';
    if ($const === 'PASSWD' && $isDefined) {
        $value = '***' . str_repeat('*', strlen($value) - 3) . substr($value, -3);
    }
    echo "<tr><td>$const</td><td>" . ($isDefined ? 'YES' : 'NO') . "</td><td>$value</td></tr>";
}

echo "</table>";

echo "<h3>Environment Variables:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

$envVars = ['DB_HOST', 'DB_USERNAME', 'DB_PASSWORD', 'DB_NAME'];
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($var === 'DB_PASSWORD' && $value) {
        $value = '***' . str_repeat('*', max(0, strlen($value) - 3)) . substr($value, -3);
    }
    echo "<tr><td>$var</td><td>" . ($value ?: 'NOT SET') . "</td></tr>";
}

echo "</table>";

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $db = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
    if ($db->connect_error) {
        echo "<p style='color: red;'><strong>Connection Failed:</strong> " . $db->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'><strong>Connection Successful!</strong></p>";
        echo "<p>MySQL Server Version: " . $db->server_info . "</p>";
        $db->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Exception:</strong> " . $e->getMessage() . "</p>";
}
?>
