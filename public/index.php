<?php
require_once '../helpers.php';

require(basePath('Router.php'));
require(basePath('routes.php'));

$router = new Router();
initRoutes($router);

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);

inspect($uri);
inspect($method);
