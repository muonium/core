<?php
namespace application\models;
use \library\MVC as l;

class Files extends l\Model {
    /* files table
        1   id                  int(11)     AUTO_INCREMENT
        2   id_owner            int(11)
        3   folder_id           bigint(20)
        4   name                varchar(128)
        5   size	            bigint(20)
        6   last_modification	int(11)
        7   favorite            tinyint(1)
        8   trash               tinyint(1)
        9   expires             int(11)
		10  dk					varchar(330)
    */

    protected $id = null;
    protected $id_owner = null;
    protected $folder_id;
    protected $name;
    protected $size;
    protected $last_modification;
    protected $favorite;
    protected $trash;
	protected $dk;

	function __construct($id_owner = null) {
		parent::__construct();
		// id_owner (int) can be passed at init
		$this->id_owner = $id_owner;
	}

	function exists($name, $folder_id) {
		// name (string) - Filename, folder_id (int) - Folder id
		// Returns true if the file exists for the defined owner, otherwise false
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT id FROM files WHERE id_owner = ? AND name = ? AND folder_id = ?");
        $req->execute([$this->id_owner, $name, $folder_id]);
        if($req->rowCount() === 0) return false;
        return true;
    }

	function isShared($name, $folder_id) {
		// name (string) - Filename, folder_id (int) - Folder id
		// Returns true if the file is shared, otherwise false
		if($this->id_owner === null) return false;
		$req = self::$_sql->prepare("SELECT id FROM files WHERE id_owner = ? AND name = ? AND folder_id = ? AND dk IS NOT NULL");
		$req->execute([$this->id_owner, $name, $folder_id]);
		if($req->rowCount() === 0) return false;
		return true;
	}

