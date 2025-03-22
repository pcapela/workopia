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

    $newUserData = array_intersect_key($_POST, array_flip($alowedFields));

    $newUserData = array_map('sanitize', $newUserData);

    $requiredFields = ['name', 'password', 'password_confirmation', 'email'];

    $errors = [];
    foreach ($requiredFields as $field) {
      if (empty($newUserData[$field]) || !Validation::string($newUserData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required.';
      }
    }
    // Specific checks
    if (!empty($newUsergData['password']) && $newUserData['password'] !== $newUserData['password_confirmation']) {
      $errors['password'] = 'Passwords must match.';
    }

    if (!empty($newUsergData['email']) && !Validation::email($newUserData['email'])) {
      $errors['email'] = 'e-mail format is not valid.';
    }

    $query = 'SELECT email FROM users WHERE email = :email;';
    if (!empty($newUserData['email']) && $this->db->query($query, ['email' => $newUserData['email']])->fetch() !== false) {
      $errors['email'] = 'User name (e-mail) already in use.';
    }

    if (!empty($errors)) {
      // Reload view with errors
      loadView('users/create', [
        'user' => $newUserData,
        'errors' => $errors
      ]);
    } else {
      unset($newUserData['password_confirmation']); // Not a db table column
      $fields = [];
      $values = [];
      foreach ($newUserData as $field => $data) {
        $fields[] = $field;
        $values[] = ':' . $field;
        if ($data === '') {
          $newUserData[$field] = null;
        }
      }

      $fields = implode(',', $fields);
      $values = implode(',', $values);


      try {
        $query = "INSERT INTO users ({$fields}) VALUES ({$values});";
        $this->db->query($query, $newUserData);
        redirect('/');
      } catch (Exception $e) {
        inspectAndDie($e);
        $errors['insert'] = $e->getMessage();
        $newUserData['password_confirmation'] = $newUserData['password'];
        loadView('users/create', [
          'user' => $newUserData,
          'errors' => $errors
        ]);
      }
    }
  }
}
