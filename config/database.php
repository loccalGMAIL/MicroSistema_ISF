<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'microsistema_isf';
    public $mysqli;

    public function __construct() {
        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->mysqli->connect_errno) {
            die('Fallo la conexión: ' . $this->mysqli->connect_error);
        }
    }

    public function getConnection() {
        return $this->mysqli;
    }

    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
}