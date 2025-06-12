<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "pharmacy-inventory";
    protected $connection;
    private $logFile = __DIR__ . '/database_errors.log';

    public function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->database",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $errorMsg = date('[Y-m-d H:i:s]') . " Database Error: " . $e->getMessage() . "\n";
            file_put_contents($this->logFile, $errorMsg, FILE_APPEND);
            throw new Exception("Database connection failed. See log for details.");
        }
    }

    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new Database();
        }
        return $instance->connection;
    }
}
?>

