<?php
/**
 * Database Credentials - Minimal version for API endpoints
 * This file only defines database constants without any extra functionality
 * to prevent HTML output in JSON responses
 */

// Define database constants
if (!defined('SERVER'))   define('SERVER', 'localhost');
if (!defined('USERNAME')) define('USERNAME', 'monicah.lekupe');
if (!defined('PASSWD'))   define('PASSWD', 'Amelia@2026');
if (!defined('DATABASE')) define('DATABASE', 'ecommerce_2025A_monicah_lekupe');

// Define minimal required constants to prevent errors
// if (!defined('APP_ENV'))  define('APP_ENV', 'production');
// if (!defined('APP_DEBUG')) define('APP_DEBUG', false);
// ?>