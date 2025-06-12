<?php
require_once __DIR__ . '/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function findByUsername(string $username) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $errorMsg = date('[Y-m-d H:i:s]') . " SQL Error in findByUsername: " . $e->getMessage() . "\n";
            file_put_contents(__DIR__ . '/database_errors.log', $errorMsg, FILE_APPEND);
            return false;
        }
    }

    public function verifyCredentials(string $username, string $password) {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
