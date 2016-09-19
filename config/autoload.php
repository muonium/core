<?php 
function autoloader($class_name) {
	$tab_dir = array(
			'./config/',
			'./application/models/',
			'./application/controllers/',
			'./application/views/',
			'./library/',
			'./library/MVC/'
	);
	 
	foreach($tab_dir as $dir) {
		//Class exists in this dir ?
		if(file_exists($dir.$class_name.'.php')) {
			require_once($dir.$class_name.'.php');

			//End of execution if successful loading: it is not necessary to do all records
			return;
		}
	}
}

spl_autoload_register('autoloader');