	function getInfos($id) {
		// Get infos from a shared file
		$req = self::$_sql->prepare("SELECT U.id, U.login, F.name, F.size, F.folder_id, F.id_owner, F.last_modification, F.dk
			FROM files F, users U WHERE F.id = ? AND F.id_owner = U.id AND F.trash = 0 AND F.expires IS NULL AND F.dk IS NOT NULL");
		$req->execute([$id]);
		if($req->rowCount() === 0) return false;
        return $req->fetch(\PDO::FETCH_ASSOC);
	}

    function getFilename($id = null) {
		// id (int) - File id
		// Returns filename, or false if it doesn't exist
		if($this->id_owner === null) return false;
		$id = ($id === null) ? $this->id : $id;
		if(!is_numeric($id)) return false;
        $req = self::$_sql->prepare("SELECT name FROM files WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['name'];
    }

    function getFolderId($id = null) {
		// id (int) - File id
		// Returns folder id of the file, or false if it doesn't exist
		if($this->id_owner === null) return false;
		$id = ($id === null) ? $this->id : $id;
		if(!is_numeric($id)) return false;
        $req = self::$_sql->prepare("SELECT folder_id FROM files WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['folder_id'];
    }

    function getFolderFromId($id = null) {
		// id (int) - File id
		// Returns folder id of the file, or false if it doesn't exist
		$id = ($id === null) ? $this->id : $id;
		if(!is_numeric($id))
            return false;
        $req = self::$_sql->prepare("SELECT folder_id FROM files WHERE id = ?");
        $req->execute([$id]);
        if($req->rowCount() === 0)
            return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['folder_id'];
    }

    function getSize($id = null) {
		// id (int) - File id (not necessary if name and folder id are defined)
		// Returns size of the file, 0 if it doesn't exist
		if($this->id_owner === null) return false;
        if($id === null && isset($this->name) && isset($this->folder_id)) {
            $req = self::$_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND name = ? AND folder_id = ?");
            $req->execute([$this->id_owner, $this->name, $this->folder_id]);
        }
		else {
			$id = ($id === null) ? $this->id : $id;
			if(!is_numeric($id)) return 0;
        	$req = self::$_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND id = ?");
        	$req->execute([$this->id_owner, $id]);
		}
        if($req->rowCount() === 0) return 0;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['size'];
    }

    function getFavorites() {
		// Returns an array containing favorites files for current user
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT name, id, size, last_modification, folder_id FROM files WHERE id_owner = ? AND favorite = 1");
        $req->execute([$this->id_owner]);
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    function getFiles($folder_id, $trash = null) {
		// folder_id (int) - Folder id, trash (not necessary, null/'all', 0 or 1) - Show from trash or not
		// Returns an array of files for a folder id, from trash if trash = 1
		if($this->id_owner === null) return false;
        if($trash === null || $trash === 'all') {
            $req = self::$_sql->prepare("SELECT name, id, size, last_modification, favorite, trash, folder_id, dk FROM files WHERE id_owner = ? AND folder_id = ? ORDER BY name ASC");
            $req->execute([$this->id_owner, $folder_id]);
        }
        elseif($trash == 0 || ($trash == 1 && $folder_id !== 0)) {
            $req = self::$_sql->prepare("SELECT name, id, size, last_modification, favorite, trash, folder_id, dk FROM files WHERE id_owner = ? AND folder_id = ? AND trash = 0 ORDER BY name ASC");
            $req->execute([$this->id_owner, $folder_id]);
        }
        else { // trash == 1 && $folder_id == 0
            $req = self::$_sql->prepare("SELECT files.name, files.id, files.size, files.last_modification, files.favorite, files.trash, files.folder_id, files.dk, folders.path, folders.name AS dname
				FROM files LEFT JOIN folders ON files.folder_id = folders.id WHERE files.id_owner = ? AND files.trash = 1 ORDER BY files.name ASC");
            $req->execute([$this->id_owner]);
        }
        if($req->rowCount() === 0) return false;

        // Example
        /*
            Array (
            	[0] => Array (
                	[name] => test.jpg, [id] => 1, [size] => 34, [last_modification] => 0 ...
				)
                [1] => Array (
                	[name] => a.png, [id] => 2, [size] => 30, [last_modification] => 0 ...
                )
            )
        */
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    function addNewFile($folder_id, $expires = true) {
		// folder_id (int) - Folder id, expires (not necessary) - If false, the file cannot expires if not completed
		// Insert a new file in the database, name, size, last_modification need to be set before !
		if($this->id_owner === null) return false;
        $expires = ($expires === false) ? null : time()+86400;
		if(!isset($this->last_modification)) $this->last_modification = time();
		if(isset($this->last_modification) && isset($this->name) && isset($this->size)) {
			return $this->insert('files', [
				'id' => null,
				'id_owner' => intval($this->id_owner),
				'folder_id' => intval($folder_id),
				'name' => $this->name,
				'size' => intval($this->size),
				'last_modification' => $this->last_modification,
				'favorite' => 0,
				'trash' => 0,
				'expires' => $expires
			]);
		}
        return false;
    }

    function updateTrash($id, $trash) {
		// id (int) - File id, trash (int) - Trash state
		// Update trash state for chosen file
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE files SET trash = ? WHERE id_owner = ? AND id = ?");
        return $req->execute([$trash, $this->id_owner, $id]);
    }

    function updateFile($folder_id, $expires = true) {
		// folder_id (int) - Folder id, expires (not necessary) - If false, the file cannot expires if not completed
        // Update file and returns the difference beetween the size of the new file and the size of the old file
		// name, size, last_modification need to be set before !
		if($this->id_owner === null) return false;
		if(!isset($this->last_modification)) $this->last_modification = time();
		if(isset($this->last_modification) && isset($this->name) && isset($this->size)) {
	        $this->folder_id = $folder_id; // Set current folder id, also needed for getSize
	        $old_size = $this->getSize();
	        if($expires === false) {
	            $req = self::$_sql->prepare("UPDATE files SET size = ?, last_modification = ?, expires = NULL WHERE id_owner = ? AND name = ? AND folder_id = ?");
	        }
	        else {
	            $req = self::$_sql->prepare("UPDATE files SET size = ?, last_modification = ? WHERE id_owner = ? AND name = ? AND folder_id = ?");
	        }
	        $req->execute([$this->size, $this->last_modification, $this->id_owner, $this->name, $folder_id]);
	        return (($this->size)-$old_size);
		}
		return false;
    }

    function updateDir() {
        // Update folder id and filename according to owner and file id
		// name, folder id and file id need to be set before !
		if($this->id_owner === null || !isset($this->folder_id) || !isset($this->name) || !isset($this->id)) return false;
        $req = self::$_sql->prepare("UPDATE files SET folder_id = ?, name = ? WHERE id_owner = ? AND id = ?");
        return $req->execute([$this->folder_id, $this->name, $this->id_owner, $this->id]);
    }

    // Not used for now
    function updateFolderId($old, $new) {
		// Update folder where the file is
		// old - Old folder id (int), new - New folder id (int)
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE files SET folder_id = ? WHERE id_owner = ? AND folder_id = ?");
        return $req->execute([$new, $this->id_owner, $old]);
    }

    function deleteFile($id) {
		// Delete chosen file
		// id - File id (int)
		if($this->id_owner === null) return false;
		$size = $this->getSize($id);
        if($size === false) return false;
        $req = self::$_sql->prepare("DELETE FROM files WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        return $size;
    }

    function deleteFiles($folder_id) {
		// Delete files from chosen folder
		// folder_id - Folder id (int)
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("DELETE FROM files WHERE id_owner = ? AND folder_id = ?");
        $ret = $req->execute([$this->id_owner, $folder_id]);
        return $ret;
    }

    function setFavorite($id) {
		// Set or unset file as favorite
		// id - File id (int)
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE files SET favorite = ABS(favorite-1) WHERE id_owner = ? AND id = ?");
        return $req->execute([$this->id_owner, $id]);
    }

	function setDK($id, $dk) {
		// Set a DK for a file in order to share it
		if($this->id_owner === null) return false;
		$req = self::$_sql->prepare("UPDATE files SET dk = ? WHERE id_owner = ? AND id = ? AND trash = 0");
        return $req->execute([$dk, $this->id_owner, $id]);
	}

    function rename($folder_id, $old, $new) {
		// Rename a file
		// folder_id - Folder id (int), old - Old name (string), new - New name (string)
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE files SET name = ? WHERE id_owner = ? AND folder_id = ? AND name = ?");
        return $req->execute([$new, $this->id_owner, $folder_id, $old]);
    }

    function getFullPath($id) {
        // Used for download feature
		// id - File id (int)
		if($this->id_owner === null || !is_numeric($id)) return false;
        $folder_id = $this->getFolderId($id);
        if($folder_id === false) return false;
        if($folder_id !== 0) {
            $req = self::$_sql->prepare("SELECT `path`, folders.name AS dname, files.name AS fname FROM files, folders
				WHERE files.id_owner = ? AND files.id = ? AND folders.id = ? AND folders.id_owner = files.id_owner");
            $req->execute([$this->id_owner, $id, $folder_id]);
            if($req->rowCount() === 0) return false;
            $res = $req->fetch(\PDO::FETCH_ASSOC);
            return NOVA.'/'.$this->id_owner.'/'.$res['path'].$res['dname'].'/'.$res['fname'];
        }
        $filename = $this->getFilename($id);
        if($filename === false) return false;
        return NOVA.'/'.$this->id_owner.'/'.$filename;
    }

	function deleteFilesfinal() {
		if($this->id_owner === null) return false;
		$req2 = self::$_sql->prepare("DELETE FROM files WHERE id_owner = ?");
		return $req2->execute([$this->id_owner]);
	}
}
