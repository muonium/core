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
        function getId($path) {
            $req = self::$_sql->prepare("SELECT id FROM folders WHERE id_owner = ? AND `path` = ?");
            $req->execute(array($_SESSION['id'], $path));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['id'];
        }

        function getIdOwner() {
            return $this->id_owner;
        }

        function getFoldername($id) {
            $req = self::$_sql->prepare("SELECT name FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['name'];
        }

        function getPath($id) {
            $req = self::$_sql->prepare("SELECT `path` FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['path'];
        }

        function getFullPath($folder_id) {
            // getPath + getFolderName
            if(!is_numeric($folder_id))
                return false;
            elseif($folder_id != 0) {
                $req = self::$_sql->prepare("SELECT `path`, name FROM folders WHERE id_owner = ? AND id = ?");
                $ret = $req->execute(array($_SESSION['id'], $folder_id));
                if($ret) {
                    $res = $req->fetch();
                    return $res['0'].$res['1'];
                }
                return false;
            }
            else
                return '';
        }

        function getParent($id) {
            $req = self::$_sql->prepare("SELECT parent FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['parent'];
        }

        function getChildren($id, $trash = '') {
            if($trash === '' || $trash === 'all') {
                $req = self::$_sql->prepare("SELECT id, name, size, parent, `path` FROM folders WHERE id_owner = ? AND parent = ? ORDER BY name ASC");
                $req->execute(array($_SESSION['id'], $id));
            }
            elseif($trash === 0 || ($trash === 1 && $id !== 0)) {
                $req = self::$_sql->prepare("SELECT id, name, size, parent, `path` FROM folders WHERE id_owner = ? AND parent = ? AND trash = 0 ORDER BY name ASC");
                $req->execute(array($_SESSION['id'], $id));
            }
            else { // trash === 1 && $id === 0
                $req = self::$_sql->prepare("SELECT id, name, size, parent, `path` FROM folders WHERE id_owner = ? AND trash = 1 ORDER BY name ASC");
                $req->execute(array($_SESSION['id']));
            }
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetchAll(\PDO::FETCH_NUM);
            return $res;
        }

        function getTrash($id) {
            $req = self::$_sql->prepare("SELECT trash FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['trash'];
        }

        function getSize($id) {
            $req = self::$_sql->prepare("SELECT size FROM folders WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['size'];
        }

        function updateTrash($id, $trash) {
            $req = self::$_sql->prepare("UPDATE folders SET trash = ? WHERE id_owner = ? AND id = ?");
            return $req->execute(array($trash, $_SESSION['id'], $id));
        }

        // Update the size of the folder with an increment of $size
        function updateFolderSize($id, $size) {
            $req = self::$_sql->prepare("UPDATE folders SET size = size + ? WHERE id_owner = ? AND id = ?");
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
        // Maybe better to use UPDATE with LIKE ?
        function updatePath($id, $path, $foldername) {
            $subdirs = $this->getChildren($id);
            if($subdirs !== false) {
                foreach($subdirs as $subdir)
                    $this->updatePath($subdir['0'], $path.$foldername.'/', $subdir['1']);
            }
            $req = self::$_sql->prepare("UPDATE folders SET `path` = ? WHERE id_owner = ? AND id = ?");
            $req->execute(array($path, $_SESSION['id'], $id));
        }

        // Experimental method
        function rename($path, $old, $new) {
            $req = self::$_sql->prepare("UPDATE folders SET name = ? WHERE id_owner = ? AND name = ? AND `path` = ?");
            $req->execute(array($new, $_SESSION['id'], $old, $path));

            // Children
            $req = self::$_sql->prepare("UPDATE folders SET `path` = CONCAT(?, SUBSTR(`path`, ?)) WHERE id_owner = ? AND `path` LIKE ?");
            $req->execute(array($path.$new, strlen($path.$old)+1, $_SESSION['id'], $path.$old.'%'));
        }

        function updateParent($id, $parent) {
            $req = self::$_sql->prepare("UPDATE folders SET parent = ?, name = ? WHERE id_owner = ? AND id = ?");
            return $req->execute(array($parent, $this->name, $_SESSION['id'], $id));
        }

        function insert() {
            $req = self::$_sql->prepare("INSERT INTO  folders VALUES (NULL, ?, ?, ?, ?, ?, ?)");
            return $req->execute(array($_SESSION['id'], $this->name, $this->size, $this->parent, 0, $this->path));
        }

        function delete($id) {
            // Delete folder with id $id and its children in database, //removed :update parents folder size
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
            //$this->updateFoldersSize($id, -1*$size);
            $req = self::$_sql->prepare("DELETE FROM folders WHERE `path` LIKE ? AND id_owner = ?");
            $req->execute(array($path.'%', $_SESSION['id']));
            $req2 = self::$_sql->prepare("DELETE FROM folders WHERE id = ? AND id_owner = ?");
            $req2->execute(array($id, $_SESSION['id']));
            return $size;
        }
    }
