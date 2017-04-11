<?php
// This file is always called
use \library\MVC as l;
//
session_start();
require_once("./config/autoload.php");

// Defines

// Mui Version
define('VERSION', '2017.04.11.0');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);

define('MVC_ROOT', '/core');
define('IMG', '/core/public/pictures/');
define('NOVA', dirname(dirname(__FILE__)).'/nova');

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

// Functions

function echo_h($str) {
    echo htmlentities($str, ENT_QUOTES);
}

// Banned session (anti-bruteforce)
if(!empty($_SESSION['banSID'])) {
    $err = new l\Languages();
    echo_h($err->txt->Error->bannedSession);
    exit;
}

/* ROUTING */
$_routing = l\Routing::getInstance();
$_routing->route();
//
?>
