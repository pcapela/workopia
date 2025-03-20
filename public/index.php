<?php
require_once '../helpers.php';

require(basePath('Router.php'));
require(basePath('Database.php'));
require(basePath('routes.php'));

// $config = require(basePath('config/db.php'));
// $db = new Database($config);

// Create Router and init routes
$router = new Router();
initRoutes($router);

// Get HTTP URI and METHOD
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Handle HTTP Command...
$router->route($uri, $method);

// inspect($uri);
// inspect($method);
