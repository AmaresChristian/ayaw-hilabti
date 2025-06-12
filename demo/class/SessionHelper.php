<?php
class SessionHelper {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setFlash($key, $message) {
        self::start();
        $_SESSION['flash_messages'][$key] = $message;
    }

    public static function getFlash($key) {
        self::start();
        if (isset($_SESSION['flash_messages'][$key])) {
            $msg = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $msg;
        }
        return null;
    }

    public static function generateCsrfToken() {
        self::start();
        if(empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken($token) {
        self::start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
