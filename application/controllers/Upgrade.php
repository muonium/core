<?php
namespace application\controllers;
use \library\MVC as l;
use \application\models as m;

class Upgrade extends l\Languages {
	private $_modelStorage;
	private $_modelUpgrade;
	private $_modelStoragePlans;

    function __construct() {
        parent::__construct(array(
            'mustBeLogged' => true,
            'mustBeValidated' => true
        ));
		$this->_modelUpgrade = new m\Upgrade();
		$this->_modelStorage = new m\Storage();
		$this->_modelStoragePlans = new m\StoragePlans();
    }

    function DefaultAction() {
		$offers = '';
		$storage_plans = $this->_modelStoragePlans->getPlans();
		foreach($storage_plans as $plan) {
			$offers .= '<li>'.$this->showSize($plan['size']).' - <td>'.$plan['price'].' '.$plan['currency'].' - '.$this->duration($plan['duration']);
			if($plan['paypal_button_id'] !== null) {
				$offers .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="'.$plan['paypal_button_id'].'">
					<input name="return" type="hidden" value="'.URL_APP.'/Upgrade/Validation" />
					<input name="cancel_return" type="hidden" value="'.URL_APP.'/Upgrade" />
					<input name="notify_url" type="hidden" value="'.URL_APP.'/IPN" />
					<input name="custom" type="hidden" value="'.$_SESSION['id'].'" />
					<button type="submit">'.$this->txt->Upgrade->buy.'</button>
					</form></li>';
			}
		}

		$history = '';
		$upgrades = $this->_modelUpgrade->getUpgrades();
		foreach($upgrades as $upgrade) {
			$history .= '<tr><td>'.$this->showSize($upgrade['size']).'</td><td>'.$upgrade['price'].' '.$upgrade['currency'].'</td>
			<td>'.date('Y-m-d G:i', $upgrade['start']).'</td><td>'.date('Y-m-d G:i', $upgrade['end']).'</td><td class="red fit-width">';
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
