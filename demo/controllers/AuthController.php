<?php
require_once __DIR__ . '/../class/User.php';
session_start();

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login($username, $password) {
        $user = $this->userModel->verifyCredentials($username, $password);
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            return true;
        }
        return false;
    }
    // User.php
public function verifyCredentials(string $username, string $password) {
    $user = $this->findByUsername($username);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

    public function logout() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function check() {
        return isset($_SESSION['user']);
    }

    public function user() {
        return $_SESSION['user'] ?? null;
    }

    public function requireLogin() {
        if (!$this->check()) {
            header('Location: /pages/login.php');
            exit();
        }
    }

    public function requireRole($roles) {
        $user = $this->user();
        if (!$user || !in_array($user['role'], (array)$roles)) {
            header('HTTP/1.1 403 Forbidden');
            echo '403 Forbidden - You do not have permission to access this page.';
            exit();
        }
    }
}
?>
