<?php
namespace application\models;
use \library\MVC as l;

class Users extends l\Model {

        /*
            1   id                  int(11)         AUTO_INCREMENT
            2   login               varchar(20)
            3   password            varchar(128)
            4   email               varchar(254)
            5   registration_date   int(11)
            6   last_connection     int(11)
            7   cek                 varchar(330)
            8   double_auth         tinyint(1)      => 0 : Double auth not available for this user
            9   auth_code           varchar(8)      => 1 : Double auth available for this user
        */

        protected $id;
        protected $login;
        protected $password;
        protected $email;
		protected $cek;
        protected $doubleAuth = 0;
        protected $code;

        /* ******************** SETTER ******************** */

        function setDoubleAuth($state) {
            if($state == 0 || $state == 1)
                $this->doubleAuth = $state;
        }

        function setCode($code) {
            if(strlen($code) == 8)
                $this->code = $code;
        }

        /* ************************************************ */

        /* ******************** GETTER ******************** */
        function getId() {
            // Get id with login or email
            if(!empty($this->login)) {
                $req = self::$_sql->prepare("SELECT id FROM users WHERE login = ?");
                $req->execute(array($this->login));
            }
            elseif(!empty($this->email)) {
                $req = self::$_sql->prepare("SELECT id FROM users WHERE email = ?");
                $req->execute(array($this->email));
            }
            else
                return false;

            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['id'];
        }

        function getEmail($id = null) {
            // Get email with user id or login or email
			if(is_numeric($id)) {
				$req = self::$_sql->prepare("SELECT email FROM users WHERE id = ?");
                $req->execute(array($id));
			}
            elseif(!empty($this->id)) {
                $req = self::$_sql->prepare("SELECT email FROM users WHERE id = ?");
                $req->execute(array($this->id));
            }
            elseif(!empty($this->login)) {
                $req = self::$_sql->prepare("SELECT email FROM users WHERE login = ?");
                $req->execute(array($this->login));
            }
            elseif(!empty($this->email)) {
                $req = self::$_sql->prepare("SELECT email FROM users WHERE email = ?");
                $req->execute(array($this->email));
            }
            else {
                return false;
			}

            if($req->rowCount() == 0) {
                return false;
			}
            $res = $req->fetch();
            return $res['email'];
        }

        function getPassword() {
            $req = self::$_sql->prepare("SELECT password FROM users WHERE id = ?");
            $req->execute(array($this->id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['password'];
        }

        function getCek() {
            $req = self::$_sql->prepare("SELECT cek FROM users WHERE id = ?");
            $req->execute(array($this->id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['cek'];
        }

        function getLogin() {
             return $this->login;
        }

        function getDoubleAuth() {
            $req = self::$_sql->prepare("SELECT double_auth FROM users WHERE id = ? AND double_auth = '1'");
            $req->execute(array($this->id));
            if($req->rowCount() == 0)
                return false;
            return true;
        }

        function getCode() {
            $req = self::$_sql->prepare("SELECT auth_code FROM users WHERE id = ?");
            $req->execute(array($this->id));
            if($req->rowCount() == 0)
                return false;
            $res = $req->fetch();
            return $res['auth_code'];
        }

        /* **************************************** */

        function EmailExists() {
            $req = self::$_sql->prepare("SELECT id FROM users WHERE email = ?");
            $req->execute(array($this->email));
            if($req->rowCount())
                return true;
            return false;
        }

        function LoginExists() {
            $req = self::$_sql->prepare("SELECT id FROM users WHERE login = ?");
            $req->execute(array($this->login));
            if($req->rowCount())
                return true;
            return false;
        }

        function Insertion() {
            // $this->password must be double-hashed !
            $req = self::$_sql->prepare("INSERT INTO users VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, '')");
            $ret = $req->execute(array($this->login, $this->password, $this->email, time(), time(), $this->cek, $this->doubleAuth));
            return $ret;
        }

		function Connection() {
			$req = self::$_sql->prepare("SELECT id FROM users WHERE email = ? AND password = ?");
            $req->execute(array($this->email, $this->password));
            if($req->rowCount())
                return true;
            return false;
		}

        function updateLogin() {
            if(!empty($this->id)) {
                if(is_numeric($this->id)) {
                    $req = self::$_sql->prepare("UPDATE users SET login = ? WHERE id = ?");
                    return $req->execute(array($this->login, $this->id));
                }
            }
            return false;
        }

        function updatePassword() {
            // $this->password must be encrypted !
            if(!empty($this->id)) {
                if(is_numeric($this->id)) {
                    $req = self::$_sql->prepare("UPDATE users SET password = ? WHERE id = ?");
                    return $req->execute(array($this->password, $this->id));
                }
            }
            return false;
        }

        function updateCek() {
            // $this->passphrase must be encrypted !
            if(!empty($this->id)) {
                if(is_numeric($this->id)) {
                    $req = self::$_sql->prepare("UPDATE users SET cek = ? WHERE id = ?");
                    return $req->execute(array($this->cek, $this->id));
                }
            }
            return false;
        }

        function updateDoubleAuth($state) {
            if($state == 0 || $state == 1) {
                $req = self::$_sql->prepare("UPDATE users SET double_auth = ? WHERE id = ?");
                return $req->execute(array($state, $this->id));
            }
            return false;
        }

        function updateCode($code) {
            if(strlen($code) == 8) {
                $req = self::$_sql->prepare("UPDATE users SET auth_code = ? WHERE id = ?");
                return $req->execute(array($code, $this->id));
            }
            return false;
        }

/*      Add method for update email  */
		function updatemail() {
	        if(!empty($this->id)) {
	            if(is_numeric($this->id)) {
	                $req = self::$_sql->prepare("UPDATE users SET email = ? WHERE id = ?");
	                return $req->execute(array($this->email, $this->id));
	            }
	        }
	        return false;
	    }
	/*  Add Method for delete user  */

		function deleteUser() {
            if(!empty($this->id)) {
                if(is_numeric($this->id)) {
                     $req2 = self::$_sql->prepare("DELETE FROM users WHERE id = ?");
                    return $req2->execute(array( $this->id));
                }
            }
            return false;
        }
    }
