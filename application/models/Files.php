<?php
namespace application\models;
use \library\MVC as l;

class Files extends l\Model {
        
        /*
            1   id                  int(11)     AUTO_INCREMENT
            2   id_owner            int(11)
            3   dir                 text
            4	file                varchar(128)
            5	size	            int(11)
            6	last_modification	int(11)
            7   favorite            tinyint(1)
        */
        
        protected $id;
        protected $id_owner;
        protected $dir;
        protected $name;
        protected $size;
        protected $last_modification;
        protected $favorite;
        
        /* ******************** SETTER ******************** */
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
        function getIdOwner() {
            return $this->id_owner;
        }
        
        function getFilename($id) {
            $req = $this->_sql->prepare("SELECT file FROM files WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['file'];
        }
            
        function getSize() {
            if(!isset($this->id)) {
                $req = $this->_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND file = ? AND dir = ?");
                $req->execute(array($_SESSION['id'], $this->name, $this->{'dir'}));
            }
            else {
                $req = $this->_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND id = ?");
                $req->execute(array($_SESSION['id'], $id));
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
        
        function getFiles($path) {
            $req = $this->_sql->prepare("SELECT file, id, size, last_modification, favorite FROM files WHERE id_owner = ? AND dir = ?");
            $req->execute(array($_SESSION['id'], $path));
            if($req->rowCount() == 0)
                return false;
            // \PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE to set first column as key
            // 0 => id, 1 => size, 2 => last_modification, 3 => favorite
            /*
                Array
                (
                  [test.jpg] => Array
                    (
                      [0] => 1
                      [1] => 34
                      [2] => 0
                      [3] => 0
                    )

                  [a.png] => Array
                    (
                      [0] => 2
                      [1] => 30
                      [2] => 0
                      [3] => 0
                    )
                )
            */
            return $req->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE|\PDO::FETCH_NUM);
        }
        
        //
        
        function addNewFile($path) {
            $req = $this->_sql->prepare("INSERT INTO files VALUES ('', ?, ?, ?, ?, ?, '0')");
            $ret = $req->execute(array($_SESSION['id'], $path, $this->name, $this->size, $this->last_modification));   
            return $ret;
        }
        
        function updateFile($path) {
            // returns the difference beetween the size of the new file and the size of the old file
            $this->{'dir'} = $path;
            $old_size = $this->getSize();
            $req = $this->_sql->prepare("UPDATE files SET size = ?, last_modification = ? WHERE id_owner = ? AND file = ? AND dir = ?");
            $ret = $req->execute(array($this->size, $this->last_modification, $_SESSION['id'], $this->name, $path));
            return (($this->size)-$old_size);
        }
        
        function deleteFile($id) {
            $req = $this->_sql->prepare("SELECT size FROM files WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            $res = $req->fetch();
            
            $req = $this->_sql->prepare("DELETE FROM files WHERE id_owner = ? AND id = ?");
            $req->execute(array($_SESSION['id'], $id));
            return $res['size'];
        }
        
        function deleteFiles($path) {
            $path = str_replace("%", "\%", $path);
            $req = $this->_sql->prepare("SELECT SUM(size) AS total_size FROM files WHERE id_owner = ? AND dir LIKE ?");
            $req->execute(array($_SESSION['id'], $path.'%'));
            $res = $req->fetch();
            $total_size = $res['total_size'];
            
            $req = $this->_sql->prepare("DELETE FROM files WHERE id_owner = ? AND dir LIKE ?");
            $req->execute(array($_SESSION['id'], $path.'%'));
            return $total_size;
        }
    }
