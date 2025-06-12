<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Instantiate AuthController
$auth = new AuthController();

// Perform logout
$auth->logout();

// Clear output buffers
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Redirect to login page
header('Location: /demo/pages/login.php');
exit();
?>