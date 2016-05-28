<?php

class Accueil extends Controleur {

	function DefaultAction() {
			if(isset($_SESSION['Utilisateur'])) {
				include_once('./application/views/vUtilisateur.php');
			} else {
				include_once('./application/views/vLogin.php');
			}
	}
}
?>