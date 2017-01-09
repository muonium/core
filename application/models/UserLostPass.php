<?php
namespace application\models;
use \library\MVC as l;

class UserLostPass extends l\Model {

        /*
            1   id                  int(11)         AUTO_INCREMENT
            2   id_user             int(11)
            3   val_key             varchar(128)
            4   expire              int(11)
        */

        protected $id;
        protected $id_user;
        protected $val_key;
        protected $expire;

        /* ******************** SETTER ******************** */

        /* ******************** GETTER ******************** */
        function getIdUser() {
            return $this->id_user;
        }

        function getKey() {
            $req = self::$_sql->prepare("SELECT val_key FROM user_lostpass WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['val_key'];
        }

        function getExpire() {
            $req = self::$_sql->prepare("SELECT expire FROM user_lostpass WHERE id_user = ?");
            $req->execute(array($this->id_user));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['expire'];
        }

        /* **************************************** */

        function Delete() {
            $req = self::$_sql->prepare("DELETE FROM user_lostpass WHERE id_user = ?");
            return $req->execute(array($this->id_user));
        }

        function Insert() {
            $req = self::$_sql->prepare("INSERT INTO user_lostpass VALUES (NULL, ?, ?, ?)");
            return $req->execute(array($this->id_user, $this->val_key, $this->expire));
        }

        function Update() {
            $req = self::$_sql->prepare("UPDATE user_lostpass SET val_key = ?, expire = ? WHERE id_user = ?");
            return $req->execute(array($this->val_key, $this->expire, $this->id_user));
        }
    }
