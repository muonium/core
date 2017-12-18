<?php
namespace application\models;
use \library\MVC as l;

class Folders extends l\Model {
    /* folders table
        1   id                  int(11)     AUTO_INCREMENT
        2   id_owner            int(11)
        3   name                varchar(128)
        4   size                bigint(20)
        5   parent              bigint(20)
        6   trash               tinyint(1)
        7   path	            text
    */

    protected $id = null;
    protected $id_owner = null;
    protected $name;
    protected $size = 0;
    protected $parent = 0;
    protected $trash;
    protected $path;

	function __construct($id_owner = null) {
		parent::__construct();
		// id_owner (int) can be passed at init
		$this->id_owner = $id_owner;
	}

    function getId($path) {
		// path (string) - Returns folder id from path
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT id FROM folders WHERE id_owner = ? AND `path` = ?");
        $req->execute([$this->id_owner, $path]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['id'];
    }

    function getFoldername($id) {
		// id (int) - Returns folder name from its id
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT name FROM folders WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['name'];
    }

    function getPath($id) {
		// id (int) - Returns folder path from its id (without folder name)
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT `path` FROM folders WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['path'];
    }

    function getFullPath($id) {
        // id (int) - Returns folder path with folder name included
		if($this->id_owner === null) return false;
		$id = intval($id);
		if($id === 0) return '';
        $req = self::$_sql->prepare("SELECT `path`, name FROM folders WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
		if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['path'].$res['name'];
    }

    function getParent($id) {
		// id (int) - Returns parent id from folder id
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT parent FROM folders WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['parent'];
    }

    // Not used for now, get number of subfolders
	function getSubfoldernum($id) {
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT COUNT(id) AS nb FROM folders WHERE id_owner = ? AND parent = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['nb'];
    }

    function getChildren($id, $trash = '') {
		// id (int) - Folder id, trash - Get from anywhere/trash/outside trash
		if($this->id_owner === null) return false;
        if($trash === '' || $trash === 'all') {
            $req = self::$_sql->prepare("SELECT id, name, size, parent, `path` FROM folders WHERE id_owner = ? AND parent = ? ORDER BY name ASC");
            $req->execute([$this->id_owner, $id]);
        }
        elseif($trash == 0 || ($trash == 1 && $id !== 0)) {
            $req = self::$_sql->prepare("SELECT id, name, size, parent, `path` FROM folders WHERE id_owner = ? AND parent = ? AND trash = 0 ORDER BY name ASC");
            $req->execute([$this->id_owner, $id]);
        }
        else { // trash == 1 && $id == 0
            $req = self::$_sql->prepare("SELECT id, name, size, parent, `path` FROM folders WHERE id_owner = ? AND trash = 1 ORDER BY name ASC");
            $req->execute([$this->id_owner]);
        }
        if($req->rowCount() === 0) return false;
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    function getTrash($id) {
		// id (int) - Folder id, Get trash state for selected folder
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT trash FROM folders WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['trash'];
    }

    function getSize($id) {
		// id (int) - Get folder size
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("SELECT size FROM folders WHERE id_owner = ? AND id = ?");
        $req->execute([$this->id_owner, $id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['size'];
    }

    function updateTrash($id, $trash) {
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE folders SET trash = ? WHERE id_owner = ? AND id = ?");
        return $req->execute([$trash, $this->id_owner, $id]);
    }

    // Update the size of the folder with an increment of $size
    function updateFolderSize($id, $size) {
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE folders SET size = size + ? WHERE id_owner = ? AND id = ?");
        return $req->execute([$size, $this->id_owner, $id]);
    }

    // Update the size of folder and each parent folder until root with an increment of $size
    function updateFoldersSize($id, $size) {
        do {
            if(!($this->updateFolderSize($id, $size))) break;
            $id = $this->getParent($id);
        } while($id != 0 && $id !== false);
    }

    // Update path of a folder and its subfolders
    // Maybe better to use UPDATE with LIKE ?
    function updatePath($id, $path, $foldername) {
		if($this->id_owner === null) return false;
        $subdirs = $this->getChildren($id);
        if($subdirs !== false) {
            foreach($subdirs as $subdir) {
                $this->updatePath($subdir['id'], $path.$foldername.'/', $subdir['name']);
			}
        }
        $req = self::$_sql->prepare("UPDATE folders SET `path` = ? WHERE id_owner = ? AND id = ?");
        $req->execute([$path, $this->id_owner, $id]);
    }

    // Experimental method, rename folder which has specified path
    function rename($path, $old, $new) {
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE folders SET name = ? WHERE id_owner = ? AND name = ? AND `path` = ?");
        $req->execute([$new, $this->id_owner, $old, $path]);
		// Children
        $req = self::$_sql->prepare("UPDATE folders SET `path` = CONCAT(?, SUBSTR(`path`, ?)) WHERE id_owner = ? AND `path` LIKE ?");
        $req->execute([$path.$new, strlen($path.$old)+1, $this->id_owner, $path.$old.'%']);
    }

    function updateParent($id, $parent) {
		// Update folder parent and also folder name
		if($this->id_owner === null) return false;
        $req = self::$_sql->prepare("UPDATE folders SET parent = ?, name = ? WHERE id_owner = ? AND id = ?");
        return $req->execute([$parent, $this->name, $this->id_owner, $id]);
    }

    function addFolder() {
		if($this->id_owner === null) return false;
		if(isset($this->name) && isset($this->size) && isset($this->parent) && isset($this->path)) {
			return $this->insert('folders', [
				'id' => null,
				'id_owner' => intval($this->id_owner),
				'name' => $this->name,
				'size' => intval($this->size),
				'parent' => intval($this->parent),
				'trash' => 0,
				'path' => $this->path
			]);
		}
		return false;
    }

    function delete($id) {
        // Delete folder with id $id and its children in database
		if($this->id_owner === null || $id == 0) return false;
        $size = $this->getSize($id);
        if($size === false) return false;
        $path = $this->getPath($id);
        if($path === false) return false;
        if(!($name = $this->getFoldername($id))) return false;
        $path .= $name;
        //$this->updateFoldersSize($id, -1*$size);
        $req = self::$_sql->prepare("DELETE FROM folders WHERE `path` LIKE ? AND id_owner = ?");
        $req->execute([$path.'%', $this->id_owner]);
        $req2 = self::$_sql->prepare("DELETE FROM folders WHERE id = ? AND id_owner = ?");
        $req2->execute([$id, $this->id_owner]);
        return $size;
    }

	function deleteFoldersfinal() {
		if($this->id_owner === null) return false;
		$req = self::$_sql->prepare("DELETE FROM folders WHERE id_owner = ?");
		return $req->execute([$this->id_owner]);
	}
}
