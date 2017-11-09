<?php
namespace application\models;
use \library\MVC as l;

class Users extends l\Model {
    /* users table
        1   id                  int(11)         AUTO_INCREMENT
        2   login               varchar(20)
        3   password            varchar(128)
        4   email               varchar(254)
        5   registration_date   int(11)
        6   last_connection     int(11)
        7   cek                 varchar(330)
        8   double_auth         tinyint(1)      0 : Double auth not available for this user, 1 : Double auth available for this user
        9   auth_code           varchar(8)
    */

    protected $id = null;
    protected $login;
    protected $password;
    protected $email;
	protected $cek;
    protected $doubleAuth = 0;
    protected $code;

	function __construct($id = null) {
		parent::__construct();
		// id (int) can be passed at init
		$this->id = $id;
	}

    function setDoubleAuth($state) {
        if($state == 0 || $state == 1) $this->doubleAuth = $state;
    }

    function setCode($code) {
        if(strlen($code) === 8) $this->code = $code;
    }

    function getId() {
		// Returns user id with its login or email
        if(isset($this->login)) {
            $req = self::$_sql->prepare("SELECT id FROM users WHERE login = ?");
            $req->execute([$this->login]);
        }
        elseif(isset($this->email)) {
            $req = self::$_sql->prepare("SELECT id FROM users WHERE email = ?");
            $req->execute([$this->email]);
        }
        else {
            return false;
		}
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['id'];
    }

    function getEmail($id = null) {
        // Returns user email with its id, login or email if exists
		$id = $id === null ? $this->id : $id;
		if(is_numeric($id)) {
			$req = self::$_sql->prepare("SELECT email FROM users WHERE id = ?");
            $req->execute([$id]);
		}
        elseif(isset($this->login)) {
            $req = self::$_sql->prepare("SELECT email FROM users WHERE login = ?");
            $req->execute([$this->login]);
        }
        elseif(isset($this->email)) {
            $req = self::$_sql->prepare("SELECT email FROM users WHERE email = ?");
            $req->execute([$this->email]);
        }
        else {
            return false;
		}
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['email'];
    }

    function getPassword() {
		// Returns hashed password
		if($this->id === null) return false;
        $req = self::$_sql->prepare("SELECT password FROM users WHERE id = ?");
        $req->execute([$this->id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['password'];
    }

    function getCek() {
		if($this->id === null) return false;
        $req = self::$_sql->prepare("SELECT cek FROM users WHERE id = ?");
        $req->execute([$this->id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['cek'];
    }

    function getLogin($id = null) {
		$id = $id === null ? $this->id : $id;
		if(!is_numeric($id)) return false;
		$req = self::$_sql->prepare("SELECT login FROM users WHERE id = ?");
        $req->execute([$id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['login'];
    }

    function getDoubleAuth() {
		if($this->id === null) return false;
        $req = self::$_sql->prepare("SELECT double_auth FROM users WHERE id = ? AND double_auth = '1'");
        $req->execute([$this->id]);
        if($req->rowCount() === 0) return false;
        return true;
    }

    function getCode() {
		if($this->id === null) return false;
        $req = self::$_sql->prepare("SELECT auth_code FROM users WHERE id = ?");
        $req->execute([$this->id]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['auth_code'];
    }

    function EmailExists($email = null) {
		$email = $email === null ? $this->email : $email;
		if($email === null) return false;
        $req = self::$_sql->prepare("SELECT id FROM users WHERE email = ?");
        $req->execute([$email]);
        if($req->rowCount()) return true;
        return false;
    }

    function LoginExists($login = null) {
		$login = $login === null ? $this->login : $login;
		if($login === null) return false;
        $req = self::$_sql->prepare("SELECT id FROM users WHERE login = ?");
        $req->execute([$login]);
        if($req->rowCount()) return true;
        return false;
    }

    function Insertion() {
        // Password must be hashed before
		if(!isset($this->login) || !isset($this->password) || !isset($this->email) || !isset($this->cek) || !isset($this->doubleAuth)) return false;
		return $this->insert('users', [
			'id' => null,
			'login' => $this->login,
			'password' => $this->password,
			'email' => $this->email,
			'registration_date' => time(),
			'last_connection' => time(),
			'cek' => $this->cek,
			'double_auth' => $this->doubleAuth,
			'auth_code' => ''
		]);
    }

	function Connection() {
		if(!isset($this->email) || !isset($this->password)) return false;
		$req = self::$_sql->prepare("SELECT id FROM users WHERE email = ? AND password = ?");
        $req->execute([$this->email, $this->password]);
        if($req->rowCount()) return true;
        return false;
	}

    function updateLogin() {
		if($this->id === null) return false;
        $req = self::$_sql->prepare("UPDATE users SET login = ? WHERE id = ?");
        return $req->execute([$this->login, $this->id]);
    }

    function updatePassword() {
		// Password must be hashed before
		if($this->id === null) return false;
        $req = self::$_sql->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $req->execute([$this->password, $this->id]);
    }

    function updateCek() {
		if($this->id === null) return false;
        $req = self::$_sql->prepare("UPDATE users SET cek = ? WHERE id = ?");
        return $req->execute([$this->cek, $this->id]);
    }

    function updateDoubleAuth($state) {
		if($this->id === null || ($state != 0 && $state != 1)) return false;
        $req = self::$_sql->prepare("UPDATE users SET double_auth = ? WHERE id = ?");
        return $req->execute([$state, $this->id]);
    }

    function updateCode($code) {
		if($this->id === null || strlen($code) !== 8) return false;
        $req = self::$_sql->prepare("UPDATE users SET auth_code = ? WHERE id = ?");
        return $req->execute([$code, $this->id]);
    }

	function updatemail() {
	    if($this->id === null) return false;
	    $req = self::$_sql->prepare("UPDATE users SET email = ? WHERE id = ?");
	    return $req->execute([$this->email, $this->id]);
	}

	function updateLastConnection() {
		if($this->id === null) return false;
	    $req = self::$_sql->prepare("UPDATE users SET last_connection = ? WHERE id = ?");
	    return $req->execute([time(), $this->id]);
	}

	function deleteUser() {
		if($this->id === null) return false;
        $req = self::$_sql->prepare("DELETE FROM users WHERE id = ?");
        return $req->execute([$this->id]);
    }
}
