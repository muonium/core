<?php
namespace application\models;
use \library\MVC as l;

class Files extends l\Model {

        /*
            1   id                  int(11)     AUTO_INCREMENT
            2   id_owner            int(11)
            3   folder_id           bigint(20)
            4   name                varchar(128)
            5   size	              bigint(20)
            6   last_modification	  int(11)
            7   favorite            tinyint(1)
            8   trash               tinyint(1)
        */

        protected $id;
        protected $id_owner;
        protected $folder_id;
        protected $name;
        protected $size;
        protected $last_modification;
        protected $favorite;
        protected $trash;

        /* ******************** SETTER ******************** */

        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }

        function getIdOwner() {
            return $this->id_owner;
        }

        function getFilename($id) {
            $req = $this->_sql->prepare("SELECT name FROM files WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['name'];
        }

        function getFolderId($id) {
            $req = $this->_sql->prepare("SELECT folder_id FROM files WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['folder_id'];
        }

        function getSize() {
            if(!isset($this->id)) {
                $req = $this->_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND name = ? AND folder_id = ?");
                $req->execute(array($_SESSION['id'], $this->name, $this->folder_id));
            }
            else {
                $req = $this->_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND id = ?");
                $req->execute(array($_SESSION['id'], $this->id));
            }

            if($req->rowCount() == 0)
                return 0;
            $res = $req->fetch();
            return $res['size'];
        }

        function getLastModification() {
            return $this->last_modification;
        }

        function getFavorite() {
            return $this->favorite;
        }

        function getFiles($folder_id, $style = '') {
            $req = $this->_sql->prepare("SELECT name, id, size, last_modification, favorite, trash FROM files WHERE id_owner = ? AND folder_id = ? ORDER BY name ASC");
            $req->execute(array($_SESSION['id'], $folder_id));
            if($req->rowCount() == 0)
                return false;
            //   $style = 'filename' (old way)  ||      $style = '' (default)
            // First column as key              ||  Default key
            // 0 => id, 1 => size,              ||  0 => name, 1 => id, 2 => size
            // 2 => last_modification,          ||  3 => last_modification,
            // 3 => favorite, 4 => trash        ||  4 => favorite, 5 => trash
            /*
                Array                           ||  Array
                (                               ||  (
                  [test.jpg] => Array           ||    [0] => Array
                    (                           ||      (
                      [0] => 1                  ||        [0] => test.jpg
                      [1] => 34                 ||        [1] => 1
                      [2] => 0                  ||        [2] => 34
                      [3] => 0                  ||        [3] => 0
                      [4] => 0                  ||        ...
                    )                           ||      )
                                                ||
                  [a.png] => Array              ||    [1] => Array
                    (                           ||      (
                      [0] => 2                  ||        [0] => a.png
                      [1] => 30                 ||        [1] => 2
                      [2] => 0                  ||        [2] => 30
                      [3] => 0                  ||        [3] => 0
                      [4] => 0                  ||        ...
                    )                           ||      )
                )                               ||  )
            */
            if($style == 'filename')
                return $req->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE|\PDO::FETCH_NUM);
            return $req->fetchAll(\PDO::FETCH_NUM);
        }

        //

        function addNewFile($folder_id) {
            $req = $this->_sql->prepare("INSERT INTO files VALUES (NULL, ?, ?, ?, ?, ?, '0', '0')");
            $ret = $req->execute(array($_SESSION['id'], $folder_id, $this->name, $this->size, $this->last_modification));
            return $ret;
        }

        function updateFile($folder_id) {
            // returns the difference beetween the size of the new file and the size of the old file
            $this->folder_id = $folder_id;
            $old_size = $this->getSize();
            $req = $this->_sql->prepare("UPDATE files SET size = ?, last_modification = ? WHERE id_owner = ? AND name = ? AND folder_id = ?");
            $ret = $req->execute(array($this->size, $this->last_modification, $_SESSION['id'], $this->name, $folder_id));
            return (($this->size)-$old_size);
        }

        function updateDir() {
            $req = $this->_sql->prepare("UPDATE files SET folder_id = ? WHERE id_owner = ? AND id = ?");
            return $req->execute(array($this->folder_id, $_SESSION['id'], $this->id));
        }

        // I don't know for now if I will use this method...
        function updateFolderId($old, $new) {
            $req = $this->_sql->prepare("UPDATE files SET folder_id = ? WHERE id_owner = ? AND folder_id = ?");
            return $req->execute(array($new, $_SESSION['id'], $old));
        }

        function deleteFile($id) {
            $this->id = $id;
            if(!($size = $this->getSize()))
              return false;

            $req = $this->_sql->prepare("DELETE FROM files WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            return $size;
        }

        function deleteFiles($folder_id) {
            $req = $this->_sql->prepare("DELETE FROM files WHERE id_owner = ? AND folder_id = ?");
            $ret = $req->execute(array($_SESSION['id'], $folder_id));
            return $ret;
        }
    }
