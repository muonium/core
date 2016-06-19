<?php
class Profile extends Languages
{
    function __construct() {
        parent::__construct();
        if(empty($_SESSION['id']))
            header('Location: '.MVC_ROOT.'/Error/Error/404');
        if(!empty($_SESSION['validate']))
            header('Location: '.MVC_ROOT.'/Validate');
    }
    
    function DefaultAction() {
        include(DIR_VIEW."vProfile.php");
    }
};
?>