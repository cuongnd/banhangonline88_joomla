<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class auctionauctionViewAuctionauction extends hikaauctionView {

	const ctrl = 'auctionauction';
	const name = 'HIKAAUCTION_PRODUCTAUCTION';
	const icon = 'generic';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAAUCTION_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	function show_auction_payment(){
		$app = JFactory::getApplication();

		$product_id = JRequest::getInt('product_id');

		$cancel_auction = JRequest::getInt('cancel_auction', 0);

		$productClass = hikashop_get('class.product');
		$productClass->getProducts($product_id);
		$fullProduct = $productClass->products[$product_id];

		$addressClass = hikashop_get('class.address');

		$user = JFactory::getUser();
		$class = hikashop_get('class.user');
		$hika_user = $class->get($user->id, 'cms');

		if(!empty($user->id) && !empty($hika_user->user_id)){
			$user_address = $addressClass->loadUserAddresses($hika_user->user_id);
			if(!empty($user_address)){
				$this->assignRef('user_address', $user_address);
			}
		}


		$this->assignRef('cancel_auction', $cancel_auction);
		$this->assignRef('fullProduct', $fullProduct);
		$this->assignRef('product_id', $product_id);
	}
	function cancel_auction(){
		$app = JFactory::getApplication();

		$product_id = JRequest::getInt('product_id');

		$productClass = hikashop_get('class.product');
		$productClass->getProducts($product_id);
		$fullProduct = $productClass->products[$product_id];


		$this->assignRef('fullProduct', $fullProduct);
		$this->assignRef('product_id', $product_id);
	}
}
