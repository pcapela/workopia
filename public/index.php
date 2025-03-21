<?php
require_once '../helpers.php';

require __DIR__ . '/../vendor/autoload.php';

use Framework\Router;

// This will custom autoload the class php on the first 
// reference of the class
// replaced by "psr-4 autoload" see composer.json
// 
// spl_autoload_register(function ($class) {
//   $path = basePath('Framework/' . $class . '.php');
//   if (file_exists($path)) {
//     require $path;
//   }
// });

require(basePath('routes.php'));

// $config = require(basePath('config/db.php'));
// $db = new Database($config);

// Create Router and init routes
$router = new Router();
initRoutes($router);

// Get HTTP URI and METHOD
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
$method = $_SERVER['REQUEST_METHOD'];

// Handle HTTP Command...
$router->route($uri, $method);

// inspect($uri);
// inspect($method);
