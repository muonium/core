<?php
namespace application\models;
use \library\MVC as l;

class Upgrade extends l\Model {
    /* upgrade table
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
	protected $id_user = null;

	function __construct($id_user = null) {
		parent::__construct();
		if(is_numeric($id_user)) {
			$this->id_user = $id_user;
			$req = self::$_sql->prepare("SELECT * FROM upgrade WHERE id_user = ? ORDER BY `end` DESC");
			$req->execute([$this->id_user]);
			$this->upgrades = $req->fetchAll(\PDO::FETCH_ASSOC);
		}
	}

	function getUpgrades() {
		return $this->upgrades;
	}

	function transactionExists($txn_id) {
		$req = self::$_sql->prepare("SELECT id FROM upgrade WHERE txn_id = ?");
		$req->execute([$txn_id]);
		if($req->rowCount() > 0) return true;
		return false;
	}

	function addUpgrade($size, $price, $currency, $duration, $txn_id, $user_id = null) {
		// $duration in months, -1 = lifetime
		$user_id = $user_id === null ? $this->id_user : $user_id;
		if($user_id === null) return false;

		$end = ($duration === -1) ? -1 : strtotime("+".$duration." months", time());
		$insert = $this->insert('upgrade', [
			'id' => null,
			'id_user' => $user_id,
			'txn_id' => $txn_id,
			'size' => $size,
			'price' => $price,
			'currency' => $currency,
			'start' => time(),
			'end' => $end,
			'removed' => 0
		]);

		if($insert) {
			$req = self::$_sql->prepare("UPDATE storage SET user_quota = user_quota+? WHERE id_user = ?");
			$req->execute([$size, $user_id]);
		}
	}
}
