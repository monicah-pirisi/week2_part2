<?php
// Production Database Configuration
// Copy this file to db_cred.php and update with your production credentials

if (!defined("SERVER")) {
    define("SERVER", "localhost"); // Your production database server
}

if (!defined("USERNAME")) {
    define("USERNAME", "your_production_username"); // Your production database username
}

if (!defined("PASSWD")) {
    define("PASSWD", "your_secure_production_password"); // Your production database password
}

if (!defined("DATABASE")) {
    define("DATABASE", "shoppn"); // Your production database name
}

// Additional production settings
if (!defined("DB_CHARSET")) {
    define("DB_CHARSET", "utf8mb4");
}

if (!defined("DB_COLLATE")) {
    define("DB_COLLATE", "utf8mb4_unicode_ci");
}

// Connection timeout settings
if (!defined("DB_TIMEOUT")) {
    define("DB_TIMEOUT", 30);
}

// SSL settings for production (uncomment if using SSL)
/*
if (!defined("DB_SSL")) {
    define("DB_SSL", true);
}

if (!defined("DB_SSL_CA")) {
    define("DB_SSL_CA", "/path/to/ca-cert.pem");
}
*/
?>
