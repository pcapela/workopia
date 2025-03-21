<?php

namespace App\Controllers;

use Framework\Database;

require_once '../helpers.php';

class ListingsController
{
  protected $db;

  public function __construct()
  {
    $config = require(basePath('config/db.php'));
    $this->db = new Database($config);
  }

  public function index()
  {
    $listings = $this->db->query('SELECT * FROM listings')->fetchAll();
    loadView('listings/index', [
      'listings' => $listings
    ]);
  }
  public function create()
  {
    loadView('listings/create');
  }

  public function show()
  {
    $id = $_GET['id'] ?? '';
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id;', ['id' => $id])->fetch();

    loadView('listings/show', [
      'listing' => $listing
    ]);
  }
}
