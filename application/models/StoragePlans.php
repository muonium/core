<?php
namespace application\models;
use \library\MVC as l;

class StoragePlans extends l\Model {
    /* storage_plans table
        1	id        			int(11)			AUTO_INCREMENT
        2	size      			bigint(20)
        3	price     			float
		4	currency  			varchar(10)
		5	duration  			int(11)			In months, -1 = lifetime
		6	product_id 			varchar(20)		Item unique identifier
    */

    protected $plans;

	function __construct() {
		parent::__construct();
		$req = self::$_sql->prepare("SELECT * FROM storage_plans ORDER BY size ASC");
		$req->execute();
		$this->plans = $req->fetchAll(\PDO::FETCH_ASSOC);
	}

	function getPlans() {
		return $this->plans;
	}
}
