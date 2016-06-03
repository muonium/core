<?php

class Home extends Languages {

	function DefaultAction() {
			if(!empty($_SESSION['id']) && !isset($_SESSION['validate'])) {
				include_once(DIR_VIEW.'/vUser.php');
			} else {
				include_once(DIR_VIEW.'/vLogin.php');
			}
	}
}
?>