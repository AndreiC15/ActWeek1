<?php

class DatabaseConnection {
    private $host;
    private $username;
    private $password;
    private $database;
    private $con;

    public function __construct($host, $username, $password, $database) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function prepare($sql) {
        return $this->getConnection()->prepare($sql);
    }

    public function connect() {
        $this->con = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->con->connect_error) {
            die("Connection failed: " . $this->con->connect_error);
        }

        session_start();
    }

    public function getConnection() {
        return $this->con;
    }

    public function closeConnection() {
        if ($this->con) {
            $this->con->close();
        }
    }
}
$databaseConnection = new DatabaseConnection("localhost", "root", "", "logintest");
$databaseConnection->connect();
?>
