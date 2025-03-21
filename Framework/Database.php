<?php

namespace Framework;

use PDO;
use PDOException;
use Exception;

class Database
{
  public $conn;
  /**
   * Constructor
   * 
   * @param array $config
   */
  public function __construct($config)
  {
    $dsm = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];
    try {
      $this->conn = new PDO($dsm, $config['username'], $config['password'], $options);
    } catch (PDOException $e) {
      throw new Exception("DB connection failed: {$e->getMessage()}");
    }
  }

  /**
   * Query the db
   * 
   * @param string $query
   * @return PDOStatment
   * @trows PDOException
   */
  public function query($query, $params = null)
  {
    try {
      $sth = $this->conn->prepare($query);
      $sth->execute($params);
      return $sth;
    } catch (PDOException $e) {
      throw new Exception("Error querying db: {$e->getMessage()}");
    }
  }
}
