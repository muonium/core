<?php
namespace application\models;
use \library\MVC as l;

class UserValidation extends l\Model {
        
        /*
            1   id                  int(11)         AUTO_INCREMENT
            2   id_user             int(11)
            3   val_key             varchar(128)
        */
        
        protected $id;
        protected $id_user;
        protected $val_key;
        
        /* ******************** SETTER ******************** */
        
        /* ******************** GETTER ******************** */
        function getIdUser() {
            return $this->id_user;
        }
        
        function getKey() {
            $req = $this->_sql->prepare("SELECT val_key FROM user_validation WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['val_key'];
        }
        
        /* **************************************** */
        
        function Delete() {
            $req = $this->_sql->prepare("DELETE FROM user_validation WHERE id_user = ?");
            return $req->execute(array($this->id_user));
        }
        
        function Insert() {
            $req = $this->_sql->prepare("INSERT INTO user_validation VALUES ('', ?, ?)");
            return $req->execute(array($this->id_user, $this->val_key));
        }
        
        function Update() {
            $req = $this->_sql->prepare("UPDATE user_validation SET val_key = ? WHERE id_user = ?");
            return $req->execute(array($this->val_key, $this->id_user));
        }
    }
