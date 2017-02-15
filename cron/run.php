<?php
define('NOVA', dirname(dirname(dirname(__FILE__))).'/nova');

require_once("config/confDB.php");
require_once("config/Mail.php");

// run.php contains the cron class
// Scripts are not called here
// This page is included by other files which calls cron class

class cron {

	protected static $_sql;

	private $_mail;

	// Delete inactive users after x days
	private $_inactiveUserDeleteDelay = 180;

	// Send a mail to an inactive user after x days
	private $_inactiveUserMailDelay = 150;

	function __construct() {
		self::$_sql = new PDO('mysql:host='.confDB::host.';dbname='.confDB::db,confDB::user,confDB::password);
		$this->_mail = new Mail();
	}

	function resetPpCounter() {
		// Run every month
		// Reset passphrase change counter for all users
		$req = self::$_sql->prepare("UPDATE users SET pp_counter = '0'");
		$req->execute();

		//call the notifier to log the event.
		shell_exec("bash notifier.sh reset_counter --force");
	}

	function deleteInactiveUsers() {
		// Run every day
		// Query for selecting inactive users to delete
		$req = self::$_sql->prepare("SELECT id FROM users WHERE last_connection < ?");
		$req->execute(array(time()-$this->_inactiveUserDeleteDelay*86400));

		$i = 0;
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
			// To do : delete user's folder ($row['id'])

			// Delete user
			$req = self::$_sql->prepare("DELETE FROM users, user_lostpass, user_validation, ban, files, storage
			WHERE users.id = ? AND users.id = user_lostpass.id_user AND users.id = user_validation.id_user
			AND users.id = ban.id_user AND users.id = files.id_owner AND users.id = storage.id_user");

			if($req->execute(array($row['id'])))
				$i++;
		}

		//call the notifier to log the event.
		shell_exec("bash notifier.sh inactive_users --force");

		//

		// Query for selecting inactive users to send a mail
		$req = self::$_sql->prepare("SELECT login, email FROM users WHERE last_connection >= ? AND last_connection <= ?");

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
	}

	function getFullPath($folder_id, $user_id) {
		if(!is_numeric($folder_id))
			return false;
		elseif($folder_id != 0) {
			$req = self::$_sql->prepare("SELECT `path`, name FROM folders WHERE id_owner = ? AND id = ?");
			$ret = $req->execute(array($user_id, $folder_id));
			if($ret) {
				$res = $req->fetch();
				return $res['0'].$res['1'];
			}
			return false;
		}
		else
			return '';
	}

	function deleteNotCompletedFiles() {
		$req = self::$_sql->prepare("SELECT id_owner, folder_id, name FROM files WHERE size = -1 AND expires <= ?");
		$req->execute(array(time()));
		$res = $req->fetchAll(\PDO::FETCH_ASSOC);

		foreach($res as $file) {
			$path = $this->getFullPath($file['folder_id'], $file['id_owner']);
			if($path === false)
				continue;
			$size = @filesize(NOVA.'/'.$file['id_owner'].'/'.$path.$file['name']);
				unlink(NOVA.'/'.$file['id_owner'].'/'.$path.$file['name']);
		}

		$req = self::$_sql->prepare("DELETE FROM files WHERE size = -1 AND expires <= ?");
		$req->execute(array(time()));
	}
};
?>
