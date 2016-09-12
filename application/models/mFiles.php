<?php
    class mFiles extends Model {
        
        /*
            1   id                  int(11)     AUTO_INCREMENT
            2   id_owner            int(11)
            3   dir                 text
            4	file                varchar(128)
            5	size	            int(11)
            6	last_modification	int(11)
            7   favorite            tinyint(1)
        */
        
        private $id;
        private $id_owner;
        private $dir;
        private $name;
        private $size;
        private $last_modification;
        private $favorite;
        
        /* ******************** SETTER ******************** */
        function setIdOwner($id_owner) {
            $this->id_owner = $id_owner;
        }
        
        function setFile($file) {
            $this->name = $file;
        }
            
        function setSize($size) {
            $this->size = $size;
        }
        
        function setLastModification($last_modification) {
            $this->last_modification = $last_modification;
        }
        
        function setFavorite() {
            $this->favorite = 1;
        }
        
        function unsetFavorite() {
            $this->favorite = 0;
        }
        
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
            return $this->size;
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
