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
        
        // We remove the first directory of the url and we keep only the last part
        //if(!($pos = strpos($appel_url, '/', 1)))
        //    $appel_url = "";
        //else {
        //    $appel_url = substr($appel_url, $pos);
        //}
        
        $appel_url = str_replace(MVC_ROOT, "", $appel_url);
        if($appel_url[0] == "/")
            $appel_url = substr($appel_url, 1);
        
		$arguments = explode('/', $appel_url);
		//var_dump($arguments);
		// We remove useless "/"
		$this->clear_empty_value($arguments);
		// Number of arguments
		$nbArgs = count($arguments);

		if($nbArgs == 0) // No arguments, we'll display default page
		{
			$_controller = DEFAULT_CONTROLLER;
			$_method = DEFAULT_FUNCTION;
		}
		else
		{
            // Controller is the first argument
			$_controller = $arguments[0];
			
            // More arguments ?
			if($nbArgs > 1)
			{
                // Method is the second argument
                // We add "Action" suffix to tell that it's a method called in the url (to differentiate with other methods)
				$_method = $arguments[1].'Action';
				if($nbArgs > 2)
				{
                    // If there are more arguments, then these are the parameters
					for($i = 2; $i < $nbArgs; $i++)
					{
						$params[$i-2] = $arguments[$i];
					}
				}
			}
		}

		if (!file_exists(DIR_CLASS.'/'.$_controller.'.php'))
		{
            // Error : Controller doesn't exists
			header('Location: '.MVC_ROOT.'/Error/Error/404');
		}		
		else
			require_once(DIR_CLASS.'/'.$_controller.'.php');

		if (!class_exists($_controller))
		{
            // Error : Class doesn't exists
			header('Location: '.MVC_ROOT.'/Error/Error/404');
		}
		else
		{
            // Call the controller
			$_class = new $_controller();
            
            // Call a method ?
			if(!empty($_method))
			{
				if(!method_exists($_class, $_method))
				{
                    // Error : Method doesn't exists
					header('Location: '.MVC_ROOT.'/Error/Error/404');
				}
				else
				{
                    // Are there parameters ?
					if(!empty($params))
					{
						$nbParams = count($params);
						
						$r = new ReflectionMethod($_class, $_method);
						
						if($r->getNumberOfRequiredParameters() > $nbParams)
						{
                            // Error : Not enough parameters for calling this method
							header('Location: '.MVC_ROOT.'/Error/Error/404');
						}
						else
                        {
							switch($nbParams) {
                                case 1:
                                    $_class->$_method($params['0']);
                                    break;
                                case 2:
                                    $_class->$_method($params['0'], $params['1']);
                                    break;
                                case 3:
                                    $_class->$_method($params['0'], $params['1'], $params['2']);
                                    break;
                                case 4:
                                    $_class->$_method($params['0'], $params['1'], $params['2'], $params['3']);
                                    break;
                                case 5:
                                    $_class->$_method($params['0'], $params['1'], $params['2'], $params['3'], $params['4']);
                                    break;
                                case 6:
                                    $_class->$_method($params['0'], $params['1'], $params['2'], $params['3'], $params['4'], $params['5']);
                                    break;
                                case 7:
                                    $_class->$_method($params['0'], $params['1'], $params['2'], $params['3'], $params['4'], $params['5'], $params['6']);
                                    break;
                                case 8:
                                    $_class->$_method($params['0'], $params['1'], $params['2'], $params['3'], $params['4'], $params['5'], $params['6'], $params['7']);
                                    break;
                                default:
                                    // Error : Routing doesn't support more than 8 parameters
                                    header('Location: '.MVC_ROOT.'/Error/Error/404');
                            }
                        }
					}
					else
					{
						$r = new ReflectionMethod($_class, $_method);
						if($r->getNumberOfRequiredParameters() > 0)
						{
                            // Error : Not enough parameters for calling this method
							header('Location: '.MVC_ROOT.'/Error/Error/404');
						}
						else
							$_class->$_method();
					}
				}
			}
			else
			{
                // No method called
				// "DefaultAction" is the default method, if "DefaultAction" exists, we call it
				if(method_exists($_class, "DefaultAction"))
					$_class->DefaultAction();
			}
		}

	}
	
    // Allows to delete useless "/" in the url
	function clear_empty_value(&$array)
	{
		foreach ($array as $key => $value)
		{
			if (empty($value))
				unset($array[$key]);
		}
		$array = array_values($array);
	}
}
?>