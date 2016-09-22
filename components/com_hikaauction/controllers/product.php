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
class productAuctionController extends hikaauctionController {

	protected $rights = array(
		'display' => array('show', 'bid'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('show');
	}

	public function bid(){
		$user = hikaauction::loadUser(false);
		if(empty($user))
			return false;

		$app = JFactory::getApplication();
		$config =& hikashop_config();
		$currencyClass = hikashop_get('class.currency');
		$auctionClass = hikaauction::get('class.auction');
		$product_id = JRequest::getInt('product_id');
		$tmp_amount = JRequest::getVar('bid_amount', 0);
		$currency_id = hikashop_getCurrency();
		$auctionConfig = hikaauction::config();

		$bidding_mode = $auctionConfig->get('bidding_mode', 'bid_increment_bidding');

		$current_winner_id = $auctionClass->getUsersWithMaxBid($product_id);

		$amount = preg_replace('/\s+/', '', $tmp_amount);
		$amount = hikaauction::convertNumber($amount);

		$return_url = JRequest::getString('return_url', '');
		if(!empty($return_url))
			$return_url = base64_decode(urldecode($return_url));
		else
			$return_url = hikashop_completeLink('product&task=show&cid='.(int)$product_id);

		$amount = (float)hikaauction::toFloat($amount);
		if(empty($amount)) {
			$app->enqueueMessage(JText::_('HKA_BID_INVALID_AMOUNT'), 'error');
			$app->redirect($return_url);
		}

		$productClass = hikashop_get('class.product');
		$productClass->getProducts($product_id);
		$fullProduct = $productClass->products[$product_id];

		if(isset($fullProduct->product_bid_increment) && $fullProduct->product_bid_increment > 0)
			$bid_increment = (int)$fullProduct->product_bid_increment;
		else
			$bid_increment = $auctionClass->getBidIncrement();

		if(isset($fullProduct->product_sale_end) && $fullProduct->product_sale_end < time()){
			$app->enqueueMessage(JText::_('HIKA_AUCTION_FINISHED'), 'error');
			$app->redirect($return_url);
		}

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

		$starting_price_amount = $auctionClass->getAuctionStartingPrice($fullProduct->prices[0]);
		$starting_price = $currencyClass->format($starting_price_amount, $fullProduct->prices[0]->price_currency_id);
		$current_price_amount = $auctionClass->getAuctionCurrentPrice($product_id,$starting_price_amount, $current_winner_id);
		$current_price = $currencyClass->format($current_price_amount, $fullProduct->prices[0]->price_currency_id);
		$maxBidOfUser = $auctionClass->getMaxBidOfUser($product_id,$user);

		switch ($bidding_mode) {
			case 'bid_increment_bidding':
				if($amount < $starting_price_amount){
					$app->enqueueMessage(JText::sprintf('HKA_YOUR_BID_MUST_BE_HIGHER_OR_EQUAL_TO', $starting_price), 'error');
					$app->redirect($return_url);
				}
				if($starting_price_amount < $current_price_amount && ($amount < $current_price_amount + $bid_increment)){
					$app->enqueueMessage(JText::sprintf('HKA_YOUR_BID_MUST_BE_HIGHER_OR_EQUAL_TO',$current_price_amount + $bid_increment), 'error');
					$app->redirect($return_url);
				}
				$current_bidding_price = $current_price_amount + $bid_increment;
				break;

			case 'current_price_bidding':
				if($amount < $starting_price_amount){
					$app->enqueueMessage(JText::sprintf('HKA_YOUR_BID_MUST_BE_HIGHER_OR_EQUAL_TO', $starting_price), 'error');
					$app->redirect($return_url);
				}
				if($starting_price_amount < $current_price_amount && ($amount < $current_price_amount)){
					$app->enqueueMessage(JText::sprintf('HKA_YOUR_BID_MUST_BE_HIGHER_OR_EQUAL_TO',$current_price_amount), 'error');
					$app->redirect($return_url);
				}
				$current_bidding_price = $current_price_amount;
				break;

			case 'free_bidding':
				if($amount < $starting_price_amount){
					$app->enqueueMessage(JText::sprintf('HKA_YOUR_BID_MUST_BE_HIGHER_OR_EQUAL_TO', $starting_price), 'error');
					$app->redirect($return_url);
				}
				$current_bidding_price = $starting_price_amount;
				break;
		}

		if($amount <= $maxBidOfUser ){
			$app->enqueueMessage(JText::sprintf('HKA_YOU_ALREADY_HAVE_A_PREVIOUS_BID_HIGHER_OR_EQUAL_TO',$amount), 'error');
			$app->redirect($return_url);
		}

		$userMax = $auctionClass->getUsersWithMaxBid($product_id);
		$maxBid = $auctionClass->getMaxBidOfUser($product_id, $userMax);

		if(!empty($userMax) && !empty($maxBid)) {
			if($amount >= $current_bidding_price && $amount <= $maxBid) {
				if($amount < $maxBid && $bidding_mode == 'bid_increment_bidding')
					$newPrice = $currencyClass->format(@$amount + $bid_increment, $currency_id);
				else
					$newPrice = $currencyClass->format(@$amount, $currency_id);

				if(empty($mailClass))
					$mailClass = hikaauction::get('class.mail');

				$mail = $mailClass->loadInfos('auction_price_changed_notification');
				if(!empty($mail) && !empty($mail->published)) {
					if(empty($queueClass))
						$queueClass = hikaauction::get('class.queue');
					$queueClass->createQueue((int)$product_id, (int)$userMax, 'mail', array('auction_price_changed_notification',$newPrice));
				}
			}

			if($amount >= $current_bidding_price && $amount > $maxBid) {
				if($bidding_mode == 'bid_increment_bidding')
					$newPrice = $currencyClass->format($maxBid + $bid_increment, $currency_id);
				else
					$newPrice = $currencyClass->format($maxBid, $currency_id);

				$users = $auctionClass->getAllBiders($product_id);
				if(empty($mailClass))
					$mailClass = hikaauction::get('class.mail');
				$mail = $mailClass->loadInfos('auction_bidders_outbid_notification');
				if(!empty($mail) && !empty($mail->published)) {
					if(empty($queueClass))
						$queueClass = hikaauction::get('class.queue');
					foreach ($users as $value) {
						if($value->auction_bidder_id != $user)
							$queueClass->createQueue((int)$product_id, (int)$value->auction_bidder_id, 'mail', array('auction_bidders_outbid_notification',$newPrice));
					}
				}
			}

			$ret = $auctionClass->saveBid($product_id, $amount, $user);
		}
		else
			$ret = $auctionClass->saveBid($product_id, $amount, $user);

		$app->enqueueMessage(JText::_('HKA_BID_SUBMITTED'), 'success');
		$app->redirect($return_url);
	}

}
