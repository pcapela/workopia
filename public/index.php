<?php
require_once '../helpers.php';

require __DIR__ . '/../vendor/autoload.php';

use Framework\Router;

session_start();

require(basePath('routes.php'));

// Create Router and init routes
$router = new Router();
initRoutes($router);

// Get HTTP URI and METHOD
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);


// Handle HTTP Command...
$router->route($uri);
