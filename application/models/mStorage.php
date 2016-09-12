<?php
    class mStorage extends Model {
        
        /*
            1   id              int(11)     AUTO_INCREMENT
            2   id_user         int(11)
            3   user_quota      int(11)
            4   size_stored     int(11)	
        */
        
        private $id;
        private $id_user;
        private $user_quota;
        private $size_stored;
        
        /* ******************** SETTER ******************** */
        function setIdUser($id_user) {
            $this->id_user = $id_user;
        }
        
        function setUserQuota($user_quota) {
            $this->user_quota = $user_quota;
        }       
        
        function setSizeStored($size_stored) {
            $this->size_stored = $size_stored;
        }
        
        function incrementSizeStored($i) {
            if(is_numeric($i)) {
                if($i > 0) {
                    $req = $this->_sql->prepare("UPDATE storage SET size_stored = size_stored+? WHERE id_user = ?");
                    return $req->execute(array($i, $this->id_user));
                }
            }
            return false;
        }
        
        function decrementSizeStored($i) {
            if(is_numeric($i)) {
                if($i > 0) {
                    $req = $this->_sql->prepare("UPDATE storage SET size_stored = size_stored-? WHERE id_user = ?");
                    return $req->execute(array($i, $this->id_user));
                }
            }
            return false;
        }
        
        function updateSizeStored($i) {
            if(is_numeric($i)) {
                if($i > 0) {
                    $req = $this->_sql->prepare("UPDATE storage SET size_stored = ? WHERE id_user = ?");
                    return $req->execute(array($i, $this->id_user));
                }
            }
            return false;
        }
        
        function Insertion() {
            $req = $this->_sql->prepare("INSERT INTO storage VALUES ('', ?, ?, ?)");
            $ret = $req->execute(array($this->id_user, 2000000000, 0));   
            return $ret;
        }
        
        /* ******************** GETTER ******************** */
        function getId() {
            return $this->id;
        }
        
        function getIdUser() {
            return $this->id_user;
        }
        
        function getUserQuota() {
            $req = $this->_sql->prepare("SELECT user_quota FROM storage WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['user_quota'];
        }       
        
        function getSizeStored() {
            $req = $this->_sql->prepare("SELECT size_stored FROM storage WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['size_stored'];
        }
    }

