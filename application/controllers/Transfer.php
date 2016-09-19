<?php
namespace application\controllers;
use \library\MVC as l;

class Transfer extends l\Languages {
    
    function DefaultAction() {
        require_once('./application/views/vTransfer.php');
    }
}
?>