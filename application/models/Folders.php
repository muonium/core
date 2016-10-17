<?php
namespace application\models;
use \library\MVC as l;

class Folders extends l\Model {

        /*
            1   id                  int(11)     AUTO_INCREMENT
            2   id_owner            int(11)
            2   name                varchar(128)
            3   size                bigint(20)
            4   parent              bigint (20)
            5   trash               tinyint(1)
            6   path	              text
        */

        protected $id;
        protected $id_owner;
        protected $name;
        protected $size = 0;
        protected $parent = 0;
        protected $trash;
        protected $path;

        /* ******************** SETTER ******************** */

        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }

        function getIdOwner() {
            return $this->id_owner;
        }

        function getFoldername($id) {
            $req = $this->_sql->prepare("SELECT name FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['name'];
        }

        function getPath($id) {
            $req = $this->_sql->prepare("SELECT `path` FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['path'];
        }

        function getParent($id) {
            $req = $this->_sql->prepare("SELECT parent FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['parent'];
        }

        function getChildren($id, $trash = '') {
            if($trash === '' || $trash === 'all') {
                $req = $this->_sql->prepare("SELECT id, name, size FROM folders WHERE id_owner = ? AND parent = ? ORDER BY name ASC");
                $req->execute(array($_SESSION['id'], $id));
            }
            elseif($trash === 0 || ($trash === 1 && $id !== 0)) {
                $req = $this->_sql->prepare("SELECT id, name, size FROM folders WHERE id_owner = ? AND parent = ? AND trash = 0 ORDER BY name ASC");
                $req->execute(array($_SESSION['id'], $id));
            }
            else { // trash === 1 && $id === 0
                $req = $this->_sql->prepare("SELECT id, name, size FROM folders WHERE id_owner = ? AND trash = 1 ORDER BY name ASC");
                $req->execute(array($_SESSION['id']));
            }
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetchAll(\PDO::FETCH_NUM);
            return $res;
        }

        function getTrash($id) {
            $req = $this->_sql->prepare("SELECT trash FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['trash'];
        }

        function getSize($id) {
            $req = $this->_sql->prepare("SELECT size FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['size'];
        }

        // Update the size of the folder with an increment of $size
        function updateFolderSize($id, $size) {
            $req = $this->_sql->prepare("UPDATE folders SET size = size + ? WHERE id_owner = ? AND id = ?");
            return $req->execute(array($size, $_SESSION['id'], $id));
        }

        // Update the size of folder and each parent folder until root with an increment of $size
        function updateFoldersSize($id, $size) {
            do {
                if(!($this->updateFolderSize($id, $size)))
                  break;
                $id = $this->getParent($id);
            } while($id != 0 && $id !== false);
        }

        // Update path of a folder and its subfolders
        function updatePath($id, $path) {
            if($subdirs = $this->getChildren($id)) {
                foreach($subdirs as $subdir)
                    $this->updatePath($subdir['0'], $path.$subdir['1'].'/');
            }
            $req = $this->_sql->prepare("UPDATE folders SET `path` = ? WHERE id_owner = ? AND id = ?");
            $req->execute(array($path, $_SESSION['id'], $id));
        }

        function updateParent($id, $parent) {
            $req = $this->_sql->prepare("UPDATE folders SET parent = ? WHERE id_owner = ? AND id = ?");
            return $req->execute(array($parent, $_SESSION['id'], $id));
        }

        function insert() {
            $req = $this->_sql->prepare("INSERT INTO  folders VALUES (NULL, ?, ?, ?, ?, ?, ?)");
            return $req->execute(array($_SESSION['id'], $this->name, $this->size, $this->parent, 0, $this->path));
        }

        function delete($id) {
            // Delete folder with id $id and its children in database, update parents folder size
            if($id == 0)
                return false;
            $size = $this->getSize($id);
            if($size === false)
                return false;
            $path = $this->getPath($id);
            if($path === false)
                return false;
            if(!($name = $this->getFoldername($id)))
                return false;
            $path .= $name;
            $this->updateFoldersSize($id, -1*$size);
            $req = $this->_sql->prepare("DELETE FROM folders WHERE `path` LIKE ? AND id_owner = ?");
            $req->execute(array($path.'%', $_SESSION['id']));
            $req2 = $this->_sql->prepare("DELETE FROM folders WHERE id = ? AND id_owner = ?");
            $req2->execute(array($id, $_SESSION['id']));
            return $size;
        }
    }
