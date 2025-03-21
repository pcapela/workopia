<?php

namespace Framework;

use App\controllers\ErrorController;


class Router
{
  protected $routes = [];

  // public function registerRoute($method, $uri, $controller)
  // {
  //   $this->routes[] = [
  //     'method' => $method,
  //     'uri' => $uri,
  //     'controller' => $controller
  //   ];
  // }

  public function registerRoute($method, $uri, $action)
  {
    list($controller, $controllerMethod) = explode('@', $action,);

    $this->routes[] = [
      'method' => $method,
      'uri' => $uri,
      'controller' => $controller,
      'controllerMethod' => $controllerMethod,
    ];
  }

  /**
   * Add GET route
   * 
   * @param string $uri
   * @param string $controller
   * 
   * @return void
   */
  public function get($uri, $controller)
  {
    $this->registerRoute('GET', $uri, $controller);
  }

  /**
   * Add POST route
   * 
   * @param string $uri
   * @param string $controller
   * 
   * @return void
   */
  public function post($uri, $controller)
  {
    $this->registerRoute('POST', $uri, $controller);
  }

  /**
   * Add PUT route
   * 
   * @param string $uri
   * @param string $controller
   * 
   * @return void
   */
  public function put($uri, $controller)
  {
    $this->registerRoute('PUT', $uri, $controller);
  }

  /**
   * Add DELETE route
   * 
   * @param string $uri
   * @param string $controller
   * 
   * @return void
   */
  public function delete($uri, $controller)
  {
    $this->registerRoute('DELETE', $uri, $controller);
  }

  // /**
  //  * Route the request
  //  * 
  //  * @param string $uri
  //  * @param string $method
  //  */
  // public function route($uri, $method)
  // {
  //   foreach ($this->routes as $route) {
  //     if ($route['uri'] === $uri && $route['method'] === $method) {
  //       require basePath('App/' . $route['controller']);
  //       return;
  //     }
  //   }
  //   $this->error(404);
  // }

  /**
   * Route the request
   * 
   * @param string $uri
   * @param string $method
   */
  public function route($uri)
  {
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    foreach ($this->routes as $route) {

      // Split the arg and route uris in segments
      $uriSegments = explode('/', trim($uri));
      $routeSegments = explode('/', trim($route['uri']));

      // Check if nummber if segs matches
      if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
        $params = [];
        $match = true;

        for ($i = 0; $i < count($uriSegments); $i++) {
          if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
            $match = false;
            break;
          }

          // If we've a match for params let's get them...
          if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
            $params[$matches[1]] = $uriSegments[$i];
          }
        }

        if ($match) {
          // Get the controller class and method (not the http method...)
          $controller = 'App\\Controllers\\' . $route['controller'];
          $controllerMethod = $route['controllerMethod'];

          // Instantiate the controller
          $controllerInstance = new $controller();
          $controllerInstance->$controllerMethod($params);
          return;
        }
      }
    }
    ErrorController::notFound("Can't find that sh*t!");
  }
}
