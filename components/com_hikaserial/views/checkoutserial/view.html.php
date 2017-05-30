<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class checkoutserialViewcheckoutserial extends hikaserialView {

	protected $ctrl = 'checkout';
	protected $icon = 'checkout';

	public function display($tpl = null, $params = array()) {
		$this->params =& $params;
		$fct = $this->getLayout();
		if(method_exists($this, $fct)) {
			if($this->$fct() === false)
				return;
		}
		parent::display($tpl);
	}

	public function coupon() {
		$app = JFactory::getApplication();

		global $Itemid;
		$url_itemid='';
		if(!empty($Itemid)){
			$url_itemid = '&Itemid='.$Itemid;
		}
		$this->assignRef('url_itemid', $url_itemid);

		$config = hikaserial::config();
		$this->assignRef('config', $config);
		$shopConfig = hikaserial::config(false);
		$this->assignRef('shopConfig', $shopConfig);

		$cart = new stdClass();
		if(method_exists($this->params->view, 'initCart'))
			$cart = $this->params->view->initCart();

		if(isset($cart->coupon))
			$this->assignRef('coupon', $cart->coupon);

		$this->assignRef('step', $this->params->view->step);

		$cartHelper = hikaserial::get('shop.helper.cart');
		$this->assignRef('cartHelper', $cartHelper);
	}
}
