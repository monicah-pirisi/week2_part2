<?php
/*
    // Database credentials
    // Settings/db_cred.php

    // define('DB_HOST', 'localhost');
    // define('DB_USER', 'root');
    // define('DB_PASS', '');
    // define('DB_NAME', 'dbforlab');

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
        // Use the database name from the provided SQL dump
        define("DATABASE", "ecommerce_2025A_monicah_lekupe"); 
    }
*/
?>

<?php
//Database credentials

// Auto-detect environment
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // LOCALHOST settings
    if (!defined("SERVER")) define("SERVER", "localhost");
    if (!defined("USERNAME")) define("USERNAME", "root");
    if (!defined("PASSWD")) define("PASSWD", "");
    if (!defined("DATABASE")) define("DATABASE", "shoppn");
} else {
    // PRODUCTION server settings
    if (!defined("SERVER")) define("SERVER", "localhost");
    if (!defined("USERNAME")) define("USERNAME", "monicah.lekupe");
    if (!defined("PASSWD")) define("PASSWD", "Amelia@2026");
    if (!defined("DATABASE")) define("DATABASE", "ecommerce_2025A_monicah_lekupe");
}
?>