<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class dl extends l\Languages {

    private $_modelFiles;

    function __construct() {
        parent::__construct([
            'mustBeLogged' => false,
            'mustBeValidated' => false
        ]);
    }

    function DefaultAction() {
		if(is_array($_GET) && count($_GET) > 0) {
			$b = getFileId(key($_GET));
			if(is_numeric($b)) {
				$this->_modelFiles = new m\Files();
				$infos = $this->_modelFiles->getInfos($b);
				if($infos !== false) {
					$filesize = showSize($infos['size']);
					require_once(DIR_VIEW.'Dl.php');
					exit;
				}
			}
		}
		header('Location: User');
    }
};
?>
