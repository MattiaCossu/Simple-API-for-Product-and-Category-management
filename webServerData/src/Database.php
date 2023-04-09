<?php
require_once 'config.php';

class Database {
    private Pdo $connection;

    public function __construct() {
        $this->connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    }

    public function getConnection() {
        return $this->connection;
    }
}