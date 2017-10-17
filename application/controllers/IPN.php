<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;
use \config as conf;

/* Called after a transaction by CoinPayments */

if(!function_exists('hash_equals')) { // PHP < 5.6
	function hash_equals($str1, $str2) {
		if(strlen($str1) != strlen($str2)) return false;
		$res = $str1 ^ $str2; $ret = 0;
		for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
		return !$ret;
	}
}

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
		$merchant_id = conf\confPayments::merchant_id;
		$ipn_secret = conf\confPayments::ipn_secret;

		if(count($_POST) > 0) {
			if(isset($_SERVER['HTTP_HMAC']) && isset($_POST['merchant'])) {
				$merchant = $_POST['merchant'];
				if($merchant == $merchant_id) {
					$request = file_get_contents('php://input');
					if(isset($request) && $request !== false) {
						$hmac = hash_hmac("sha512", $request, $ipn_secret);
						if(hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {

							$ipn_mode = isset($_POST['ipn_mode']) ? $_POST['ipn_mode'] : null;
							$product_id = isset($_POST['item_number']) ? $_POST['item_number'] : null;
							$user_id = isset($_POST['custom']) && is_numeric($_POST['custom']) ? intval($_POST['custom']) : 0;
							$txn_id = isset($_POST['txn_id']) ? $_POST['txn_id'] : null;
							$status = isset($_POST['status']) && is_numeric($_POST['status']) ? intval($_POST['status']) : 0;
							$currency1 = isset($_POST['currency1']) ? $_POST['currency1'] : null;
							$amount1 = isset($_POST['amount1']) && is_numeric($_POST['amount1']) ? floatval($_POST['amount1']) : 0;
							$currency2 = isset($_POST['currency2']) ? $_POST['currency2'] : null;
							$amount2 = isset($_POST['amount2']) && is_numeric($_POST['amount2']) ? floatval($_POST['amount2']) : 0;

							if($ipn_mode == 'hmac' && $user_id !== 0 && $product_id !== null && $txn_id !== null && $currency1 !== null) {
								if(!($this->_modelUpgrade->transactionExists($txn_id))) {
									$plans = $this->_modelStoragePlans->getPlans();
									foreach($plans as $plan) {
										if($plan['product_id'] === $product_id) {
											// get price currency & name with product_id
											$price = floatval($plan['price']);
											$currency = $plan['currency'];
											$size = $plan['size'];
											$duration = $plan['duration'];
											break;
										}
									}

									if(isset($price) && isset($currency)) {
										if(strtoupper($currency) == strtoupper($currency1) && $amount1 >= floatval($price)) {
											if($status >= 100 || $status == 2) {
												$this->_modelUpgrade->id_user = $user_id;
												$user_mail = $this->_modelUsers->getEmail($user_id);
												if($user_mail !== false) {
													$this->_modelUpgrade->addUpgrade($size, $amount2, $currency2, $duration, $txn_id, $user_id);
												}
											}
										}
									}
								}
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
