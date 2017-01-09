<?php
namespace application\controllers;
use \library\MVC as l;

class Recent extends l\Languages {

    function __construct() {
        parent::__construct(array(
            'mustBeLogged' => true,
            'mustBeValidated' => true
        ));
    }

    function DefaultAction() {
        require_once(DIR_VIEW.'vRecent.php');
    }
}
