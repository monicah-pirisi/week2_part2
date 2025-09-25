// Settings/core.php
<?php

// for header redirection
ob_start();

// Secure session configuration and single start
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$httpOnly = true;

if (PHP_VERSION_ID >= 70300) {
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => $isHttps,
		'httponly' => $httpOnly,
		'samesite' => 'Lax'
	]);
} else {
	ini_set('session.cookie_httponly', '1');
	ini_set('session.cookie_secure', $isHttps ? '1' : '0');
	// Best-effort for older PHP versions
	ini_set('session.cookie_samesite', 'Lax');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Check if the user is logged in
function isLoggedIn() {
	return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

// Check if the logged-in user has admin role
function isAdmin() {
	return isLoggedIn() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

?>