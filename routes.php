<?php


function initRoutes($router)
{
  // Home --------------------------------------------------
  $router->get('/', 'HomeController@index');

  // Listings -----------------------------------------------

  $router->get('/listings', 'ListingsController@index');
  $router->get('/listings/create', 'ListingsController@create');

  $router->post('/listings', 'ListingsController@store');
  $router->get('/listings/{id}', 'ListingsController@show');

  $router->get('/listings/edit/{id}', 'ListingsController@edit');
  $router->put('/listings/{id}', 'ListingsController@update');

  $router->delete('/listings/{id}', 'ListingsController@destroy');

  // User & Autherization --------------------------------------
  $router->get('/auth/register', 'UserController@create');
  $router->post('/auth/register', 'UserController@store');

  $router->get('/auth/login', 'UserController@login');
  $router->post('/auth/login', 'UserController@authenticate');
  $router->get('/auth/logout', 'UserController@logout');
}
