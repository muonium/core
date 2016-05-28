<?php 
class Routing {

	private static $instance;
	

	public static function getInstance() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	function route() {

		$appel_url = addslashes($_SERVER['REQUEST_URI']);

		// On garde que les parties dont on a besoin dans l'url
		//$appel_url = str_replace("/projects", "", $appel_url);
       // $appel_url = str_replace("/QuantaCloud", "", $appel_url);
        $appel_url = str_replace("/neutron", "", $appel_url);
        

        
		$arguments = explode('/', $appel_url);
		//var_dump($arguments);
		// On enleve les eventuels "/" inutiles
		$this->clear_empty_value($arguments);
		// Nombre d'arguments
		$nbArgs = count($arguments);

		if($nbArgs == 0) // Pas d'arguments, on affiche la page par défaut
		{
			$_controller = DEFAULT_CONTROLLER;
			$_function = DEFAULT_FUNCTION;
		}
		else
		{
			//var_dump($arguments);
			$_controller = $arguments[0];
			//echo $_controller;
			
			if($nbArgs > 1)
			{

				$_function = $arguments[1];
				if($nbArgs > 2)
				{
					for($i = 2; $i < $nbArgs; $i++)
					{
						$_params[$i-2] = $arguments[$i];
					}
				}
			}
		}

		if (!file_exists(DIR_CLASS.'/'.$_controller.'.php'))
		{
			$class = ERROR_CONTROLLER;
			$method = $_controller;
		}		
		else
		{
			require_once(DIR_CLASS.'/'.$_controller.'.php');
		}

		if (!class_exists($_controller))
		{
			// par la suite redirection : header('Location: '.ROOT.'/error');
			echo 'Erreur : La classe '.$_controller.' n\'est pas définie.';
		}
		else
		{
			$_class = new $_controller();
			//var_dump($_class);
			if(!empty($_function))
			{
				if(!method_exists($_class, $_function))
				{
					// par la suite redirection : header('Location: '.ROOT.'/error');
					echo 'Erreur : La méthode '.$_function.' n\'est pas définie.';
				}
				else
				{
					if(!empty($_params))
					{
						$nbParams = count($_params);
						$params = "'".implode($_params, "','")."'";
						
						$r = new ReflectionMethod($_class, $_function);
						
						if($r->getNumberOfRequiredParameters() > $nbParams)
						{
							// par la suite redirection : header('Location: '.ROOT.'/error');
							echo 'Erreur : Impossible d\'appeler la méthode '.$_function.'.';
						}
						else
						{
							$_class->$_function($params);
						}
					}
					else
					{
						$r = new ReflectionMethod($_class, $_function);
						if($r->getNumberOfRequiredParameters() > 0)
						{
							// par la suite redirection : header('Location: '.ROOT.'/error');
							echo 'Erreur : Impossible d\'appeler la méthode '.$_function.'.';
						}
						else
						{
							$_class->$_function();
						}
					}
				}
			}
			else
			{
				// Existence methode "Main" ? Si oui, on l'appelle
				if(method_exists($_class, "DefaultAction"))
					$_class->DefaultAction();
			}
		}

	}
	
	function clear_empty_value(&$array)
	{
		foreach ($array as $key => $value)
		{
			if (empty($value))
				unset($array[$key]);
		}
		$array = array_values($array);
	}

	/*function block($function = "")
	{
		// Cette fonction permet d'ajouter une securite en bloquant l'utilisateur tentant d'acceder a une fonction non autorisee via l'url
		if($this->_nbArgs > 1) {
			if(!isset($function)) // Blocage automatique si plus d'un argument passe dans l'url et pas de nom de fonction definie 
				exit;
			else {
				for($i=1;$i<$this->_nbArgs;$i++) {
					if($this->_arguments[$i] == $function)
						exit;
				}
			}
		}
	}*/

}
?>