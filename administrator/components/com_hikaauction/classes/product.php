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
class hikaauctionProductClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	public function  __construct($config = array()) {
		parent::__construct($config);
		$this->config = hikaauction::config();
	}

	public function checkForm(&$product, &$do) {
		if(empty($product->product_auction) && empty($product->old->product_auction))
			return;

		$old_product_auction = 0;
		if(isset($product->old->product_auction))
			$old_product_auction = (int)$product->old->product_auction;

		$app = JFactory::getApplication();
		if(!$app->isAdmin()) {
			if(!hikaauction::initMarket())
				return;

			if(!hikamarket::acl('product/edit/plugin/hikaauction')) {
				unset($product->product_auction);
				if((int)$old_product_auction > 1) {
					unset($product->product_sale_start);
					unset($product->product_sale_end);
				}
				return;
			}
		}

		if(empty($product->product_auction))
			return;
		$product_auction = (int)$product->product_auction;

		$currencies = array();
		foreach($product->prices as $price) {
			$currencies[ (int)$price->price_currency_id ] = (int)$price->price_currency_id;
		}
		if(count($currencies) > 1) {
			$app->enqueueMessage(JText::_('HIKA_AUCTION_SEVERAL_CURRENCIES_FOR_PRODUCT'), 'error');
			$do = false;
		}

		if($old_product_auction > 1) {
			unset($product->product_auction);

			$err = false;
			if($product_auction <= 1 && $product_auction != $old_product_auction) {
				$app->enqueueMessage(JText::_('HIKA_AUCTION_EDIT_ERROR_ALREADY_FINISH'), 'error');
				$err = true;
			}
			if(isset($product->product_sale_start) && $product->product_sale_start != $product->old->product_sale_start) {
				if(!$err)
					$app->enqueueMessage(JText::_('HIKA_AUCTION_EDIT_ERROR_ALREADY_FINISH'), 'error');
				$err = true;
				unset($product->product_sale_start);
			}
			if(isset($product->product_sale_end) && $product->product_sale_end != $product->old->product_sale_end) {
				if(!$err)
					$app->enqueueMessage(JText::_('HIKA_AUCTION_EDIT_ERROR_ALREADY_FINISH'), 'error');
				$err = true;
				unset($product->product_sale_end);
			}
			return;
		}

		if($product_auction > 1)
			$product_auction = 1;

		if(empty($product->product_sale_start) && empty($product->old->product_sale_start))
			$product->product_sale_start = time();

		if(isset($product->product_sale_end) && empty($product->product_sale_end)) {
			if(empty($product->old->product_sale_end)) {
				$product->product_sale_end = time() + (24 * 3600);
				$app->enqueueMessage(JText::_('HIKA_AUCTION_EDIT_INFO_SALE_END_SET'));
			} else {
				unset($product->product_sale_end);
				$app->enqueueMessage(JText::_('HIKA_AUCTION_EDIT_INFO_SALE_END_RESET'));
			}
		}

		$max_duration = (int)$this->config->get('maxduration', 30);
		if($max_duration > 0 && !empty($product->product_sale_end) && !empty($product->product_sale_start)) {
			$duration = ($product->product_sale_end - $product->product_sale_start) / (24 * 3600);
			if($duration > $max_duration) {
				$do = false;
				$app->enqueueMessage(JText::sprintf('HIKA_AUCTION_DURATION_MORE_THAN_X_DAYS', $max_duration));
			}
		}

		if($product->product_quantity != 1) {
			$do = false;
			$app->enqueueMessage(JText::sprintf('HIKA_AUCTION_PRODUCT_QUANTITY_MORE_THAN_ONE'));
		}

		if($old_product_auction != $product_auction && $product_auction == 0) {
			$auctionClass = hikaauction::get('class.auction');
			$users = $auctionClass->getAllBiders($product->product_id);
			if(empty($mailClass))
				$mailClass = hikaauction::get('class.mail');
			$mail = $mailClass->loadInfos('auction_cancelled');
			if(!empty($mail) && !empty($mail->published)) {
				if(empty($queueClass))
					$queueClass = hikaauction::get('class.queue');
				foreach($users as $value) {
					$queueClass->createQueue((int)$product->product_id, (int)$value->auction_bidder_id, 'mail', array('auction_cancelled'));
				}
			}
		}
	}

	public function saveForm(&$product) {
		if(!isset($product->product_id))
			return;
		$product_id = (int)$product->product_id;
		$auctionClass = hikaauction::get('class.auction');

		$app = JFactory::getApplication();
		if(!$app->isAdmin()) {
			if(!hikaauction::initMarket())
				return;

			if(!hikamarket::acl('product/edit/plugin/hikaauction'))
				return;
		}
	}

	public function beforePriceCalculate(&$product) {
		if(empty($product->product_auction))
			return;

		$price_mode = $this->config->get('pricemode', 'first');

		if($price_mode == 'max') {
			foreach($product->prices as $k => $p) {
				if($p->price_min_quantity > 1)
					unset($product->prices[$k]);
			}
		} else if($price_mode == 'reach') {
			if(empty($this->auctionClass))
				$this->auctionClass = hikaauction::get('class.auction');

			static $auctionCache = array();
			$pid = (int)$product->product_id;
			if(!isset($auctionCache[$pid]))
				$auctionCache[$pid] = (int)$this->auctionClass->getauctionStatus($pid);
			$i = 0;
			foreach($product->prices as $k => $p) {
				if((int)$p->price_min_quantity < $auctionCache[$pid])
					$i = (int)$k;
			}
			foreach($product->prices as $k => &$p) {
				if($k != $i)
					unset($product->prices[$k]);
				else
					$p->price_min_quantity = 1;
			}
			unset($p);
		} else {
			$i = 0;
			$c = count($product->prices);
			foreach($product->prices as $k => &$p) {
				if($c > 1 && $i++ != 1)
					unset($product->prices[$k]);
				else
					$p->price_min_quantity = 1;
			}
			unset($p);
		}

	}

	public function processView(&$viewObj) {
		if(empty($viewObj->element->product_auction))
			return;
		$auctionConfig = hikaauction::config();
		$auctionClass = hikaauction::get('class.auction');
		$viewObj->user = hikashop_loadUser();

		$viewObj->addTemplatePath(HIKAAUCTION_FRONT . 'views' . DS . 'productauction' . DS . 'tmpl' . DS);

		$app = JFactory::getApplication();
		$viewObj->addTemplatePath(JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . HIKAAUCTION_COMPONENT . DS . 'productauction');

		$viewObj->productlayout = 'show_auction';

		$dbHelper = hikaauction::get('helper.database');
		$db = $dbHelper->get();

		$db->setQuery('SELECT id FROM '.hikashop_table('menu',false).' WHERE link=\'index.php?option=com_hikaauction&view=productauction&layout=bid\'');
		$bid_itemid = $db->loadResult();
		$viewObj->bid_itemid = '';
		if(!empty($bid_itemid))
			$viewObj->bid_itemid = '&Itemid='. $bid_itemid;

		$db->setQuery('SELECT id FROM '.hikashop_table('menu',false).' WHERE `link` LIKE \'%index.php?option=com_users%\'');
		$login_itemid = $db->loadResult();
		$viewObj->login_itemid = '';
		if(!empty($login_itemid))
			$viewObj->login_itemid = '&Itemid='. $login_itemid;

		$viewObj->current_winner_id = $auctionClass->getUsersWithMaxBid($viewObj->element->product_id);
		$viewObj->number_bids = $auctionClass->getNumberofBids($viewObj->element->product_id);
		$viewObj->number_bidders = $auctionClass->getNumberofBidders($viewObj->element->product_id);
		if(isset($viewObj->element->product_bid_increment) && $viewObj->element->product_bid_increment > 0)
			$viewObj->bid_increment = (int)$viewObj->element->product_bid_increment;
		else
			$viewObj->bid_increment = $auctionClass->getBidIncrement();

		$viewObj->bidding_mode = $auctionConfig->get('bidding_mode', 'bid_increment_bidding');

		if(!empty($viewObj->user))
			$viewObj->maxBidOfUser = $auctionClass->getMaxBidOfUser($viewObj->element->product_id,$viewObj->user);

		$viewObj->max_bid = $auctionClass->getMaxBid($viewObj->element->product_id);
		$viewObj->starting_price_amount = $auctionClass->getAuctionStartingPrice($viewObj->element->prices[0]);
		$viewObj->starting_price = $viewObj->currencyHelper->format($viewObj->starting_price_amount, $viewObj->element->prices[0]->price_currency_id);

		$viewObj->auction_currency = $viewObj->currencyHelper->get($viewObj->element->prices[0]->price_currency_id);

		if(!$viewObj->max_bid)
			$viewObj->max_bid = $viewObj->starting_price_amount;

		$viewObj->current_price_amount = $auctionClass->getAuctionCurrentPrice($viewObj->element->product_id,$viewObj->starting_price_amount, $viewObj->current_winner_id);
		$viewObj->current_price = $viewObj->currencyHelper->format($viewObj->current_price_amount, $viewObj->element->prices[0]->price_currency_id);


		$viewObj->auction_finished = $auctionClass->getAuctionStatus($viewObj->element);

		$viewObj->basePrice = $viewObj->element->prices[0];

		if(hikashop_loadUser() && !$viewObj->auction_finished){
			$viewObj->bid_amount = $auctionClass->getAuctionBiddingPrice($viewObj->current_price_amount, $viewObj->starting_price_amount, $viewObj->maxBidOfUser, $viewObj->number_bids, $viewObj->bidding_mode, $viewObj->bid_increment);
			$viewObj->bid_price = $viewObj->currencyHelper->format($viewObj->bid_amount, $viewObj->basePrice->price_currency_id);

			if($viewObj->max_bid == $viewObj->starting_price_amount || $viewObj->bidding_mode != 'bid_increment_bidding')
				$viewObj->priceBase = $viewObj->current_price_amount;
			else
				$viewObj->priceBase = $viewObj->current_price_amount + $viewObj->bid_increment;
		}

		$viewObj->display_nb_bidders = $auctionConfig->get('display_nb_bidders',0);
		$viewObj->display_nb_bid = $auctionConfig->get('display_nb_bid',0);
		$viewObj->display_starting_auction_price = $auctionConfig->get('display_starting_auction_price',0);
		$viewObj->show_auction_history_in_page = $auctionConfig->get('show_auction_history_in_page',0);
	}
}
