<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;
use \config as conf;

class Upgrade extends l\Languages {
	private $_modelUpgrade;
	private $_modelStoragePlans;

    function __construct() {
        parent::__construct([
            'mustBeLogged' => true,
            'mustBeValidated' => true
        ]);
		$this->_modelUpgrade = new m\Upgrade($_SESSION['id']);
		$this->_modelStoragePlans = new m\StoragePlans();
    }

    function DefaultAction() {
		$offers = '';
		$endpoint = 'https://www.coinpayments.net/index.php';
		$merchant_id = conf\confPayments::merchant_id;
		$ipn_url = conf\confPayments::ipn_url;

		$storage_plans = $this->_modelStoragePlans->getPlans();
		$i = 0;
		foreach($storage_plans as $plan) {
			$product_name = showSize($plan['size']).' - '.$plan['price'].' '.strtoupper($plan['currency']).' - '.$this->duration($plan['duration']);
			$offers .= '<div>';

			if($i === 0) $offers .= '<div class="most-popular">Most Popular</div>';
			$offers .= '<div class="offer-size">'.showSize($plan['size']).'</div>';
			$offers .= '<div class="offer-price"><span class="currency">'.currencySymbol($plan['currency']).'</span>'.number_format($plan['price'], 2).'</div>';
			$offers .= '<div class="offer-duration">'.$this->duration($plan['duration']).'</div>';

			if($plan['product_id'] !== null) {

				$fields = [
					'cmd' => '_pay_simple',
					'merchant' => $merchant_id,
					'item_name' => $product_name,
					'item_number' => $plan['product_id'],
					'currency' => strtolower($plan['currency']),
					'amountf' => floatval($plan['price']),
					'ipn_url' => $ipn_url,
					'success_url' => URL_APP.'/Upgrade/?success=ok',
					'cancel_url' => '',
					'custom'  => $_SESSION['id'],
					'want_shipping' => '0'
				];

				$offers .= '<form action="'.$endpoint.'" method="post">';
				foreach($fields as $name => $value) {
					$offers .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
				}
				$offers .= '<button type="submit" class="btn">'.self::$txt->Upgrade->upgrade.'</button>';
				$offers .= '</form>';
			}
			$offers .= '</div>';
			$i++;
		}

		$history = '';
		$upgrades = $this->_modelUpgrade->getUpgrades();
		foreach($upgrades as $upgrade) {
			$history .= '<tr>';
			$history .= '<td>'.showSize($upgrade['size']).'</td>';
			$history .= '<td>'.$upgrade['price'].' '.currencySymbol($upgrade['currency']).'</td>';
			$history .= '<td>'.date(self::$txt->Dates->date.' '.self::$txt->Dates->time, $upgrade['start']).'</td>';
			$history .= '<td>'.date(self::$txt->Dates->date.' '.self::$txt->Dates->time, $upgrade['end']).'</td>';
			$history .= '<td class="red fit-width">';
			if($upgrade['removed'] === 1) $history .= self::$txt->Upgrade->expired;
			$history .= '</td></tr>';
		}
		$msg = isset($_GET['success']) ? '<p class="green">'.self::$txt->Upgrade->success_msg.'</p>' : '';

		require_once(DIR_VIEW."Upgrade.php");
    }

	function duration($duration) {
		if($duration < 0) return self::$txt->Upgrade->lifetime;
		if($duration === 12) return $duration.' '.self::$txt->Upgrade->year;
		if($duration % 12 === 0) return ($duration/12).' '.self::$txt->Upgrade->years;
		if($duration === 1) return $duration.' '.self::$txt->Upgrade->month;
		return $duration.' '.self::$txt->Upgrade->months;
	}
}
