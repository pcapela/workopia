<?php

namespace App\Controllers;

use Framework\Database;

require_once '../helpers.php';

class HomeController
{
  protected $db;

  public function __construct()
  {
    $config = require(basePath('config/db.php'));
    $this->db = new Database($config);
  }

  public function index($params)
  {
    $listings = $this->db->query('SELECT * FROM listings LIMIT 6')->fetchAll();

    loadView('home', [
      'listings' => $listings
    ]);
  }
}
