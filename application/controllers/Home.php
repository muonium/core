<?php

class Home extends Languages {

	function DefaultAction() {
			if(!empty($_SESSION['id']))
                if(!empty($_SESSION['validate']))
                    header('Location: '.MVC_ROOT.'/Validate');
                else
				    header('Location: '.MVC_ROOT.'/User');
			else
				header('Location: '.MVC_ROOT.'/Login');
	}
}
?>