<?php
namespace application\models;
use \library\MVC as l;

class UserLostPass extends l\Model {
    /* user_lostpass table
        1   id                  int(11)         AUTO_INCREMENT
        2   id_user             int(11)
        3   val_key             varchar(128)
        4   expire              int(11)
    */

    protected $id = null;
    protected $id_user = null;
    protected $val_key;
    protected $expire;

	function __construct($id_user = null) {
		parent::__construct();
		// id_user (int) can be passed at init
		$this->id_user = $id_user;
	}

    function getKey() {
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("SELECT val_key FROM user_lostpass WHERE id_user = ?");
        $req->execute([$this->id_user]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['val_key'];
    }

    function getExpire() {
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("SELECT expire FROM user_lostpass WHERE id_user = ?");
        $req->execute([$this->id_user]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['expire'];
    }

    function Delete() {
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("DELETE FROM user_lostpass WHERE id_user = ?");
        return $req->execute([$this->id_user]);
    }

    function Insertion() {
		if($this->id_user === null || !isset($this->val_key) || !isset($this->expire)) return false;
		return $this->insert('user_lostpass', [
			'id' => null,
			'id_user' => $this->id_user,
			'val_key' => $this->val_key,
			'expire' => $this->expire
		]);
    }

    function Update() {
		if($this->id_user === null || !isset($this->val_key) || !isset($this->expire)) return false;
        $req = self::$_sql->prepare("UPDATE user_lostpass SET val_key = ?, expire = ? WHERE id_user = ?");
        return $req->execute([$this->val_key, $this->expire, $this->id_user]);
    }
}
