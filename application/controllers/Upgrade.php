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
		foreach($storage_plans as $plan) {
			$product_name = $this->showSize($plan['size']).' - '.$plan['price'].' '.strtoupper($plan['currency']).' - '.$this->duration($plan['duration']);
			$offers .= '<li>'.$product_name;
			if($plan['product_id'] !== null) {

				$fields = [
					'cmd' => '_pay_simple',
					'merchant' => $merchant_id,
					'item_name' => $product_name,
					'item_number' => $plan['product_id'],
					'currency' => strtolower($plan['currency']),
					'amountf' => floatval($plan['price']),
					'ipn_url' => $ipn_url,
					'success_url' => '',
					'cancel_url' => '',
					'custom'  => $_SESSION['id'],
					'want_shipping' => '0'
				];

				$offers .= '<form action="'.$endpoint.'" method="post">';
				foreach($fields as $name => $value) {
					$offers .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
				}
				$offers .= '<button type="submit">'.$this->txt->Upgrade->buy.'</button>';
				$offers .= '</form>';
			}
			$offers .= '</li>';
		}

		$history = '';
		$upgrades = $this->_modelUpgrade->getUpgrades();
		foreach($upgrades as $upgrade) {
			$history .= '<tr>';
			$history .= '<td>'.$this->showSize($upgrade['size']).'</td>';
			$history .= '<td>'.$upgrade['price'].' '.strtoupper($upgrade['currency']).'</td>';
			$history .= '<td>'.date('Y-m-d G:i', $upgrade['start']).'</td>';
			$history .= '<td>'.date('Y-m-d G:i', $upgrade['end']).'</td>';
			$history .= '<td class="red fit-width">';
			if($upgrade['removed'] === 1) $history .= $this->txt->Upgrade->expired;
			$history .= '</td></tr>';
		}

		require_once(DIR_VIEW."Upgrade.php");
    }

	function duration($duration) {
		if($duration < 0) return $this->txt->Upgrade->lifetime;
		if($duration === 12) return $duration.' '.$this->txt->Upgrade->year;
		if($duration % 12 === 0) return ($duration/12).' '.$this->txt->Upgrade->years;
		if($duration === 1) return $duration.' '.$this->txt->Upgrade->month;
		return $duration.' '.$this->txt->Upgrade->months;
	}
}
