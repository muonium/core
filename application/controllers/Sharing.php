<?php
namespace application\controllers;
use \library\MVC as l;

class Sharing extends l\Languages {
    
    function DefaultAction() {
        require_once('./application/views/vSharing.php');
    }
}
?>