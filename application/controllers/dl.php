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
                    $folderID = $this->_modelFiles->getFolderFromId($b);
					require_once(DIR_VIEW.'Dl.php');
					exit;
				}
			}
		}
		header('Location: User');
    }
    
    function getChunkAction($sharerID) {
		if(isset($_POST['filename']) && isset($_POST['line']) && isset($_POST['folder_id'])) {
			// Get a chunk with Ajax
		    $line = $_POST['line'];
		    $filename = $this->parseFilename($_POST['filename']);
			$folder_id = $_POST['folder_id'];

			if($filename !== false && is_numeric($folder_id)) {
				$path = $this->getUploadFolderPath($folder_id);
				if($path === false) {
					echo 'error';
					exit;
				}

				$filepath = NOVA.'/'.$sharerID.'/'.$path.$filename;
				$file = new \SplFileObject($filepath, 'r');
			    $file->seek($line);

			    echo str_replace("\r\n", "", $file->current());
			}
		}
	}

	function getNbChunksAction($sharerID) {
		if(isset($_POST['filename']) && isset($_POST['folder_id'])) {
		    // Get number of chunks with Ajax
		    $filename = $this->parseFilename($_POST['filename']);
			$folder_id = $_POST['folder_id'];

			if($filename !== false && is_numeric($folder_id)) {
				$path = $this->getUploadFolderPath($folder_id);
				if($path === false) {
					echo '0';
					exit;
				}

				$filepath = NOVA.'/'.$sharerID.'/'.$path.$filename;
			    if(file_exists($filepath)) {
			        $file = new \SplFileObject($filepath, 'r');
			        $file->seek(PHP_INT_MAX);

					if($file->current() === "EOF") { // A line with "EOF" at the end of the file when the file is complete
						echo $file->key()-1;
					} else {
						echo $file->key();
					}
				}
				else {
					echo '0';
				}
			}
			else {
				echo '0';
			}
		}
	}

};
?>
