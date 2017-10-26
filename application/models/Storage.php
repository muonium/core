<?php
namespace application\models;
use \library\MVC as l;

class Storage extends l\Model {
    /* storage table
        1   id              int(11)     AUTO_INCREMENT
        2   id_user         int(11)
        3   user_quota      bigint(20)
        4   size_stored     bigint(20)
    */

    protected $id = null;
    protected $id_user = null;
    protected $user_quota;
    protected $size_stored;

	function __construct($id_user = null) {
		parent::__construct();
		// id_user (int) can be passed at init
		$this->id_user = $id_user;
	}

    function incrementSizeStored($i) {
		// i (int) - Increment the size stored of $i B
		// $_SESSION['size_stored'] is incremented in the controller
		if($this->id_user === null || !is_numeric($i)) return false;
        $req = self::$_sql->prepare("UPDATE storage SET size_stored = size_stored+? WHERE id_user = ?");
        return $req->execute([$i, $this->id_user]);
    }

    function decrementSizeStored($i) {
		// i (int) - Decrement the size stored of $i B
        if(is_numeric($i)) return $this->incrementSizeStored(-1*$i);
        return false;
    }

    function updateSizeStored($i) {
		// i (int) - Set the size stored to $i B
		// $_SESSION['size_stored'] is set in the controller
		if($this->id_user === null || !is_numeric($i) || $i < 0) return false;
        $req = self::$_sql->prepare("UPDATE storage SET size_stored = ? WHERE id_user = ?");
        return $req->execute([$i, $this->id_user]);
    }

    function Insertion() {
		// Create a record for a new user
		if($this->id_user === null) return false;
		return $this->insert('storage', [
			'id' => null,
			'id_user' => $this->id_user,
			'user_quota' => 2*1000*1000*1000,
			'size_stored' => 0
		]);
    }

    function getUserQuota() {
		// Returns user quota
		// $_SESSION['size_stored'] is set in the controller
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("SELECT user_quota FROM storage WHERE id_user = ?");
        $req->execute([$this->id_user]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['user_quota'];
    }

    function getSizeStored() {
		// Returns size stored
		// $_SESSION['size_stored'] is set in the controller
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("SELECT size_stored FROM storage WHERE id_user = ?");
        $req->execute([$this->id_user]);
        if($req->rowCount() === 0) return false;
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res['size_stored'];
    }

	function deleteStorage() {
		if($this->id_user === null) return false;
        $req = self::$_sql->prepare("DELETE FROM storage WHERE id_user = ?");
        return $req->execute([$this->id_user]);
    }
}
