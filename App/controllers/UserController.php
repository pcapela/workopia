<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use PDOException;
use Exception;

require_once '../helpers.php';


class UserController
{

  protected $db;

  public function __construct()
  {
    $config = require(basePath('config/db.php'));
    $this->db = new Database($config);
  }

  /**
   * Login page
   *
   * @param [type] $params
   * @return void
   */
  public function login($params)
  {
    loadView('users/login');
  }

  /**
   * Login page
   *
   * @param [type] $params
   * @return void
   */
  public function logout($params)
  {
    $_SESSION['success_message'] = 'User ' . $_SESSION['loggedin_user_email'] . ' logged out';
    unset($_SESSION['loggedin_user']);
    unset($_SESSION['loggedin_user_email']);
    redirect('/');
  }

  /**
   * Authenticate user
   *
   * @param [type] $params
   * @return void
   */
  public function authenticate($params)
  {

    $alowedFields = ['email', 'password'];
    $authenticationData = array_intersect_key($_POST, array_flip($alowedFields));


    $errors = [];
    foreach ($alowedFields as $field) {
      if (empty($authenticationData[$field]) || !Validation::string($authenticationData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required.';
      }
    }
    if (!empty($authenticationData['email']) && !empty($authenticationData['password'])) {

      $query = 'SELECT * FROM users WHERE email = :email;';
      $user = $this->db->query($query, ['email' => $authenticationData['email']])->fetch();

      if ($user === false || $user->password !== $authenticationData['password']) {
        $errors['password'] = 'e-mail and password do not match!';
      }
    }
    if (!empty($errors)) {
      unset($_SESSION['logedin_user']);
      // Reload view with errors
      loadView('users/login', [
        'user' => $authenticationData,
        'errors' => $errors
      ]);
    } else {
      $_SESSION['success_message'] = 'User ' . $user->email . ' logged in';
      $_SESSION['loggedin_user'] = $user->id;
      $_SESSION['loggedin_user_email'] = $user->email;
      redirect('/');
    }
  }


  /**
   * Register page
   *
   * @param [type] $params
   * @return void
   */
  public function create($params)
  {
    loadView('users/create');
  }

  /**
   * Store User in DB
   *
   * @param [type] $params
   * @return void
   */
  public function store($params)
  {
    // inspectAndDie('Store....');

    $alowedFields = ['name', 'email', 'city', 'state', 'email', 'password', 'password_confirmation'];

    $newUsergData = array_intersect_key($_POST, array_flip($alowedFields));

    $newUsergData = array_map('sanitize', $newUsergData);

    $requiredFields = ['name', 'password', 'password_confirmation', 'email'];

    $errors = [];
    foreach ($requiredFields as $field) {
      if (empty($newUsergData[$field]) || !Validation::string($newUsergData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required.';
      }
    }
    // Specific checks
    if (!empty($newUsergData['password']) && $newUsergData['password'] !== $newUsergData['password_confirmation']) {
      $errors['password'] = 'Passwords must match.';
    }

    if (!empty($newUsergData['email']) && !Validation::email($newUsergData['email'])) {
      $errors['email'] = 'e-amil format is not valid.';
    }

    $query = 'SELECT email FROM users WHERE email = :name;';
    if (!empty($newUsergData['email']) && $this->db->query($query, ['email' => $newUsergData['email']])->fetch() !== false) {
      $errors['name'] = 'User name already in use.';
    }

    if (!empty($errors)) {
      // Reload view with errors
      loadView('users/create', [
        'user' => $newUsergData,
        'errors' => $errors
      ]);
    } else {
      unset($newUsergData['password_confirmation']); // Not a table column
      $fields = [];
      $values = [];
      foreach ($newUsergData as $field => $data) {
        $fields[] = $field;
        $values[] = ':' . $field;
        if ($data === '') {
          $newUsergData[$field] = null;
        }
      }

      $fields = implode(',', $fields);
      $values = implode(',', $values);

      $query = "INSERT INTO users ({$fields}) VALUES ({$values});";
      try {
        $this->db->query($query, $newUsergData);
        redirect('/');
      } catch (Exception $e) {
        inspectAndDie($e);
        $errors['insert'] = $e->getMessage();
        $newUsergData['password_confirmation'] = $newUsergData['password'];
        loadView('users/create', [
          'user' => $newUsergData,
          'errors' => $errors
        ]);
      }
    }
  }
}
