<?php
session_start();

// On vérifie que l'ip n'est pas bannie
/*if(file_exists("banned_ip.txt")) {
    $bannedIP = explode(";", file_get_contents("banned_ip.txt"));
    if(in_array($_SERVER['REMOTE_ADDR'], $bannedIP)) {
        echo 'Votre adresse IP ('.$_SERVER['REMOTE_ADDR'].') est bannie !';
        exit;
    }     
}*/

include_once("./config/autoload.php");

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)).DS."\\");

// Contrôleur par défaut
define ('DEFAULT_CONTROLLER', 'Accueil');
define ('DEFAULT_FUNCTION', 'DefaultAction');

// Contrôleur en cas d'erreur
define ('ERROR_CONTROLLER', 'Error');
define ('ERROR_FUNCTION', 'Error');
 
define ('DIR_CLASS', __DIR__.'/application/controllers/');
define ('DIR_MODEL', __DIR__.'/application/models/');
define ('DIR_VIEW',  __DIR__.'/application/views/');

/* ROUTING */
$_routing = Routing::getInstance();
$_routing->route();
//



?>