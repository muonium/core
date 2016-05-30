<?php

class Home extends Languages {

	function DefaultAction() {
			if(isset($_SESSION['Utilisateur'])) {
				include_once(DIR_VIEW.'/vUser.php');
			} else {
				include_once(DIR_VIEW.'/vLogin.php');
			}
	}
}
?>