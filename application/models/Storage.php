<?php
namespace application\models;
use \library\MVC as l;

class Storage extends l\Model {

        /*
            1   id              int(11)     AUTO_INCREMENT
            2   id_user         int(11)
            3   user_quota      bigint(20)
            4   size_stored     bigint(20)
        */

        protected $id;
        protected $id_user;
        protected $user_quota;
        protected $size_stored;

        /* ******************** SETTER ******************** */

        /* ************************************************ */

        function incrementSizeStored($i) {
            if(is_numeric($i)) {
                if($i > 0) {
                    $req = self::$_sql->prepare("UPDATE storage SET size_stored = size_stored+? WHERE id_user = ?");
					$_SESSION['size_stored'] += $i;
                    return $req->execute(array($i, $this->id_user));
                }
            }
            return false;
        }

        function decrementSizeStored($i) {
            if(is_numeric($i)) {
                if($i > 0) {
                    $req = self::$_sql->prepare("UPDATE storage SET size_stored = size_stored-? WHERE id_user = ?");
					$_SESSION['size_stored'] -= $i;
                    return $req->execute(array($i, $this->id_user));
                }
            }
            return false;
        }

        function updateSizeStored($i) {
            if(is_numeric($i)) {
                if($i > 0) {
                    $req = self::$_sql->prepare("UPDATE storage SET size_stored = ? WHERE id_user = ?");
					$_SESSION['size_stored'] = $i;
                    return $req->execute(array($i, $this->id_user));
                }
            }
            return false;
        }

        function Insertion() {
            $req = self::$_sql->prepare("INSERT INTO storage VALUES (NULL, ?, ?, ?)");
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
            $req = self::$_sql->prepare("SELECT user_quota FROM storage WHERE id_user = ?");
            $req->execute(array($_SESSION['id']));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
			$_SESSION['user_quota'] = $res['user_quota'];
            return $res['user_quota'];
        }

        function getSizeStored() {
            $req = self::$_sql->prepare("SELECT size_stored FROM storage WHERE id_user = ?");
            $req->execute(array($_SESSION['id']));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
			$_SESSION['size_stored'] = $res['size_stored'];
            return $res['size_stored'];
        }

	function deleteStorage() {
            if(!empty($this->id_user)) {
				
                if(is_numeric($this->id_user)) {
                    $req = self::$_sql->prepare("DELETE FROM storage WHERE id_user = ?");
                   return $req->execute(array($this->id_user));
                   
                }
            }
            return false;
        }
    }
