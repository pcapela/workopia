<?php


function initRoutes($router)
{
  $router->get('/', 'HomeController@index');

  $router->get('/listings', 'ListingsController@index');
  $router->get('/listings/create', 'ListingsController@create');

  $router->post('/listings', 'ListingsController@store');

  $router->get('/listing/{id}', 'ListingsController@show');
}
