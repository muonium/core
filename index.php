<?php
// This file is always called
use \library\MVC as l;
//
session_start();
require_once("./config/autoload.php");

// Defines

// Mui Version
define('VERSION', '2018.02.19.1');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);
define('MVC_ROOT', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));
define('URL_APP', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . MVC_ROOT);
define('IMG', MVC_ROOT.'/public/pictures/');
define('NOVA', dirname(dirname(__FILE__)).'/nova');

// Default controller
define ('DEFAULT_CONTROLLER', 'Home');
define ('DEFAULT_FUNCTION', 'DefaultAction');

// Error controller
define ('ERROR_CONTROLLER', 'Error');
define ('ERROR_FUNCTION', 'Error');

define ('DIR_CLASS', ROOT.'/application/controllers/');
define ('DIR_MODEL', ROOT.'/application/models/');
define ('DIR_VIEW',  ROOT.'/application/views/');

define ('DEFAULT_LANGUAGE', 'en');
define ('DIR_LANGUAGE', ROOT.'/public/translations/');

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

require_once("library/MVC/Functions.php");
/* ROUTING */
$_routing = l\Routing::getInstance();
$_routing->route();
//
?>
