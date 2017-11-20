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

	function setURL($id) {
		return rtrim(strtr(base64_encode($id), '+/', '-_'), '=');
	}

	function getFileId($b) {
		return base64_decode(str_pad(strtr($b, '-_', '+/'), strlen($b) % 4, '=', STR_PAD_RIGHT));
	}

    function DefaultAction() {
		if(is_array($_GET) && count($_GET) > 0) {
			$b = $this->getFileId(key($_GET));
			if(is_numeric($b)) {
				$this->_modelFiles = new m\Files();
				$infos = $this->_modelFiles->getInfos($b);
				if($infos !== false) {
					$filesize = $this->showSize($infos['size']);
					require_once(DIR_VIEW.'Dl.php');
					exit;
				}
			}
		}
		header('Location: User');
    }
};
?>
