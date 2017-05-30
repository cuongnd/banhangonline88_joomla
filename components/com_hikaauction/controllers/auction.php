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
class auctionAuctionController extends hikaauctionController {

	protected $rights = array(
		'display' => array(),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
	}

	function show_auction_payment(){
		if(!$this->_checkLogin()) return true;

		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$class = hikashop_get('class.user');
		$hika_user = $class->get($user->id, 'cms');
		$product_id = JRequest::getInt('product_id');

		$productClass = hikashop_get('class.product');
		$productClass->getProducts($product_id);
		$fullProduct = $productClass->products[$product_id];

		$auctionClass = hikaauction::get('class.auction');
		$newWinner = $auctionClass->getUsersWithMaxBid($fullProduct->product_id);

		if(empty($newWinner) || $hika_user->user_id != $newWinner){
			$app->enqueueMessage(JText::_('HIKA_AUCTION_PAYMENT_ERROR'), 'error');
			$app->redirect(hikashop_completeLink('order'));
		}

		JRequest::setVar( 'layout', 'show_auction_payment'  );
		return parent::display();
	}

	function cancel_auction(){
		if(!$this->_checkLogin()) return true;

		$user = JFactory::getUser();
		$class = hikashop_get('class.user');
		$hika_user = $class->get($user->id, 'cms');

		$auctionClass = hikaauction::get('class.auction');
		$currencyClass = hikashop_get('class.currency');

		$app = JFactory::getApplication();
		$config =& hikashop_config();
		$product_id = JRequest::getInt('product_id');

		$productClass = hikashop_get('class.product');
		$productClass->getProducts($product_id);
		$fullProduct = $productClass->products[$product_id];

		if(!empty($fullProduct->product_tax_id)){
			$main_tax_zone = explode(',',$config->get('main_tax_zone',''));
			if(count($main_tax_zone)){
				$main_tax_zone = array_shift($main_tax_zone);
			}
		}

		if(!empty($fullProduct->product_tax_id)){
			foreach($fullProduct->prices as $key => $price){
				$fullProduct->prices[$key]->price_value_with_tax = $currencyClass->getTaxedPrice($fullProduct->prices[0]->price_value,$main_tax_zone,$fullProduct->product_tax_id);
			}
		}else{
			foreach($fullProduct->prices as $key => $price){
				$fullProduct->prices[$key]->price_value_with_tax = $price->price_value;
			}
		}

		$ret = $auctionClass->cancelUserAuction($fullProduct->product_id, $hika_user->user_id);

		$newWinner = $auctionClass->getUsersWithMaxBid($fullProduct->product_id);

		if(isset($newWinner)){
			$starting_price_amount = $auctionClass->getAuctionStartingPrice($fullProduct->prices[0]);
			$starting_price = $currencyClass->format($starting_price_amount, $fullProduct->prices[0]->price_currency_id);
			$current_price_amount = $auctionClass->getAuctionCurrentPrice($product_id,$starting_price_amount, $newWinner);
			$current_price = $currencyClass->format($current_price_amount, $fullProduct->prices[0]->price_currency_id);

			$queueClass = hikaauction::get('class.queue');
			$queueClass->createQueue((int)$fullProduct->product_id, (int)$newWinner, 'mail', array('auction_finished_winner_notification', $current_price_amount));
		}
		JRequest::setVar('layout', 'cancel_auction');
		return parent::display();
	}

