<?php
namespace application\controllers;
use \library\MVC as l;

class Recent extends l\Languages {
    
    function DefaultAction() {
        require_once('./application/views/vRecent.php');
    }
}
?>