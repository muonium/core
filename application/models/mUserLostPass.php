<?php
    class mUserLostPass extends Model {
        
        /*
            1   id                  int(11)         AUTO_INCREMENT
            2   id_user             int(11)
            3   val_key             varchar(128)
            4   expire              int(11)
        */
        
        private $id;
        private $id_user;
        private $val_key;
        private $expire;
        
        /* ******************** SETTER ******************** */
            
        function setIdUser($id_user) {
            $this->id_user = $id_user;
        }
        
        function setKey($key) {
            $this->val_key = $key;
        }
        
        function setExpire($expire) {
            $this->expire = $expire;
        }
        
        /* ******************** GETTER ******************** */
        function getIdUser() {
            return $this->id_user;
        }
        
        function getKey() {
            $req = $this->_sql->prepare("SELECT val_key FROM user_lostpass WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['val_key'];
        }
        
        function getExpire() {
            $req = $this->_sql->prepare("SELECT expire FROM user_lostpass WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['expire'];
        }
        
        /* **************************************** */
        
        function Delete() {
            $req = $this->_sql->prepare("DELETE FROM user_lostpass WHERE id_user = ?");
            return $req->execute(array($this->id_user));
        }
        
        function Insert() {
            $req = $this->_sql->prepare("INSERT INTO user_lostpass VALUES ('', ?, ?, ?)");
            return $req->execute(array($this->id_user, $this->val_key, $this->expire));
        }
        
        function Update() {
            $req = $this->_sql->prepare("UPDATE user_lostpass SET val_key = ? WHERE id_user = ?");
            return $req->execute(array($this->val_key, $this->id_user));
        }
    }

