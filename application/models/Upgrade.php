<?php
namespace application\models;
use \library\MVC as l;

class Upgrade extends l\Model {

    /*
        1	id        int(11)			AUTO_INCREMENT
        2	id_user   int(11)
		3	txn_id	  varchar(64)	Transaction ID, Must be unique
        4	size      bigint(20)
        5	price     float
		6	currency  varchar(10)
		7	start	  int(11)
		8	end	      int(11)
		9	removed	  tinyint(1)	Confirmation that upgrade has been removed from user storage quota
    */

    protected $upgrades = null;

	function __construct() {
		parent::__construct();
		if(isset($_SESSION['id'])) {
			$req = self::$_sql->prepare("SELECT * FROM upgrade WHERE id_user = ? ORDER BY `end` DESC");
			$req->execute(array($_SESSION['id']));
			$this->upgrades = $req->fetchAll(\PDO::FETCH_ASSOC);
		}
	}

	function getUpgrades() {
		return $this->upgrades;
	}

	function transactionExists($txn_id) {
		$req = self::$_sql->prepare("SELECT id FROM upgrade WHERE txn_id = ?");
		$req->execute(array($txn_id));
		if($req->rowCount() > 0) return true;
		return false;
	}

	function addUpgrade($size, $price, $currency, $duration, $txn_id, $user_id = null) {
		// $duration in months, -1 = lifetime
		$user_id = ($user_id === null && isset($_SESSION['id'])) ? $_SESSION['id'] : $user_id;
		if($user_id !== null) {
			$end = ($duration === -1) ? -1 : strtotime("+".$duration." months", time());
			$req = self::$_sql->prepare("INSERT INTO upgrade VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 0)");

			if($req->execute(array($user_id, $txn_id, $size, $price, $currency, time(), $end))) {
				$req = self::$_sql->prepare("UPDATE storage SET user_quota = user_quota+? WHERE id_user = ?");
				$req->execute(array($size, $user_id));
			}
		}
	}
}
