<?php

class Connection {
  public $connection;
  private $host = "localhost";
  private $dbname = "yourbest_care";
  private $user = "root";
  private $pass = "";
  private $charset = "utf8mb4";
  private $options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ];

  public function __construct() {
    $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";
    $this->connection = new PDO($dsn, $this->user, $this->pass, $this->options);
  }

  public function ready($query, $params = []) {
    $stmt = $this->connection->prepare($query);
    $stmt->execute($params);
    return $stmt;
  }
}

