<?php
namespace library\MVC;

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
        $appel_url = str_replace(MVC_ROOT, '', $appel_url);
        if($appel_url[0] == "/") $appel_url = substr($appel_url, 1);

		$arguments = explode('/', $appel_url);
		$this->clear_empty_value($arguments); // Remove useless "/"
		// Number of arguments
		$nb_args = count($arguments);

		if($nb_args == 0) { // No arguments, we'll display default page
			$_controller = DEFAULT_CONTROLLER;
			$_method = DEFAULT_FUNCTION;
		}
		else {
			if(substr($arguments[$nb_args-1], 0, 1) === '?') {
				// Query string support
				array_pop($arguments);
				$nb_args--;
			}
            // Controller is the first argument
			$_controller = $arguments[0];

            // More arguments ?
			if($nb_args > 1) {
                // Method is the second argument
                // We add "Action" suffix to tell that it's a method called in the url (to differentiate with other methods)
				$_method = $arguments[1].'Action';
				if($nb_args > 2) {
                    // If there are more arguments, then these are the parameters
					for($i = 2; $i < $nb_args; $i++) {
						$params[$i-2] = $arguments[$i];
					}
				}
			}
		}

		if (!file_exists(DIR_CLASS.'/'.$_controller.'.php')) {
            // Error : Controller doesn't exists
			header('Location: '.MVC_ROOT.'/Error/Error/404');
		}
		else {
			require_once(DIR_CLASS.'/'.$_controller.'.php');
		}
        $c = '\application\controllers\\'.$_controller;

		if (!class_exists($c)) {
            // Error : Class doesn't exists
			header('Location: '.MVC_ROOT.'/Error/Error/404');
		}
		else {
            // Call the controller
			$_class = new $c();

            // Call a method ?
			if(!empty($_method)) {
				if(!method_exists($_class, $_method)) {
                    // Error : Method doesn't exists
					header('Location: '.MVC_ROOT.'/Error/Error/404');
				}
				else {
                    // Are there parameters ?
					if(!empty($params)) {
						$nb_params = count($params);
						$r = new \ReflectionMethod($_class, $_method);

						if($r->getNumberOfRequiredParameters() > $nb_params) {
                            // Error : Not enough parameters for calling this method
							header('Location: '.MVC_ROOT.'/Error/Error/404');
						}
						else {
							call_user_func_array([$_class, $_method], $params);
                        }
					}
					else {
						$r = new \ReflectionMethod($_class, $_method);
						if($r->getNumberOfRequiredParameters() > 0) {
                            // Error : Not enough parameters for calling this method
							header('Location: '.MVC_ROOT.'/Error/Error/404');
						}
						else {
							$_class->$_method();
						}
					}
				}
			}
			else {
                // No method called
				// "DefaultAction" is the default method, if "DefaultAction" exists, we call it
				if(method_exists($_class, "DefaultAction")) {
					$_class->DefaultAction();
				}
			}
		}

	}

    // Allows to delete useless "/" in the url
	function clear_empty_value(&$array) {
		foreach ($array as $key => $value) {
			if(empty($value)) unset($array[$key]);
		}
		$array = array_values($array);
	}
}
