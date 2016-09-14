<?php
require_once("config/confDB.php");
require_once("config/Mail.php");

// run.php contains the cron class
// Scripts are not called here
// This page is included by other files which calls cron class

class cron {

	protected $_sql;

	private $_mail;

	// Delete inactive users after x days
	private $_inactiveUserDeleteDelay = 180;

	// Send a mail to an inactive user after x days
	private $_inactiveUserMailDelay = 150;

	function __construct() {
		$this->_sql = new PDO('mysql:host='.confDB::hostDefaut.';dbname='.confDB::bddDefaut,confDB::userDefaut,confDB::passDefaut);
		$this->_mail = new Mail();
	}

	function resetPpCounter() {
		// Run every month
		// Reset passphrase change counter for all users
		$req = $this->_sql->prepare("UPDATE users SET pp_counter = '0'");
		$req->execute();
	}

	function deleteInactiveUsers() {
		// Run every day
		// Query for selecting inactive users to delete
		$req = $this->_sql->prepare("SELECT id FROM users WHERE last_connection < ?");
		$req->execute(array(time()-$this->_inactiveUserDeleteDelay*86400));

		$i = 0;
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
			// To do : delete user's folder ($row['id'])

			// Delete user
			$req = $this->_sql->prepare("DELETE FROM users, user_lostpass, user_validation, ban, files, storage
			WHERE users.id = ? AND users.id = user_lostpass.id_user AND users.id = user_validation.id_user
			AND users.id = ban.id_user AND users.id = files.id_owner AND users.id = storage.id_user");

			if($req->execute(array($row['id'])))
				$i++;
		}

		echo '<p>Deleted '.$i.' inactive users</p>';

		//

		// Query for selecting inactive users to send a mail
		$req = $this->_sql->prepare("SELECT login, email FROM users WHERE last_connection >= ? AND last_connection <= ?");

		// $mailDay is an array with the day, month and year of the date x days before the current date
		$mailDay = explode("/", date('d/m/Y', time()-$this->_inactiveUserMailDelay*86400));

		// Select timestamp of this day at 00:00:00 and 23:59:59
		$mailDayFirst = mktime(0, 0, 0, $mailDay['1'], $mailDay['0'], $mailDay['2']);
		$mailDayLast = mktime(23, 59, 59, $mailDay['1'], $mailDay['0'], $mailDay['2']);

		// The mail will be sent once time, when the user reaches x days of inactivity
		$req->execute(array($mailDayFirst, $mailDayLast));

		$i = 0;
		// Subject of the mail
		$this->_mail->setSubject("Muonium - You are inactive");

		$this->_mail->setMessage("Hi ".$row['login'].",<br />This email is sent because you are inactive for 
		".$this->_inactiveUserMailDelay." days.<br />Your account will be deleted in
		".$this->_inactiveUserDeleteDelay-$this->_inactiveUserMailDelay." days if you don't log in<br />
		Muonium Team");

		while($row = $req->fetch(PDO::FETCH_ASSOC)) {

			$this->_mail->setTo($row['email']);
			if($this->_mail->send())
				$i++;
		}

		echo '<p>'.$i.' mails sent for inactive users</p>';
	}
};
?>
