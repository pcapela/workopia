<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

require_once '../helpers.php';

class ListingsController
{
  protected $db;

  public function __construct()
  {
    $config = require(basePath('config/db.php'));
    $this->db = new Database($config);
  }

  public function index($params)
  {

    $listings = $this->db->query('SELECT * FROM listings')->fetchAll();
    loadView('listings/index', [
      'listings' => $listings
    ]);
  }
  public function create($params)
  {
    loadView('listings/create');
  }

  public function show($params)
  {
    //$id = $_GET['id'] ?? '';
    $id = $params['id'];
    $listing = $this->db->query('SELECT * FROM listings WHERE id = :id;', ['id' => $id])->fetch();

    if (!$listing) {
      ErrorController::notFound('Listing not found!');
    } else {
      loadView('listings/show', [
        'listing' => $listing
      ]);
    }
  }

  public function store($params)
  {
    $alowedFields = ['title', 'description', 'salary', 'requirements', 'benefits', 'company', 'address', 'city', 'state', 'phone', 'email'];

    $newListingData = array_intersect_key($_POST, array_flip($alowedFields));

    $newListingData['user_id'] = 1; // lacking a better one for now...
    $newListingData = array_map('sanitize', $newListingData);

    $requiredFields = ['title', 'description', 'city', 'state', 'email'];

    $errors = [];
    foreach ($requiredFields as $field) {
      if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required.';
      }
    }
    if (!empty($errors)) {
      // Reload view with errors
      loadView('listings/create', [
        'listing' => $newListingData,
        'errors' => $errors
      ]);
    } else {
      $fields = [];
      $values = [];
      foreach ($newListingData as $field => $data) {
        $fields[] = $field;
        $values[] = ':' . $field;
        if ($data === '') {
          $newListingData[$field] = null;
        }
      }
      $fields = implode(',', $fields);
      $values = implode(',', $values);

      $query = "INSERT INTO listings ({$fields}) VALUES ({$values});";
      $this->db->query($query, $newListingData);
      redirect('/listings');
    }
  }
}
