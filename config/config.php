<?php

class Database {
    public $connection;
    
    private $dbhost = 'localhost';
    private $dbname = 'kiddo';
    private $dbuser = 'root';
    private $dbpass = '';
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->dbuser, $this->dbpass);
            
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}