<?php
namespace application\controllers;
use \library\MVC as l;

class Favorites extends l\Languages {
    
    function DefaultAction() {
        require_once('./application/views/vFavorites.php');
    }
}
?>