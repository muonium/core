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
        parent::__construct([
            'mustBeLogged' => true,
            'mustBeValidated' => true
        ]);
    }

    function DefaultAction() {
        require_once(DIR_VIEW."User.php");
    }

    function getFolderVars() {
        // User sent folder_id, initialize model folders, check if folder exists and set folder_id and path in class attributes
        $this->_modelFolders = new m\Folders($_SESSION['id']);

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
            if(!is_numeric($folder_id)) return false;
            $path = $this->_modelFolders->getPath($folder_id);
            if($path === false) return false;
            $path .= $this->_modelFolders->getFoldername($folder_id);
            $this->_path = $path.'/';
            $this->_folderId = $folder_id;
        }
        return true;
    }

	function parseFilename($f) {
        $f = str_replace(['|', '/', '\\', ':', '*', '?', '<', '>', '"'], "", $f); // not allowed chars
		if(strlen($f) > 128) { // max length 128 chars
			$f = substr($f, 0, 128);
		}
		return $f;
	}

	function getUploadFolderPath($folder_id) {
		// Get the full path of an uploaded file until its folder using SESSION
		if(isset($_SESSION['upload'][$folder_id]['path'])) {
			return $_SESSION['upload'][$folder_id]['path'];
		}

		$this->_modelFolders = new m\Folders($_SESSION['id']);

		$path = $this->_modelFolders->getFullPath($folder_id);
		if($path === false || !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
			return false;
		}

		if($path != '') $path .= '/';
		$_SESSION['upload'][$folder_id]['path'] = $path;
		return $path;
	}

	function writeChunkAction() {
		// SESSION upload contains path for a folder id and its files uploaded during this session but only which doesn't exist or not complete

		function write($fpath, $data) {
			$data_length = strlen($data);
			if($_SESSION['size_stored']+$data_length > $_SESSION['user_quota']) {
				echo 'error';
			} else {
				$f = @fopen($fpath, "a");
				if($f === false || fwrite($f, $data) === false) {
					echo 'error';
				} else {
					$storage = new m\Storage($_SESSION['id']);
					if($storage->incrementSizeStored($data_length)) {
						$_SESSION['size_stored'] += $data_length;
					}
					echo 'ok';
				}
				fclose($f);
			}
		}

		if(isset($_POST['data']) && isset($_POST['filename']) && isset($_POST['folder_id'])) {
		    // Chunk sent by Ajax
		    $data = $_POST['data'];
			if($data !== 'EOF') $data .= "\r\n";
		    $filename = $this->parseFilename($_POST['filename']);
			$folder_id = $_POST['folder_id'];

			if($filename !== false && is_numeric($folder_id)) {
				if(isset($_SESSION['upload'][$folder_id]['files'][$filename]) && isset($_SESSION['upload'][$folder_id]['path'])) {
					// We have already write into this file in this session
					if($_SESSION['upload'][$folder_id]['files'][$filename] == 0 || $_SESSION['upload'][$folder_id]['files'][$filename] == 1) {
						$filepath = NOVA.'/'.$_SESSION['id'].'/'.$_SESSION['upload'][$folder_id]['path'].$filename;
						write($filepath, $data);
					}
				}
				else {
					// Write into a new file (which exists or not)
					$path = $this->getUploadFolderPath($folder_id);
					if($path === false) {
						echo 'error'; exit;
					}

					$filepath = NOVA.'/'.$_SESSION['id'].'/'.$path.$filename;
					$filestatus = $this->fileStatus($filepath);
					$_SESSION['upload'][$folder_id]['files'][$filename] = $filestatus;
					$_SESSION['upload'][$folder_id]['path'] = $path;

                    if($filestatus == 2) { // The file exists, exit
                        return;
                    }
					else {
						// The file doesn't exist or is not complete
						// Insert into files table if this file is not present
						$this->_modelFiles = new m\Files($_SESSION['id']);

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
						$this->_modelFiles = new m\Files($_SESSION['id']);
					}
					if(!isset($this->_modelFolders)) {
						$this->_modelFolders = new m\Folders($_SESSION['id']);
					}

					$this->_modelFiles->name = $filename;
					$this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$_SESSION['upload'][$folder_id]['path'].$filename);
					$this->_modelFiles->last_modification = time();

					if($this->_modelFiles->exists($filename, $folder_id)) {
						$this->_modelFiles->updateFile($folder_id, false);
					} else {
						$this->_modelFiles->addNewFile($folder_id, false);
					}

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

	function getFileStatusAction() {
        // Return a message/code according to file status
		// Client side : If the file exists, ask the user if he wants to replace it
		// Also check the quota
		if(isset($_POST['filesize']) && isset($_POST['filename']) && isset($_POST['folder_id'])) {
			// size_stored_tmp includes files currently uploading (new session variable because we can't trust a value sent by the client)
			// Used only to compare, if user sent a fake value, it will start uploading process but it will stop in the first chunk because we update size_stored for every chunk
			if(empty($_SESSION['size_stored_tmp'])) {
				$_SESSION['size_stored_tmp'] = $_SESSION['size_stored'];
			}

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
			else {
				echo 'err';
			}
		}
		else {
			echo 'err';
		}
	}

	function fileStatus($f) {
		// Returns 0 when the file doesn't exist, 1 when it exists and not complete, 2 when it exists and is complete
		if(file_exists($f)) {
		    $file = new \SplFileObject($f, 'r');
		    $file->seek(PHP_INT_MAX);
            $file->seek($file->key()); // Point to the last line

			if($file->current() === "EOF") { // A line with "EOF" at the end of the file when the file is complete
				return 2;
			}
			return '1@'.$file->key(); // Returns 1 (not complete) + last line number
		}
		return 0;
	}

    function AddFolderAction() {
        $this->getFolderVars();
        if(!empty($_POST['folder'])) {
            $folder = urldecode($_POST['folder']);
            $folder = $this->parseFilename($folder);
            if(strlen($folder) > 64) { // max length 64 chars
                $folder = substr($folder, 0, 64);
			}

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path) && !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$folder)) {
                $this->_modelFolders->name = $folder;
                $this->_modelFolders->parent = $this->_folderId;
                $this->_modelFolders->path = $this->_path;
                $this->_modelFolders->insertV();
                echo $this->_modelFolders->getLastInsertedId();
                mkdir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$folder, 0770);
                return;
            }
        }
        echo 'error';
    }

    function getTree() {
        $i = 0;
        $this->_modelFiles = new m\Files($_SESSION['id']);

        if(empty($this->_modelFolders)) {
            $this->_modelFolders = new m\Folders($_SESSION['id']);
        }

        $this->_modelStorage = new m\Storage($_SESSION['id']);
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();

		if($quota !== false && $stored !== false) {
			$_SESSION['size_stored'] = $stored;
			$_SESSION['user_quota'] = $quota;
		}

        // Link to parent folder
        echo '<div class="quota">';
        if($this->_folderId != 0) {
            $parent = $this->_modelFolders->getParent($this->_folderId);
            echo '<a id="parent-'.$parent.'" onclick="Folders.open('.$parent.')"><img src="'.IMG.'desktop/arrow.svg" class="icon"></a> ';
        }
        $pct = round($stored/$quota*100, 2);
        echo str_replace(['[used]', '[total]'], [$this->showSize($stored), $this->showSize($quota)], $this->txt->User->quota_of).' - '.$pct.'%';
        echo '<div class="progress_bar">';
        echo '<div class="used" style="width:'.$pct.'%"></div>';
        echo '</div></div>';

        echo '<hr><div id="tree"> ';

        $path = $this->_modelFolders->getFullPath($this->_folderId);

        if($subdirs = $this->_modelFolders->getChildren($this->_folderId, $this->trash)) {
            foreach($subdirs as $subdir) {
                $elementnum = count(glob(NOVA.'/'.$_SESSION['id'].'/'.$subdir['path'].$subdir['name']."/*"));
                $subdir['name'] = $this->parseFilename($subdir['name']);

                echo '<span class="folder" id="d'.$subdir['id'].'" name="'.htmlentities($subdir['name']).'"
                title="'.$this->showSize($subdir['size']).'"
                data-folder="'.htmlentities($subdir['parent']).'"
                data-path="'.htmlentities($subdir['path']).'"
                data-title="'.htmlentities($subdir['name']).'"
                onclick="Selection.addFolder(event, this.id)"
                ondblclick="Folders.open('.$subdir['id'].')">
                <img src="'.IMG.'desktop/extensions/folder.svg" class="icon"> <strong>'.htmlentities($subdir['name']).'</strong> [';
                if($elementnum > 1) {
    			    echo $elementnum.' '.$this->txt->User->PlurialElement.']</span>';
    			} else {
    				echo $elementnum.' '.$this->txt->User->element.']</span>';
    			}
            }
        }
        if($files = $this->_modelFiles->getFiles($this->_folderId, $this->trash)) {
            foreach($files as $file) {
                $fpath = $path;
                $file['name'] = $this->parseFilename($file['name']);
                if(array_key_exists('path', $file) && array_key_exists('dname', $file)) {
                    $fpath = $file['path'].$file['dname'];
                }
                if($file['size'] < 0) {
                    $filesize = '['.$this->txt->User->notCompleted.'] '.$this->showSize(@filesize(NOVA.'/'.$_SESSION['id'].'/'.$fpath.'/'.$file['name']));
                }
                else {
                    $filesize = $this->showSize($file['size']);
                }
                echo '<span class="file" id="f'.$file['id'].'"';
                if($file['size'] < 0) { echo ' style="color:red" '; }
                echo 'title="'.$filesize.'&#10;'.$this->txt->User->lastmod.' : '.date('d/m/Y G:i', $file['last_modification']).'"
                onclick="Selection.addFile(event, this.id)"
				ondblclick="Selection.dl(this.id)"
                data-folder="'.htmlentities($file['folder_id']).'"
                data-path="'.htmlentities($fpath).'"
                data-title="'.htmlentities($file['name']).'">';

				echo htmlentities($file['name']).'</span>';
            }
        }

        echo '</div>';
    }

    function ChangePathAction() {
        if(!isset($_POST['folder_id'])) {
            $folder_id = 0;
        } elseif(!is_numeric($_POST['folder_id'])) {
            return false;
        } else {
            $folder_id = urldecode($_POST['folder_id']);
		}

        $this->trash = empty($_POST['trash']) ? 0 : 1;

        if($folder_id == 0) {
            // root
            $this->_path = '';
            $this->_folderId = 0;
            $this->getTree();
        }
        else {
            $this->_modelFolders = new m\Folders($_SESSION['id']);

            $path = $this->_modelFolders->getPath($folder_id);
            if($path === false) return false;
            $path .= $this->_modelFolders->getFoldername($folder_id);

            if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path)) {
                $this->_path = $path;
                $this->_folderId = $folder_id;
                $this->getTree();
            }
        }
    }

    function FavoritesAction() {
        if(isset($_POST['id']) && is_numeric($_POST['id'])) {
            $id = $_POST['id'];
            $this->_modelFiles = new m\Files($_SESSION['id']);
            $this->_modelFiles->setFavorite($id);
        }
    }

    function MvTrashAction() {
        $this->_modelFiles = new m\Files($_SESSION['id']);
        $this->_modelFolders = new m\Folders($_SESSION['id']);

        $trash = 1;
        if(isset($_POST['trash']) && $_POST['trash'] == 0) $trash = 0;

        if(!empty($_POST['files'])) {
            $files = explode("|", $_POST['files']);
            foreach($files as $file) {
                if(is_numeric($file)) $this->_modelFiles->updateTrash($file, $trash);
            }
        }

        if(!empty($_POST['folders'])) {
            $folders = explode("|", $_POST['folders']);
            foreach($folders as $folder) {
                if(is_numeric($folder)) $this->_modelFolders->updateTrash($folder, $trash);
            }
        }
    }

    function rmFile($id, $path, $folder_id) {
		// $folder_id is used only to delete session var
        if(is_numeric($id)) {
            $filename = $this->_modelFiles->getFilename($id);
            if($filename !== false) {
                if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename)) {
					if(isset($_SESSION['upload'][$folder_id]['files'][$filename])) {
						unset($_SESSION['upload'][$folder_id]['files'][$filename]);
                    }
                    // deleteFile() returns file size
                    $fsize = $this->_modelFiles->deleteFile($id);
                    $completed = true;
                    if($fsize == -1) {
                        $completed = false;
                        $fsize = @filesize(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename);
                    }
                    unlink(NOVA.'/'.$_SESSION['id'].'/'.$path.$filename);
                    return [$fsize, $completed];
                }
            }
        }
        return [0, true];
    }

    function RmFilesAction() {
        $this->_modelFolders = new m\Folders($_SESSION['id']);
        $this->_modelFiles = new m\Files($_SESSION['id']);

        $total_size = 0;
        $tab_folders = []; // key : folder id, value : array ( path to folder, updated size )
        $path = '';

        if(isset($_POST['files']) && isset($_POST['ids'])) {
            $files = explode("|", urldecode($_POST['files']));
            $ids = explode("|", urldecode($_POST['ids']));

            $nbFiles = count($files);
            $nbIds = count($ids);

            if($nbFiles === $nbIds && $nbFiles > 0) {
                for($i = 0; $i < $nbFiles; $i++) {
                    $folder_id = $ids[$i];
                    if(array_key_exists($folder_id, $tab_folders)) {
                        $path = $tab_folders[$folder_id][0];
					}
                    else {
                        $path = $this->_modelFolders->getFullPath($folder_id);
                        if($path === false) continue;
                        $tab_folders[$folder_id][0] = $path;
                        $tab_folders[$folder_id][1] = 0;
                    }

                    $fsize = $this->rmFile($files[$i], $path.'/', $folder_id);
                    if(count($fsize) != 2) continue;
                    $size = $fsize[0];
                    if($fsize[1] === false) {
                        $total_size += $size;
                        // It's not necessary here to update folder size because when the file is not completed, the folder size wasn't updated
                    }
                    else {
                        $total_size += $size;
                        $tab_folders[$folder_id][1] += $size;
                    }
                }

                // Decrement storage counter
                $this->_modelStorage = new m\Storage($_SESSION['id']);
                if($this->_modelStorage->decrementSizeStored($total_size)) {
					$_SESSION['size_stored'] -= $total_size;
				}

                // Update folders size
                foreach($tab_folders as $key => $val) {
                    $this->_modelFolders->updateFoldersSize($key, -1*$val[1]);
				}
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
					if(isset($_SESSION['upload'][$id])) unset($_SESSION['upload'][$id]);
                    // Delete subfolders
                    if($subdirs = $this->_modelFolders->getChildren($id)) {
                        foreach($subdirs as $subdir) {
                            $this->rmRdir($subdir['id']);
						}
                    }

                    // Delete files
                    foreach(glob("{$full_path}/*") as $file) {
                        if(is_file($file)) unlink($file);
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
        if(!is_numeric($id)) return 0;

        $size = $this->_modelFolders->getSize($id);
        if($size === false) return 0;

        // Delete folder, files, subfolders and also files in db
        $this->rmRdir($id);

        // Delete folders and subfolders in db and update parents folder size
        $this->_modelFolders->delete($id);
        return $size;
    }

    function RmFoldersAction() {
        $this->_modelFolders = new m\Folders($_SESSION['id']);
        $this->_modelFiles = new m\Files($_SESSION['id']);

        $total_size = 0;
        $tab_folders = []; // key : folder id, value : updated size
        $path = '';

        if(isset($_POST['folders']) && isset($_POST['ids'])) {
            $folders = explode("|", urldecode($_POST['folders']));
            $ids = explode("|", urldecode($_POST['ids']));

            $nbFolders = count($folders);
            $nbIds = count($ids);

            if($nbFolders === $nbIds && $nbFolders > 0) {
                for($i = 0; $i < $nbFolders; $i++) {
                    $folder_id = $ids[$i];
                    if(!array_key_exists($folder_id, $tab_folders)) $tab_folders[$folder_id] = 0;
                    $size = $this->rmFolder($folders[$i]);
                    $total_size += $size;
                    $tab_folders[$folder_id] += $size;
                }

                // Decrement storage counter
                $this->_modelStorage = new m\Storage($_SESSION['id']);
                if($this->_modelStorage->decrementSizeStored($total_size)) {
					$_SESSION['size_stored'] -= $total_size;
				}

                // Update folders size
                foreach($tab_folders as $key => $val) {
                    $this->_modelFolders->updateFoldersSize($key, -1*$val);
				}
            }
        }
        echo 'done';
    }

    function RenameAction() {
        $this->_modelFiles = new m\Files($_SESSION['id']);
        $this->_modelFolders = new m\Folders($_SESSION['id']);

        if(isset($_POST['old']) && isset($_POST['new']) && isset($_POST['folder_id'])) {
            $folder_id = urldecode($_POST['folder_id']);
            if(!is_numeric($folder_id)) return false;
            $old = urldecode($_POST['old']);
            $new = urldecode($_POST['new']);
            $new = $this->parseFilename($new);

            if($old != $new && !empty($old) && !empty($new)) {
                $path = $this->_modelFolders->getFullPath($folder_id);
                if($path != '') $path .= '/';

                if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$old) && !is_dir(NOVA.'/'.$_SESSION['id'].'/'.$path.$new)) {
                    if(strlen($new) > 64) { // max folder length 64 chars
                        $new = substr($new, 0, 64);
					}
                    // Rename folder in db
                    $this->_modelFolders->rename($path, $old, $new);
                }
                elseif(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$old) && !file_exists(NOVA.'/'.$_SESSION['id'].'/'.$path.$new)) {
                    if(strlen($new) > 128) { // max file length 128 chars
                        $new = substr($new, 0, 128);
					}

                    // Rename file in db
					if(isset($_SESSION['upload'][$folder_id]['files'][$old])) {
						unset($_SESSION['upload'][$folder_id]['files'][$old]);
					}
                    $this->_modelFiles->rename($folder_id, $old, $new);
                }
                else {
                    return false;
                }

                rename(NOVA.'/'.$_SESSION['id'].'/'.$path.$old, NOVA.'/'.$_SESSION['id'].'/'.$path.$new);
				echo 'ok';
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
                if($i < 2) {
                    $name .= " ($i)";
                } else {
                    $pos = strrpos($name, "(");
                    if($pos === false) return false;
                    $name = substr($name, 0, $pos)."($i)";
                }
                $i++;
            }
        }
        elseif($type == 'file') {
            while(file_exists($path.$name)) {
                if($i < 2) {
                    $name = $this->addSuffixe($name, " ($i)");
                } else {
                    $first_pos = strrpos($name, "(");
                    $last_pos = strrpos($name, ")");
                    if($first_pos === false || $last_pos === false || $first_pos >= $last_pos) return false;
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
        if($src == 0) return false;
        $src_foldername = $this->_modelFolders->getFoldername($src);
        if($src_foldername === false) return false;
        $size = $this->_modelFolders->getSize($src);
        if($size === false) return false;
        $src_parent_path = $this->_modelFolders->getPath($src);

        $dst_parent_path = ($dst == 0) ? '' : $this->_modelFolders->getPath($dst);
        $dst_parent_name = ($dst == 0) ? '' : $this->_modelFolders->getFoldername($dst).'/';

        // Folder copies support
        $dst_foldername = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$dst_parent_path.$dst_parent_name, $src_foldername, 'folder');
        if($dst_foldername === false) return false;

        $this->_modelFolders->name = $dst_foldername;
        $this->_modelFolders->parent = $dst;
        $this->_modelFolders->path = $dst_parent_path.$dst_parent_name;
        $this->_modelFolders->size = $size;
        $this->_modelFolders->insertV();
        $folder_id = $this->_modelFolders->getLastInsertedId();

        $src_path = $src_parent_path.$src_foldername.'/';
        $dst_path = $this->_modelFolders->path.$dst_foldername;

        @mkdir(NOVA.'/'.$_SESSION['id'].'/'.$dst_path, 0770);

        if($subdirs = $this->_modelFolders->getChildren($src)) {
            foreach($subdirs as $subdir) {
                $this->recurse_copy($subdir['id'], $folder_id);
			}
        }
        if($files = $this->_modelFiles->getFiles($src)) {
            foreach($files as $file) {
                copy(NOVA.'/'.$_SESSION['id'].'/'.$src_path.$file['name'], NOVA.'/'.$_SESSION['id'].'/'.$dst_path.'/'.$file['name']);
                // Add the new file in db
                $this->_modelFiles->name = $file['name'];
                $this->_modelFiles->last_modification = time();
                $this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$dst_path.'/'.$file['name']);
                $this->_modelFiles->addNewFile($folder_id, false);
            }
        }
    }

    function addSuffixe($file, $suffixe) {
        $double_extensions = ['tar.gz', 'tar.bz', 'tar.xz', 'tar.bz2'];

        $pos = strpos($file, '.');
        if($pos === false) return $file.$suffixe;

        $pathinfo = pathinfo($file);
        if(empty($pathinfo['extension'])) return $file.$suffixe;

        $file_length = strlen($file);
        for($i = 0; $i < count($double_extensions); $i++) {
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
		$copy = (isset($_POST['copy']) && $_POST['copy'] == 1) ? 1 : 0;

        if(empty($_POST['files']) && empty($_POST['folders'])) return;

        $this->_modelFiles = new m\Files($_SESSION['id']);
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

            if($old_path === false) return false;
            $old_path .= $this->_modelFolders->getFoldername($old_folder_id).'/';
        }

        $this->_modelStorage = new m\Storage($_SESSION['id']);
        $quota = $this->_modelStorage->getUserQuota();
        $stored = $this->_modelStorage->getSizeStored();
		if($quota === false || $stored === false) return false;
		$_SESSION['size_stored'] = $stored;
		$_SESSION['user_quota'] = $quota;
        $uploaded = 0;

        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$this->_path) && is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path)) {
            if(!empty($_POST['files'])) {
                $files = explode("|", urldecode($_POST['files']));
                if($copy == 0 && $this->_path != $old_path) {
                    //
                    // cut and paste files
                    //
                    for($i = 0; $i < count($files); $i++) {
                        if(is_numeric($files[$i])) {
							$filename = $this->_modelFiles->getFilename($files[$i]);
                            if($filename === false) continue;
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename)) {
                                // Files copies support
                                $dst_filename = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$this->_path, $filename, 'file');
                                if($dst_filename === false) return false;
                                //
								if(isset($_SESSION['upload'][$old_folder_id]['files'][$filename])) {
									unset($_SESSION['upload'][$old_folder_id]['files'][$filename]);
								}
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
                    for($i=0; $i < count($files); $i++) {
                        if(is_numeric($files[$i])) {
							$filename = $this->_modelFiles->getFilename($files[$i]);
                            if($filename === false) continue;
                            if(file_exists(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename)) {
                                $this->_modelFiles->id = $files[$i];
                                $this->_modelFiles->size = filesize(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename);
                                if($stored+$this->_modelFiles->size <= $quota) {
                                    $stored += $this->_modelFiles->size;
                                    $uploaded += $this->_modelFiles->size;
                                    $this->_modelFiles->last_modification = time();

                                    // Files copies support
                                    $dst_filename = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$this->_path, $filename, 'file');
                                    if($dst_filename === false) return false;
                                    //
                                    $this->_modelFiles->name = $dst_filename;
                                    copy(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$filename, NOVA.'/'.$_SESSION['id'].'/'.$this->_path.$dst_filename);
                                    $this->_modelFiles->addNewFile($this->_folderId, false);
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
                    for($i=0; $i < count($folders); $i++) {
                        $foldername = $this->_modelFolders->getFolderName($folders[$i]);
                        if($foldername === false) continue;
                        if(is_dir(NOVA.'/'.$_SESSION['id'].'/'.$old_path.$foldername)) {
                            $folderSize = $this->_modelFolders->getSize($folders[$i]);
                            $old_parent = $this->_modelFolders->getParent($folders[$i]);

                            // Folder copies support
                            $dst_foldername = $this->checkMultiple(NOVA.'/'.$_SESSION['id'].'/'.$this->_path, $foldername, 'folder');
                            if($dst_foldername === false) return false;
                            //

							if(isset($_SESSION['upload'][$folders[$i]])) unset($_SESSION['upload'][$folders[$i]]);
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
                    for($i=0; $i < count($folders); $i++) {
                        $foldername = $this->_modelFolders->getFolderName($folders[$i]);
                        if($foldername === false) continue;
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

            if($this->_modelStorage->updateSizeStored($stored)) {
				$_SESSION['size_stored'] = $stored;
			}
            if($uploaded != 0) $this->_modelFolders->updateFoldersSize($this->_folderId, $uploaded);
        }
    }
}
