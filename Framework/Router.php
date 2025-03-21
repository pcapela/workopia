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

  /**
   * Load error page
   * @param int @errorcode
   */
  public function error($errorCode)
  {
    http_response_code($errorCode);
    loadView('error/' . $errorCode);
    exit;
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
  public function route($uri, $method)
  {
    foreach ($this->routes as $route) {
      if ($route['uri'] === $uri && $route['method'] === $method) {

        // Get the controller class and method (not the http method...)
        $controller = 'App\\Controllers\\' . $route['controller'];
        $controllerMethod = $route['controllerMethod'];

        // Instantiate the controller
        $controllerInstance = new $controller();
        $controllerInstance->$controllerMethod();
        return;
      }
    }
    ErrorController::notFound("Can't find that sh*t!");
  }
}
