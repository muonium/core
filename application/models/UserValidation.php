<?php
namespace application\models;
use \library\MVC as l;

class UserValidation extends l\Model {
    /* user_validation table
        1   id                  int(11)         AUTO_INCREMENT
        2   id_user             int(11)
        3   val_key             varchar(128)
    */

    protected $id = null;
    protected $id_user = null;
    protected $val_key;

	function __construct($id_user = null) {
		parent::__construct();
		// id_user (int) can be passed at init
		$this->id_user = $id_user;
	}

    function getKey() {
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("SELECT val_key FROM user_validation WHERE id_user = ?");
        $req->execute([$this->id_user]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['val_key'];
    }

    function Delete() {
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("DELETE FROM user_validation WHERE id_user = ?");
        return $req->execute([$this->id_user]);
    }

    function Insertion() {
		if($this->id_user === null || !isset($this->val_key)) return false;
		return $this->insert('user_validation', [
			'id' => null,
			'id_user' => $this->id_user,
			'val_key' => $this->val_key
		]);
    }

    function Update() {
		if($this->id_user === null || !isset($this->val_key)) return false;
        $req = self::$_sql->prepare("UPDATE user_validation SET val_key = ? WHERE id_user = ?");
        return $req->execute([$this->val_key, $this->id_user]);
    }
}
