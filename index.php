<?php
// This file is always called
session_start();
include_once("./config/autoload.php");

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);

define('MVC_ROOT', '/core');

// Default controller
define ('DEFAULT_CONTROLLER', 'Home');
define ('DEFAULT_FUNCTION', 'DefaultAction');

// Error controller
define ('ERROR_CONTROLLER', 'Error');
define ('ERROR_FUNCTION', 'Error');
 
define ('DIR_CLASS', __DIR__.'/application/controllers/');
define ('DIR_MODEL', __DIR__.'/application/models/');
define ('DIR_VIEW',  __DIR__.'/application/views/');

define ('DEFAULT_LANGUAGE', 'en');
define ('DIR_LANGUAGE', __DIR__.'/public/translations/');

/* ROUTING */
$_routing = Routing::getInstance();
$_routing->route();
//
?>