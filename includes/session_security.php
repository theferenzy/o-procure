<?php
// Set session security options BEFORE session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? true : false);

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

// Session inactivity timeout (30 minutes)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header("Location: /o-procure/pages/login.php");
    exit();
}
$_SESSION['last_activity'] = time();
