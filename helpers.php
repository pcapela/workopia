<?php

/**
 * Get the base path
 * 
 * @param string: $path
 * @return string
 */

function basePath($path)
{
  return  __DIR__ . '/' . $path;
}

/**
 * Load a view
 * 
 * @param $name
 * @return void
 */
function loadView($name)
{
  $viewPath = basePath('views/' . $name . '.view.php');
  if (file_exists($viewPath)) {
    require $viewPath;
  } else {
    echo "View '{$name}' or {$viewPath} not found.";
  }
}

/**
 * Load a partial
 * 
 * @param $name
 * @return void
 */
function loadPartial($name)
{
  $partialFile =  basePath('views/partials/' . $name . '.php');
  if (file_exists($partialFile)) {
    require $partialFile;
  } else {
    echo "View '{$name}' not found.";
  }
}

/**
 * Debug a var 
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

/**
 * Debug a var and die...
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value)
{
  echo '<pre>';
  die(var_dump($value));
  echo '</pre>';
}
