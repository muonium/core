<?php 
function autoloader($nomClass) {
	$rootPath = ROOT . DS;
	$_tabDossier = array(
			'./config/',
			'./Application/models/',
			'./Application/bdd/',
			'./Application/controllers/',
			'./Application/views/',
			'./library/',
			'./library/Dossier/',
			'./library/BDD/',
			'./library/MVC/'
	);
	 
	foreach($_tabDossier as $chemindossier) {
		//Test l'existence de la class dans le dossier
		if(file_exists($chemindossier.$nomClass.'.php')) {
			require_once($chemindossier.$nomClass.'.php');

			//Fin d'execution en cas de réussite du chargement : il n'est pas nécessaire de faire tous les dossiers
			return;
		}
	}
}

spl_autoload_register('autoloader');

?>