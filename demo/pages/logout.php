<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify file is being accessed
error_log('Logout script accessed at: ' . date('Y-m-d H:i:s'));

// Check if AuthController exists
if (!file_exists(__DIR__ . '/../controllers/AuthController.php')) {
    error_log('ERROR: AuthController.php not found');
    die('System error - please contact administrator');
}

require_once __DIR__ . '/../controllers/AuthController.php';

// Verify session status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_log('Session started for logout');
}

// Debug current session
error_log('Current session data: ' . print_r($_SESSION, true));

$auth = new AuthController();

// Verify AuthController instantiation
if (!$auth) {
    error_log('ERROR: Failed to instantiate AuthController');
    die('System error - please contact administrator');
}

// Perform logout
$auth->logout();
error_log('Logout method executed');

// Verify session was destroyed
if (session_status() === PHP_SESSION_ACTIVE) {
    error_log('WARNING: Session still active after logout');
}

// Clear output buffers
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Redirect to login with absolute URL
$redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/demo/pages/login.php';
error_log('Redirecting to: ' . $redirectUrl);
header('Location: ' . $redirectUrl);
exit();
?>
