<?php
/**
 * Database Credentials
 * DEPRECATED: This file is kept for backward compatibility
 * Please use settings/config.php which loads from .env file
 */

if (!defined('SERVER'))   define('SERVER', 'localhost');
if (!defined('USERNAME')) define('USERNAME', 'root');
if (!defined('PASSWD'))   define('PASSWD', '');
if (!defined('DATABASE')) define('DATABASE', 'ecommerce_2025A_monicah_lekupe');

// Load configuration from .env file
require_once __DIR__ . '/config.php';

// Constants are already defined in config.php
// No need to redefine them here
?>
