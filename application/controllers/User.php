<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class User extends l\Languages {

    private $_modelFiles;
    private $_modelFolders;
    private $_modelStorage;

    private $_filename = ''; // current file uploaded

    private $_path = ''; // current path
    private $_folderId = 0; // current folder id (0 = root)

    private $trash = 0; // 0 : view contents not in the trash || 1 : view contents in the trash

    function __construct() {
        parent::__construct();
        if(empty($_SESSION['id']))
            exit(header('Location: '.MVC_ROOT.'/Error/Error/404'));
        if(!empty($_SESSION['validate']))
            exit(header('Location: '.MVC_ROOT.'/Validate'));
    }

    function DefaultAction() {
        require_once(DIR_VIEW."vUser.php");
    }

    function getFolderVars() {
        // User sent folder_id, initialize model folders, check if folder exists and set folder_id and path in class attributes
        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];

        if(empty($_POST['folder_id'])) {
            $this->_path = '';
            $this->_folderId = 0;
        }
        else if($_POST['folder_id'] === 0) {
            $this->_path = '';
            $this->_folderId = 0;
        }
        else {
            $folder_id = urldecode($_POST['folder_id']);
            if(!is_numeric($folder_id))
                return false;
            $path = $this->_modelFolders->getPath($folder_id);
            if($path === false)
                return false;
            $path .= $this->_modelFolders->getFoldername($folder_id);
            $this->_path = $path.'/';
            $this->_folderId = $folder_id;
        }
        return true;
    }

	function parseFilename($f) {
		$f = str_replace("|", "", $f); // | is not allowed
		if(strlen($f) > 128) // max length 128 chars
			$f = substr($f, 0, 128);
		$forbidden = '/\\:*?<>|"';
		for($i=0;$i<count($forbidden);$i++)
			if(strpos($f, $forbidden[$i]))
				return false;
		return $f;
	}

	function getUploadFolderPath($folder_id) {
		// Get the full path of an uploaded file until its folder using SESSION
		if(isset($_SESSION['upload'][$folder_id]['path']))
			return $_SESSION['upload'][$folder_id]['path'];

		$this->_modelFolders = new m\Folders();
		$this->_modelFolders->id_owner = $_SESSION['id'];

		$path = $this->_modelFolders->getFullPath($folder_id);
		if($path === false || !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path))
			return false;

		if($path != '')
			$path = $path.'/';
		$_SESSION['upload'][$folder_id]['path'] = $path;
		return $path;
	}

	function writeChunkAction() {
		// SESSION upload contains path for a folder id and its files uploaded during this session but only which doesn't exist or not complete
		// TODO : Storage management (quota, size stored), insert file in DB

		function write($fpath, $data) {
			$data_length = strlen($data);
			if($_SESSION['size_stored']+$data_length > $_SESSION['user_quota'])
				echo 'error';
			else {
				$f = fopen($fpath, "a");
				if(fwrite($f, $data) === false)
					echo 'error';
				else {
					$Storage = new m\Storage();
	                $Storage->id_user = $_SESSION['id'];
					$Storage->incrementSizeStored($data_length); // SESSION size_stored is also incremented
					echo 'ok';
				}
				fclose($f);
			}
		}

		if(isset($_POST['data']) && isset($_POST['filename']) && isset($_POST['folder_id'])) {
		    // Chunk sent by Ajax
		    $data = $_POST['data'];
			if($data !== 'EOF')
				$data = $data."\r\n";
		    $filename = $this->parseFilename($_POST['filename']);
			$folder_id = $_POST['folder_id'];

			if($filename !== false && is_numeric($folder_id)) {
				if(isset($_SESSION['upload'][$folder_id]['files'][$filename]) && isset($_SESSION['upload'][$folder_id]['path'])) {
					// We have already write into this file in this session
					if($_SESSION['upload'][$folder_id]['files'][$filename] == 0) { // For now we write only in not created files TODO update soon
						$filepath = NOVA.'/'.$_SESSION['id'].'/'.$_SESSION['upload'][$folder_id]['path'].$filename;
						write($filepath, $data);
					}
				}
				else {
					// Write into a new file (which exists or not)
					$path = $this->getUploadFolderPath($folder_id);
					if($path === false) {
						echo 'error';
						exit;
					}

					$filepath = NOVA.'/'.$_SESSION['id'].'/'.$path.$filename;
					$filestatus = $this->fileStatus($filepath);
					$_SESSION['upload'][$folder_id]['files'][$filename] = $filestatus;
					$_SESSION['upload'][$folder_id]['path'] = $path;

					if($filestatus == 2) {
						// The file is complete, replace it ?
						// TODO action
					}
					else {
						// The file doesn't exist or is not complete

						// Insert into files table if this file is not present
						$this->_modelFiles = new m\Files();
		                $this->_modelFiles->id_owner = $_SESSION['id'];

						if(!($this->_modelFiles->exists($filename, $folder_id))) {
							$this->_modelFiles->name = $filename;
							$this->_modelFiles->size = -1;
							$this->_modelFiles->last_modification = time();
							$this->_modelFiles->addNewFile($folder_id);
						}

						write($filepath, $data);
					}
				}

				// End of file
				if($data === 'EOF' && isset($_SESSION['upload'][$folder_id]['files'][$filename]) && isset($_SESSION['upload'][$folder_id]['path'])) {
					// Update files table and folders size
					if(!isset($this->_modelFiles)) {
						$this->_modelFiles = new m\Files();
						$this->_modelFiles->id_owner = $_SESSION['id'];
					}

					if(!isset($this->_modelFolders)) {
						$this->_modelFolders = new m\Folders();
						$this->_modelFolders->id_owner = $_SESSION['id'];
					}

					$this->_modelFiles->name = $filename;
					$this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$_SESSION['upload'][$folder_id]['path'].$filename);
					$this->_modelFiles->last_modification = time();

					if($this->_modelFiles->exists($filename, $folder_id))
						$this->_modelFiles->updateFile($folder_id);
					else
						$this->_modelFiles->addNewFile($folder_id);

					$this->_modelFolders->updateFoldersSize($folder_id, $this->_modelFiles->size);

					// Remove the file from SESSION upload because the status is now complete
					unset($_SESSION['upload'][$folder_id]['files'][$filename]);
				}
			}
		}
	}

	function getChunkAction() {
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

				$filepath = NOVA.'/'.$_SESSION['id'].'/'.$path.$filename;

				$file = new \SplFileObject($filepath, 'r');
			    $file->seek($line);

			    echo str_replace("\r\n", "", $file->current());
			}
		}
	}

	function getNbChunksAction() {
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

				$filepath = NOVA.'/'.$_SESSION['id'].'/'.$path.$filename;

			    if(file_exists($filepath)) {
			        $file = new \SplFileObject($filepath, 'r');
			        $file->seek(PHP_INT_MAX);

					if($file->current() === "EOF") // A line with "EOF" at the end of the file when the file is complete
						echo $file->key()-1;
					else
						echo $file->key();
				}
				else
					echo '0';
			}
			else
				echo '0';
		}
	}

	function getFileStatusAction() {
		// If the file exists, ask the user if he wants to replace it
		// Also check the quota
		if(isset($_POST['filesize']) && isset($_POST['filename']) && isset($_POST['folder_id'])) {
			// size_stored_tmp includes files currently uploading (new session variable because we can't trust a value sent by the client)
			// Used only to compare, if user sent a fake value, it will start uploading process but it will stop in the first chunk because we update size_stored for every chunk
			if(empty($_SESSION['size_stored_tmp']))
				$_SESSION['size_stored_tmp'] = $_SESSION['size_stored'];

			$filename = $this->parseFilename($_POST['filename']);
			$folder_id = $_POST['folder_id'];
			$filesize = $_POST['filesize'];

			if($filename !== false && is_numeric($folder_id) && is_numeric($filesize)) {
				if($_SESSION['size_stored_tmp']+$filesize > $_SESSION['user_quota']) {
					echo 'quota';
					exit;
				}
				$_SESSION['size_stored_tmp'] += $filesize;

				$path = $this->getUploadFolderPath($folder_id);
				if($path === false) {
					echo '0';
					exit;
				}

				$filepath = NOVA.'/'.$_SESSION['id'].'/'.$path.$filename;
				echo $this->filestatus($filepath);
			}
			else
				echo 'err';
		}
		else
			echo 'err';
	}

	function fileStatus($f) {
		// Returns 0 when the file doesn't exist, 1 when it exists and not complete, 2 when it exists and is complete
		if(file_exists($f)) {
		    $file = new \SplFileObject($f, 'r');
		    $file->seek(PHP_INT_MAX);

			if($file->current() === "EOF") // A line with "EOF" at the end of the file when the file is complete
				return 2;
			else
				return 1;
		}
		else
			return 0;
	}

    function AddFolderAction() {
        $this->getFolderVars();
        if(!empty($_POST['folder'])) {
            $folder = urldecode($_POST['folder']);
            if(strlen($folder) > 64) // max length 64 chars
                $folder = substr($folder, 0, 64);

            $forbidden = '/\\:*?<>|"';

            $f = 0;
            for($i=0;$i<count($forbidden);$i++) {
                if(strpos($folder, $forbidden[$i])) {
                    $f = 1; // Forbidden char found
                    break;
                }
            }

            if($f == 0) {
                if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path) && !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$folder)) {
                    $this->_modelFolders->name = $folder;
                    $this->_modelFolders->parent = $this->_folderId;
                    $this->_modelFolders->path = $this->_path;
                    $this->_modelFolders->insert();
                    echo $this->_modelFolders->getLastInsertedId();
                    mkdir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$folder, 0770);
                    return;
                }
            }
        }
        echo 'error';
    }

    function getTree() {
        $i = 0;
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        if(empty($this->_modelFolders)) {
            $this->_modelFolders = new m\Folders();
            $this->_modelFolders->id_owner = $_SESSION['id'];
        }

        $this->_modelStorage = new m\Storage();
        $this->_modelStorage->id_user = $_SESSION['id'];
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();

        $time_start = microtime(true);

        // Link to parent folder
        echo '<p>';
        if($this->_folderId != 0) {
            $parent = $this->_modelFolders->getParent($this->_folderId);
            echo '<a id="parent-'.$parent.'" ondblclick="Folders.open('.$parent.')"><img src="'.IMG.'desktop/arrow.svg" class="icon"></a> ';
        }
        echo ' ['.$this->showSize($stored).'/'.$this->showSize($quota).']</p>';

        echo '<hr><div id="tree"> ';

        // New way
        $path = $this->_modelFolders->getFullPath($this->_folderId);
        if($subdirs = $this->_modelFolders->getChildren($this->_folderId, $this->trash)) {
            foreach($subdirs as $subdir)
		$subfoldernum=$this->_modelFolders->getSubfoldernum($subdir['0']);
       			 $filenum=$this->_modelFiles->getFilesnum($subdir['0']);
       			 $elementnum=$subfoldernum+$filenum;
                echo '<span class="folder" id="d'.$subdir['0'].'" name="'.htmlentities($subdir['1']).'" data-folder="'.htmlentities($subdir['3']).'" data-path="'.htmlentities($subdir['4']).'" onclick="Selection.addFolder(this.id)" ondblclick="Folders.open('.$subdir['0'].')"><img src="'.IMG.'desktop/extensions/folder.svg" class="icon"> <strong>'.htmlentities($subdir['1']).'</strong> ['.$this->showSize($subdir['2']).'][';
                if ($elementnum>1){
					echo $elementnum.$this->txt->User->PlurialElement.']</span>';
				}else{
					echo $elementnum.$this->txt->User->element.']</span>';
				}
        }
        if($files = $this->_modelFiles->getFiles($this->_folderId, $this->trash)) {
            foreach($files as $file) {
                $fpath = $path;
                if(array_key_exists(7, $file) && array_key_exists(8, $file))
                    $fpath = $file['7'].$file['8'];
                echo '<span class="file" id="f'.$file['1'].'" title="'.$this->txt->User->lastmod.' : '.date('d/m/Y G:i', $file['3']).'" onclick="Selection.addFile(this.id)" data-folder="'.htmlentities($file['6']).'" data-path="'.htmlentities($fpath).'" data-title="'.htmlentities($file['0']).'">';
				// showSize with an array containing path and file name because the size in database can be -1 (file currently uploading), in this case showSize will display size with filesize()
				echo htmlentities($file['0']).' ['.$this->showSize($file['2'], array($file['0'], $fpath)).']</span>';
            }
        }

        $time_end = microtime(true);
        echo '</div><br />'.$this->txt->User->loaded.' '.($time_end-$time_start).' s';
    }

    function ChangePathAction() {
        if(!isset($_POST['folder_id']))
            $folder_id = 0;
        elseif(!is_numeric($_POST['folder_id']))
            return false;
        else
            $folder_id = urldecode($_POST['folder_id']);

        if(empty($_POST['trash']))
            $this->trash = 0;
        else
            $this->trash = 1;

        if($folder_id == 0) {
            // root
            $this->_path = '';
            $this->_folderId = 0;
            $this->getTree();
        }
        else {
            $this->_modelFolders = new m\Folders();
            $this->_modelFolders->id_owner = $_SESSION['id'];

            $path = $this->_modelFolders->getPath($folder_id);

            if($path === false)
                return false;
            $path .= $this->_modelFolders->getFoldername($folder_id);

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $this->_path = $path;
                $this->_folderId = $folder_id;
                $this->getTree();
            }
        }
    }

    function FavoritesAction() {
        if(isset($_POST['id'])) {
            if(is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                $this->_modelFiles = new m\Files();
                $this->_modelFiles->id_owner = $_SESSION['id'];
                $this->_modelFiles->setFavorite($id);
            }
        }
    }

    function MvTrashAction() {
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];

        $trash = 1;
        if(isset($_POST['trash']))
            if($_POST['trash'] == 0)
                $trash = 0;

        if(!empty($_POST['files'])) {
            $files = explode("|", $_POST['files']);
            foreach($files as $file) {
                if(is_numeric($file))
                    $this->_modelFiles->updateTrash($file, $trash);
            }
        }

        if(!empty($_POST['folders'])) {
            $folders = explode("|", $_POST['folders']);
            foreach($folders as $folder) {
                if(is_numeric($folder))
                    $this->_modelFolders->updateTrash($folder, $trash);
            }
        }
    }

    function rmFile($id, $path, $folder_id) {
		// $folder_id is used only to delete session var
        if(is_numeric($id)) {
            $filename = $this->_modelFiles->getFilename($id);
            if($filename !== false) {
                if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename)) {
					if(isset($_SESSION['upload'][$folder_id]['files'][$filename]))
						unset($_SESSION['upload'][$folder_id]['files'][$filename]);
                    unlink(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename);
                    // deleteFile() returns file size
                    return $this->_modelFiles->deleteFile($id);
                }
            }
        }
        return 0;
    }

    function RmFilesAction() {
        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];

        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        $total_size = 0;
        $tab_folders = array(); // key : folder id, value : array ( path to folder, updated size )
        $path = '';

        if(isset($_POST['files']) && isset($_POST['ids'])) {
            $files = explode("|", urldecode($_POST['files']));
            $ids = explode("|", urldecode($_POST['ids']));

            $nbFiles = count($files);
            $nbIds = count($ids);

            if($nbFiles == $nbIds && $nbFiles > 0) {
                for($i = 0; $i < $nbFiles; $i++) {
                    $folder_id = $ids[$i];
                    if(array_key_exists($folder_id, $tab_folders))
                        $path = $tab_folders[$folder_id][0];
                    else {
                        $path = $this->_modelFolders->getFullPath($folder_id);
                        if($path === false)
                            continue;
                        $tab_folders[$folder_id][0] = $path;
                        $tab_folders[$folder_id][1] = 0;
                    }

                    $size = $this->rmFile($files[$i], $path.'/', $folder_id);
                    $total_size += $size;
                    $tab_folders[$folder_id][1] += $size;
                }

                // Decrement storage counter
                $this->_modelStorage = new m\Storage();
                $this->_modelStorage->id_user = $_SESSION['id'];
                $this->_modelStorage->decrementSizeStored($total_size);

                // Update folders size
                foreach($tab_folders as $key => $val)
                    $this->_modelFolders->updateFoldersSize($key, -1*$val[1]);
            }
        }
        echo 'done';
    }

    function rmRdir($id) {
        // This is a recursive method
        if(is_numeric($id)) {
            $path = $this->_modelFolders->getFullPath($id);
            if($path !== false) {
                $full_path = NOVA.'/'.$_SESSION['id'].'/'.$path;
                if(is_dir($full_path)) {
					if(isset($_SESSION['upload'][$id]))
						unset($_SESSION['upload'][$id]);
                    // Delete subfolders
                    if($subdirs = $this->_modelFolders->getChildren($id)) {
                        foreach($subdirs as $subdir)
                            $this->rmRdir($subdir['0']);
                    }

                    // Delete files
                    foreach(glob("{$full_path}/*") as $file) {
                        if(is_file($file))
                            unlink($file);
                    }

                    // Delete files in db
                    $this->_modelFiles->deleteFiles($id);

                    // Delete folder
                    rmdir($full_path);
                }
            }
        }
    }

    function rmFolder($id) {
        if(!is_numeric($id))
            return 0;

        $size = $this->_modelFolders->getSize($id);
        if($size === false)
            return 0;

        // Delete folder, files, subfolders and also files in db
        $this->rmRdir($id);

        // Delete folders and subfolders in db and update parents folder size
        $this->_modelFolders->delete($id);
        return $size;
    }

    function RmFoldersAction() {
        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];

        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        $total_size = 0;
        $tab_folders = array(); // key : folder id, value : updated size
        $path = '';

        if(isset($_POST['folders']) && isset($_POST['ids'])) {
            $folders = explode("|", urldecode($_POST['folders']));
            $ids = explode("|", urldecode($_POST['ids']));

            $nbFolders = count($folders);
            $nbIds = count($ids);

            if($nbFolders == $nbIds && $nbFolders > 0) {
                for($i = 0; $i < $nbFolders; $i++) {
                    $folder_id = $ids[$i];
                    if(!array_key_exists($folder_id, $tab_folders))
                        $tab_folders[$folder_id] = 0;
                    $size = $this->rmFolder($folders[$i]);
                    $total_size += $size;
                    $tab_folders[$folder_id] += $size;
                }

                // Decrement storage counter
                $this->_modelStorage = new m\Storage();
                $this->_modelStorage->id_user = $_SESSION['id'];
                $this->_modelStorage->decrementSizeStored($total_size);

                // Update folders size
                foreach($tab_folders as $key => $val)
                    $this->_modelFolders->updateFoldersSize($key, -1*$val);
            }
        }
        echo 'done';
    }

    function showSize($size, $file_info = null, $precision = 2) {
        // $size => size in bytes
        if(!is_numeric($size))
            return 0;
        if($size < 0 && is_array($file_info)) // File not completely uploaded
            return '<span style="display:inline;color:red">'.$this->showSize(@filesize(NOVA.'/'.$_SESSION['id'].'/'.$file_info['1'].'/'.$file_info['0'])).'</span>';
		if($size <= 0)
			return 0;
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

    function RenameAction() {
        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];

        $this->_modelFolders = new m\Folders();
        $this->_modelFolders->id_owner = $_SESSION['id'];

        if(isset($_POST['old']) && isset($_POST['new']) && isset($_POST['path'])) {
            $path = urldecode($_POST['path']);
            if(!empty($path))
                $path .= '/';
            $old = urldecode($_POST['old']);
            $new = urldecode($_POST['new']);

            if($old != $new && !empty($old) && !empty($new)) {
                $forbidden = '/\\:*?<>|"';

                $f = 0;
                for($i=0;$i<count($forbidden);$i++) {
                    if(strpos($new, $forbidden[$i])) {
                        $f = 1; // Forbidden char found
                        break;
                    }
                }

                if($f == 0) {
                    if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$old) && !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$new)) {
                        if(strlen($new) > 64) // max folder length 64 chars
                            $new = substr($new, 0, 64);
                        // Rename folder in db
                        $this->_modelFolders->rename($path, $old, $new);
                    }
                    elseif(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$old) && !file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$new)) {
                        if(strlen($new) > 128) // max file length 128 chars
                            $new = substr($new, 0, 128);
                        // Rename file in db
                        $folder_id = 0;
                        if(!empty($path)) {
                            $folder_id = $this->_modelFolders->getId($path);
                            if($folder_id === false)
                                return false;
                        }

						if(isset($_SESSION['upload'][$folder_id]['files'][$old]))
							unset($_SESSION['upload'][$folder_id]['files'][$old]);
                        $this->_modelFiles->rename($folder_id, $old, $new);
                    }
                    else
                        return false;

                    rename(NOVA.'/'.$_SESSION['id'].'/'.$path.$old, NOVA.'/'.$_SESSION['id'].'/'.$path.$new);
					echo 'ok';
                }
            }
        }
    }

    // Check if there are multiple versions of a file or a folder and update the name.
    // Folder, Folder (1), Folder (2)...
    // File.ext, File (1).ext, File (2).ext...
    function checkMultiple($path, $name, $type) {
        $i = 1;
        if($type == 'folder') {
            while(is_dir($path.$name)) {
                if($i < 2)
                    $name .= " ($i)";
                else {
                    $pos = strrpos($name, "(");
                    if($pos === false)
                        return false;
                    $name = substr($name, 0, $pos)."($i)";
                }
                $i++;
            }
        }
        elseif($type == 'file') {
            while(file_exists($path.$name)) {
                if($i < 2)
                    $name = $this->addSuffixe($name, " ($i)");
                else {
                    $first_pos = strrpos($name, "(");
                    $last_pos = strrpos($name, ")");
                    if($first_pos === false || $last_pos === false || $first_pos >= $last_pos)
                        return false;
                    $name = substr($name, 0, $first_pos)."($i)".substr($name, $last_pos+1);
                }
                $i++;
            }
        }
        return $name;
    }

    // $src is the folder id of source folder
    // $dst is the folder id of dest folder where $src folder will be pasted
    function recurse_copy($src, $dst) {
        // This is a recursive method
        // Thank you "gimmicklessgpt at gmail dot com" from php.net for the base code
        // recurse_copy add also new files in db
        if($src == 0)
            return false;
        $src_foldername = $this->_modelFolders->getFoldername($src);
        if($src_foldername === false)
            return false;
        $size = $this->_modelFolders->getSize($src);
        if($size === false)
            return false;
        $src_parent_path = $this->_modelFolders->getPath($src);

        if($dst == 0) {
            $dst_parent_path = '';
            $dst_parent_name = '';
        }
        else {
            $dst_parent_path = $this->_modelFolders->getPath($dst);
            $dst_parent_name = $this->_modelFolders->getFoldername($dst).'/';
        }

        // Folder copies support
        $dst_foldername = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$dst_parent_path, $src_foldername, 'folder');
        if($dst_foldername === false)
            return false;
        //
        $this->_modelFolders->name = $dst_foldername;
        $this->_modelFolders->parent = $dst;
        $this->_modelFolders->path = $dst_parent_path.$dst_parent_name;
        $this->_modelFolders->size = $size;
        $this->_modelFolders->insert();
        $folder_id = $this->_modelFolders->getLastInsertedId();
        //

        $src_path = $src_parent_path.$src_foldername.'/';
        $dst_path = $this->_modelFolders->path.$dst_foldername;

        @mkdir(NOVA.'/'.$_SESSION['id'].'/'.$dst_path, 0770);

        if($subdirs = $this->_modelFolders->getChildren($src)) {
            foreach($subdirs as $subdir)
                $this->recurse_copy($subdir['0'], $folder_id);
        }
        if($files = $this->_modelFiles->getFiles($src)) {
            foreach($files as $file) {
                copy(NOVA.'/'.$_SESSION['id'].'/'.$src_path.$file['0'], NOVA.'/'.$_SESSION['id'].'/'.$dst_path.'/'.$file['0']);
                // Add the new file in db
                $this->_modelFiles->name = $file['0'];
                $this->_modelFiles->last_modification = time();
                $this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$dst_path.'/'.$file['0']);
                $this->_modelFiles->addNewFile($folder_id);
            }
        }
    }

    function addSuffixe($file, $suffixe) {
        $double_extensions = array(
            'tar.gz',
            'tar.bz',
            'tar.xz',
            'tar.bz2'
        );

        $pos = strpos($file, '.');
        if($pos === false)
            return $file.$suffixe;

        $pathinfo = pathinfo($file);
        if(empty($pathinfo['extension']))
            return $file.$suffixe;

        $file_length = strlen($file);
        for($i=0;$i<count($double_extensions);$i++) {
            $length = strlen($double_extensions[$i])+1;
            if($file_length > $length) {
                $end = substr($file, -1*$length);
                if('.'.$double_extensions[$i] == $end) {
                    $start = substr($file, 0, $file_length-$length);
                    return $start.$suffixe.$end;
                }
            }
        }

        return $pathinfo['filename'].$suffixe.'.'.$pathinfo['extension'];
    }

    function MvAction() {
        $this->getFolderVars();
        // $copy : 0 => cut, 1 => copy

        if(!isset($_POST['copy']))
            $copy = 0;
        else {
            if($_POST['copy'] == 1)
                $copy = 1;
            else
                $copy = 0;
        }

        if(empty($_POST['files']) && empty($_POST['folders']))
            return;

        $this->_modelFiles = new m\Files();
        $this->_modelFiles->id_owner = $_SESSION['id'];
        $this->_modelFiles->folder_id = $this->_folderId;

        if(empty($_POST['old_folder_id'])) {
            $old_folder_id = 0;
            $old_path = '';
        }
        else if($_POST['old_folder_id'] === 0) {
            $old_folder_id = 0;
            $old_path = '';
        }
        else {
            $old_folder_id = urldecode($_POST['old_folder_id']);
            $old_path = $this->_modelFolders->getPath($old_folder_id);

            if($old_path === false)
                return false;
            $old_path .= $this->_modelFolders->getFoldername($old_folder_id).'/';
        }

        $this->_modelStorage = new m\Storage();
        $this->_modelStorage->id_user = $_SESSION['id'];
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();
        $uploaded = 0;

        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path) && is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path)) {

            if(!empty($_POST['files'])) {
                $files = explode("|", urldecode($_POST['files']));
                if($copy == 0 && $this->_path != $old_path) {
                    //
                    // cut and paste files
                    //
                    for($i=0;$i<count($files);$i++) {
                        if(is_numeric($files[$i])) {
                            if(!($filename = $this->_modelFiles->getFilename($files[$i])))
                                continue;
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename)) {

                                // Files copies support
                                $dst_filename = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$this->_path, $filename, 'file');
                                if($dst_filename === false)
                                    return false;
                                //
								if(isset($_SESSION['upload'][$old_folder_id]['files'][$filename]))
									unset($_SESSION['upload'][$old_folder_id]['files'][$filename]);
                                rename(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$dst_filename);
                                $this->_modelFiles->id = $files[$i];
                                $this->_modelFiles->name = $dst_filename;
                                $uploaded += filesize(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$dst_filename);
                                $this->_modelFiles->updateDir();
                            }
                        }
                    }
                    // Update parent folders size
                    $this->_modelFolders->updateFoldersSize($old_folder_id, -1*$uploaded);
                }
                elseif($copy == 1) {
                    //
                    // copy and paste files
                    //
                    for($i=0;$i<count($files);$i++) {
                        if(is_numeric($files[$i])) {
                            if(!($filename = $this->_modelFiles->getFilename($files[$i])))
                                continue;
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename)) {
                                $this->_modelFiles->id = $files[$i];
                                $this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename);
                                if($stored+$this->_modelFiles->size <= $quota) {
                                    $stored += $this->_modelFiles->size;
                                    $uploaded += $this->_modelFiles->size;
                                    $this->_modelFiles->last_modification = time();

                                    // Files copies support
                                    $dst_filename = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$this->_path, $filename, 'file');
                                    if($dst_filename === false)
                                        return false;
                                    //
                                    $this->_modelFiles->name = $dst_filename;
                                    copy(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$dst_filename);
                                    $this->_modelFiles->addNewFile($this->_folderId);
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($_POST['folders'])) {
                $folders = explode("|", urldecode($_POST['folders']));
                if($copy == 0 && $this->_path != $old_path) {
                    //
                    // cut and paste folders
                    //
                    for($i=0;$i<count($folders);$i++) {
                        $foldername = $this->_modelFolders->getFolderName($folders[$i]);
                        if($foldername === false)
                            continue;
                        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername)) {
                            $folderSize = $this->_modelFolders->getSize($folders[$i]);
                            $old_parent = $this->_modelFolders->getParent($folders[$i]);

                            // Folder copies support
                            $dst_foldername = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$this->_path, $foldername, 'folder');
                            if($dst_foldername === false)
                                return false;
                            //

							if(isset($_SESSION['upload'][$folders[$i]]))
								unset($_SESSION['upload'][$folders[$i]]);
                            rename(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$dst_foldername);
                            $this->_modelFolders->name = $dst_foldername;
                            $this->_modelFolders->updateParent($folders[$i], $this->_folderId);
                            $this->_modelFolders->updatePath($folders[$i], $this->_path, $dst_foldername);

                            // Update parent folders size
                            $this->_modelFolders->updateFoldersSize($old_parent, -1*$folderSize);
                            $uploaded += $folderSize;
                        }
                    }
                }
                elseif($copy == 1) {
                    //
                    // copy and paste folders
                    //
                    for($i=0;$i<count($folders);$i++) {
                        $foldername = $this->_modelFolders->getFolderName($folders[$i]);
                        if($foldername === false)
                            continue;
                        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername)) {
                            $folderSize = $this->_modelFolders->getSize($folders[$i]);
                            if($stored+$folderSize <= $quota) {
                                $stored += $folderSize;
                                $uploaded += $folderSize;
                                // recurse_copy add also new files and subfolders in db
                                $this->recurse_copy($folders[$i], $this->_folderId);
                            }
                        }
                    }
                }
            }

            $this->_modelStorage->updateSizeStored($stored);
            if($uploaded != 0)
                $this->_modelFolders->updateFoldersSize($this->_folderId, $uploaded);
        }
    }
}
