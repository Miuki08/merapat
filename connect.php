<?php

class Database {

    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "prm";
    public $conn;
    
    public function __construct() {
        $this->conn = new mysqli(
            $this->host, 
            $this->username, 
            $this->password, 
            $this->database
        );
    }

    public function query($sql){
        return $this->conn->query($sql);
    }

}

?>