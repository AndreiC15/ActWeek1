<?php

class DatabaseConnection
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $con;

    public function __construct($host, $username, $password, $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function connect()
    {
        $this->con = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->con->connect_error) {
            throw new Exception("Connection failed: " . $this->con->connect_error);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function prepare($sql)
    {
        return $this->con->prepare($sql);
    }

    public function getConnection()
    {
        return $this->con;
    }

    public function closeConnection()
    {
        if ($this->con) {
            $this->con->close();
        }
    }
}

// Instantiate the DatabaseConnection class and connect to the database
$databaseConnection = new DatabaseConnection("localhost", "root", "", "logintest");
$databaseConnection->connect();