	function pay_auction(){
		if(!$this->_checkLogin()) return true;

		$app = JFactory::getApplication();
		$config =& hikashop_config();
		$product_id = JRequest::getInt('product_id');

		$user = JFactory::getUser();
		$class = hikashop_get('class.user');
		$hika_user = $class->get($user->id, 'cms');

		$productClass = hikashop_get('class.product');
		$auctionClass = hikaauction::get('class.auction');
		$address_id = JRequest::getInt('auction_user_address');
		$addressClass = hikashop_get('class.address');
		$currencyClass = hikashop_get('class.currency');
		$paymentClass = hikashop_get('class.payment');
		$productClass->getProducts($product_id);
		$fullProduct = $productClass->products[$product_id];

		if(!empty($fullProduct->product_tax_id)){
			$main_tax_zone = explode(',',$config->get('main_tax_zone',''));
			if(count($main_tax_zone)){
				$main_tax_zone = array_shift($main_tax_zone);
			}
		}

		if(!empty($fullProduct->product_tax_id)){
			foreach($fullProduct->prices as $key => $price){
				$fullProduct->prices[$key]->price_value_with_tax = $currencyClass->getTaxedPrice($fullProduct->prices[0]->price_value,$main_tax_zone,$fullProduct->product_tax_id);
			}
		}else{
			foreach($fullProduct->prices as $key => $price){
				$fullProduct->prices[$key]->price_value_with_tax = $price->price_value;
			}
		}

		$current_winner_id = $auctionClass->getUsersWithMaxBid($product_id);

		if(empty($user->id) || empty($hika_user->user_id) || empty($address_id) || empty($current_winner_id) || $hika_user->user_id != $current_winner_id){
			$app->enqueueMessage(JText::_('HIKA_AUCTION_PAYMENT_ERROR'), 'error');
			$app->redirect(hikashop_completeLink('order'));
		}

		$starting_price_amount = $auctionClass->getAuctionStartingPrice($fullProduct->prices[0]);
		$starting_price = $currencyClass->format($starting_price_amount, $fullProduct->prices[0]->price_currency_id);
		$current_price_amount = $auctionClass->getAuctionCurrentPrice($product_id,$starting_price_amount, $current_winner_id);
		$current_price = $currencyClass->format($current_price_amount, $fullProduct->prices[0]->price_currency_id);

		$class->loadPartnerData($hika_user);
		$order = new stdClass();
		$order->history = new stdClass();
		$order->order_status = $config->get('order_created_status','created');
		$order->history->history_reason = JText::sprintf('ORDER_CREATED');
		$order->history->history_notified = 0;
		$order->history->history_type = 'creation';
		$order->order_type = 'sale';

		$order->total = new stdClass();
		$order->total->prices[0] = new stdClass();
		$order->total->prices[0] = $fullProduct->prices[0];

		$order->order_full_price = $current_price_amount;
		$order->order_shipping_address_id = $address_id;
		$order->order_billing_address_id = $address_id;
		$product = new stdClass();
		$product->order_product_name = $fullProduct->product_name;
		$product->order_product_code = $fullProduct->product_code;
		$product->order_product_price = floatval($current_price_amount);
		$product->order_product_quantity =1;
		$product->order_product_tax = 0;
		$product->order_product_options = '';
		$product->product_id = $product_id;
		$order->cart = new stdClass();
		$order->order_user_id = $hika_user->user_id;
		$order->cart->products = array($product);
		$orderClass = hikashop_get('class.order');

		$pluginsPayment = $paymentClass->getPayments($order);

		if(!is_array($pluginsPayment) || !$pluginsPayment){
			$app->enqueueMessage(JText::_('NO_PAYMENT_METHODS_FOUND'), 'error');
			$app->redirect(hikashop_completeLink('order'));
		}

		if(hikashop_level(1) && count($pluginsPayment) > 1) {
			foreach($pluginsPayment as $payment_method){
				$order->order_payment_id = $payment_method->payment_id;
				$order->order_payment_method = $payment_method->payment_type;
				$order->order_id = $orderClass->save($order);


				$app->redirect(hikashop_completeLink('order'));
			}
		} else if(count($pluginsPayment) >= 1) {
			foreach($pluginsPayment as $payment_method){
				$order->order_payment_id = $payment_method->payment_id;
				$order->order_payment_method = $payment_method->payment_type;
				$order->order_id = $orderClass->save($order);


				$pay_url = hikashop_completeLink('order&task=pay&order_id='.$order->order_id);
				if($config->get('force_ssl',0) && strpos('https://',$pay_url) === false)
					$pay_url = str_replace('http://','https://',HIKASHOP_LIVE) . 'index.php?option=com_hikashop&ctrl=order&task=pay&order_id='.$order->order_id;
				$app->redirect($pay_url);
			}
		} else {
			$app->enqueueMessage(JText::_('NO_PAYMENT_METHODS_FOUND'), 'error');
			$app->redirect(hikashop_completeLink('order'));
		}

		JRequest::setVar( 'layout', 'show_auction_error'  );
		return parent::display();
	}

	function _checkLogin(){
		$user = JFactory::getUser();
		if ($user->guest) {
			$app=JFactory::getApplication();
			$app->enqueueMessage(JText::_('PLEASE_LOGIN_FIRST'));
			global $Itemid;
			$url = '';
			if(!empty($Itemid)){
				$url='&Itemid='.$Itemid;
			}
			if(!HIKASHOP_J16){
				$url = 'index.php?option=com_user&view=login'.$url;
			}else{
				$url = 'index.php?option=com_easysocial&view=login'.$url;
			}
			$app->redirect(JRoute::_($url.'&return='.urlencode(base64_encode(hikashop_currentUrl('',false))),false));
			return false;
		}
		return true;
	}
}

?>
