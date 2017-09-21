<?php
namespace application\models;
use \library\MVC as l;

class Ban extends l\Model {
    /* ban table
        1	id        int(11)			AUTO_INCREMENT
        2	id_user   int(11)
        3	reason    varchar(128)
        4	duration  int(11)
    */

    protected $id = null;
    protected $id_user = null;
    protected $reason;
    protected $duration;

	function __construct($id_user = null) {
		parent::__construct();
		// id_user (int) can be passed at init
		$this->id_user = $id_user;
	}

	function deleteBan() {
        if(isset($this->id_user) && is_numeric($this->id_user)) {
            $req = self::$_sql->prepare("DELETE FROM ban WHERE id_user = ?");
            return $req->execute([$this->id_user]);
        }
        return false;
    }
}
