<?php
namespace application\controllers;
use \library\MVC as l;

class Logout
{
    function __construct() {
        // delete tmp session id file
        if(isset($_SESSION['id']) || isset($_SESSION['tmp_id'])) {
			$tmp = ROOT.DS.'tmp/'.sha1(session_id().'c4$AZ_').'.tmp';
			if(file_exists($tmp)) {
            	unlink($tmp);
			}
            session_destroy();
        }
		$location = MVC_ROOT.'/Login';
		if(isset($_GET['val'])) $location .= '/?val=ok';
        header('Location: '.$location);
    }
};
?>
