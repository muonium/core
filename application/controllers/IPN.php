<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

/* Called after a transaction by PayPal */

class IPN extends l\Languages {
	private $_modelUpgrade;
	private $_modelStoragePlans;
	private $_modelUsers;

    function __construct() {
        parent::__construct([
            'mustBeLogged' => false,
            'mustBeValidated' => false
        ]);
		$this->_modelUpgrade = new m\Upgrade();
		$this->_modelStoragePlans = new m\StoragePlans();
		$this->_modelUsers = new m\Users();
    }

    function DefaultAction() {
		if(count($_POST) > 0) {
			// Send back request to PayPal to check if it's correct
			$vrf = file_get_contents('https://www.paypal.com/cgi-bin/webscr?cmd=_notify-validate', false, stream_context_create([
	    		'http' => [
	        		'header'  => "Content-type: application/x-www-form-urlencoded\r\nUser-Agent: Muonium\r\n",
	        		'method'  => 'POST',
	        		'content' => http_build_query($_POST)
	    		]
			]));

			if((bool)strstr($vrf, 'VERIFIED')) {
				// Verified by PayPal, now check if it's completed and retrieve upgrade plan with item number
				if(isset($_POST['payment_status']) && $_POST['payment_status'] == 'Completed' && isset($_POST['item_number']) && strlen($_POST['item_number']) > 0 && isset($_POST['custom']) && is_numeric($_POST['custom'])) {
					$txn_id = $_POST['txn_id'];
					$paypal_button_id = $_POST['item_number'];
					$user_id = intval($_POST['custom']);
					$price = floatval($_POST['mc_gross']);
					$currency = $_POST['mc_currency'];

					$this->_modelUpgrade->id_user = $user_id;

					// Verify if transaction id already exists
					if(!($this->_modelUpgrade->transactionExists($txn_id))) {
						$plans = $this->_modelStoragePlans->getPlans();
						foreach($plans as $plan) {
							if($plan['paypal_button_id'] === $paypal_button_id) {
								$user_mail = $this->_modelUsers->getEmail($user_id);
								if($user_mail !== false) {
									$this->_modelUpgrade->addUpgrade($plan['size'], $price, $currency, $plan['duration'], $txn_id, $user_id);
								}
								break;
							}
						}
					}
				}
			}
			exit;
		}
		header('Location: Upgrade');
	}
}
