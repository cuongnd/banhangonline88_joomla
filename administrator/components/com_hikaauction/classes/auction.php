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
class hikaauctionAuctionClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	protected function getProductsIds($products) {
		$product_ids = array();
		foreach($products as $product) {
			if(is_int($product))
				$product_ids[] = $product;
			elseif(is_object($product))
				$product_ids[] = (int)$product->product_id;
			else
				$product_ids[] = (int)$product;
		}

		if(empty($product_ids))
			return false;
		return $product_ids;
	}

	public function getProductsPrices($products, $remove_first = true) {
		$product_ids = $this->getProductsIds($products);
		if($product_ids === false)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('pr.*')
			->from(hikaauction::table('shop.price').' AS pr')
			->where(array(
				'price_product_id IN ('.implode(',',$product_ids).')',
				'price_access = '.$this->db->Quote('all')
			))
			->order('price_min_quantity ASC');
		if($remove_first)
			$query->where('price_min_quantity > 1');

		$this->db->setQuery($query);
		$prices = $this->db->loadObjectList();

		return $prices;
	}

	public function getProductPrices($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('pr.*')
			->from(hikaauction::table('shop.price').' AS pr')
			->where(array(
				'price_product_id = '.(int)$product_id,
				'price_access = '.$this->db->Quote('all'),
			))
			->order('price_min_quantity ASC');
		$this->db->setQuery($query);
		$prices = $this->db->loadObjectList();

		return $prices;
	}

	public function getAuctionsStatus($products) {
		$config = hikaauction::config();
		$confirm_statuses = explode(',', $config->get('confirm_status', 'confirmed,shipped'));

		$product_ids = $this->getProductsIds($products);
		if($product_ids === false)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select(array(
				'op.product_id',
				'SUM(op.order_product_quantity) AS quantity'
			))
			->from(hikaauction::table('shop.order_product').' AS op')
			->innerjoin(hikaauction::table('shop.order').' AS o ON o.order_id = op.order_id')
			->where(array(
				'op.product_id IN ('.implode(',', $product_ids).')',
				'o.order_status IN ('.$this->dbHelper->implode($confirm_statuses).')'
			));
		$this->db->setQuery($query);
		$auctionStatuses = $this->db->loadObjectList('product_id');

		return $auctionStatuses;
	}

	public function closeAuction($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$product_id = (int)$product_id;

		$config =& hikashop_config();
		$productClass = hikashop_get('class.product');
		$productClass->getProducts($product_id);
		if(empty($this->currencyHelper))
			$this->currencyHelper = hikaauction::get('shop.class.currency');
		$fullProduct = $productClass->products[$product_id];

	 	if(!empty($fullProduct->product_tax_id)){
			$main_tax_zone = explode(',',$config->get('main_tax_zone',''));
			if(count($main_tax_zone)){
				$main_tax_zone = array_shift($main_tax_zone);
			}
		}

		if(!empty($fullProduct->product_tax_id)){
			foreach($fullProduct->prices as $key => $price){
				$fullProduct->prices[$key]->price_value_with_tax = $this->currencyHelper->getTaxedPrice($fullProduct->prices[0]->price_value,$main_tax_zone,$fullProduct->product_tax_id);
			}
		}else{
			foreach($fullProduct->prices as $key => $price){
				$fullProduct->prices[$key]->price_value_with_tax = $price->price_value;
			}
		}

		$current_winner_id = $this->getUsersWithMaxBid($product_id);

		$current_loosers_id = $this->getAllBiders($product_id);
		foreach($current_loosers_id as $key => $valid_bidder){
			if($valid_bidder->auction_bidder_id == $current_winner_id)
				unset($current_loosers_id[$key]);
		}

		$starting_price_amount = $this->getAuctionStartingPrice($fullProduct->prices[0]);
		$starting_price = $this->currencyHelper->format($starting_price_amount, $fullProduct->prices[0]->price_currency_id);
		$current_price_amount = $this->getAuctionCurrentPrice($product_id,$starting_price_amount, $current_winner_id);
		$current_price = $this->currencyHelper->format($current_price_amount, $fullProduct->prices[0]->price_currency_id);

		$nb = $this->getNumberofBids($product_id);

		$bid_number = (int)$nb;

		$config = hikaauction::config();
		$queueClass = hikaauction::get('class.queue');

		$query = $this->dbHelper->getQuery(true);
		$query->update(hikaauction::table('shop.product'))
			->where(array(
				'product_id = ' . (int)$product_id,
				'product_auction = 1'
			));
		if($bid_number == 0) {
			$query->set('product_auction = 3');
		} else {
			$query->set('product_auction = 2');
		}
		$this->db->setQuery($query);
		$this->db->query();


		if($bid_number <= 0)
			return true;

		if(empty($this->mailClass))
			$this->mailClass = hikaauction::get('class.mail');

		$mail = $this->mailClass->loadInfos('auction_finished_winner_notification');
		if(!empty($mail) && !empty($mail->published)) {
			if(empty($queueClass))
				$queueClass = hikaauction::get('class.queue');

			if(isset($current_winner_id))
				$queueClass->createQueue((int)$product_id, (int)$current_winner_id, 'mail', array('auction_finished_winner_notification', $current_price));
		}

		$mail2 = $this->mailClass->loadInfos('auction_finished_bidders_notification');
		if(!empty($mail2) && !empty($mail2->published)) {
			if(empty($queueClass))
				$queueClass = hikaauction::get('class.queue');

			foreach($current_loosers_id as $looser){
				if(empty($looser->auction_bidder_id) || (int)$looser->auction_bidder_id <= 0)
					continue;
				$queueClass->createQueue((int)$product_id, (int)$looser->auction_bidder_id, 'mail', array('auction_finished_bidders_notification', $current_price));
			}
		}

		return true;
	}

	public function saveBid($product_id, $amount, $user_id) {
		if(empty($product_id) || empty($user_id) || (int)$product_id <= 0 || (int)$user_id <= 0 || empty($amount) || (float)hikaauction::toFloat($amount) <= 0)
			return false;

		$data = array(
			'auction_created' => time(),
			'auction_amount' => (float)hikaauction::toFloat($amount),
			'auction_bidder_id' => (int)$user_id,
			'auction_product_id' => (int)$product_id,
			'auction_status' => (int)1
		);

		$query = $this->dbHelper->getQuery(true);
		$query->insert(hikaauction::table('auction'))
			->columns($query->quoteName(array_keys($data)))
			->values($this->dbHelper->implode($data));
		$this->db->setQuery($query);
		$ret = $this->db->query();

		return $ret;
	}

	public function getNumberofBids($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('COUNT(*)')
			->from(hikaauction::table('auction'))
			->where('auction_product_id = ' . (int)$product_id);

		$this->db->setQuery($query);
		$nb = $this->db->loadResult();
		return $nb;
	}

	public function getNumberofBidders($product_id, $valid=FALSE) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		if(!isset($valid) || $valid === FALSE) {
		$query->select('COUNT(DISTINCT auction_bidder_id)')
			->from(hikaauction::table('auction'))
			->where('auction_product_id = ' . (int)$product_id);
		} elseif($valid === TRUE) {
			$query->select('COUNT(DISTINCT auction_bidder_id)')
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_status = 1'
			));
		}

		$this->db->setQuery($query);
		$nb = $this->db->loadResult();
		return $nb;
	}

	public function findCurrentPrice($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('auction_amount')
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_status = 1'
			))
			->order('auction_amount DESC');

		$this->db->setQuery($query,0,2);
		$results = $this->db->loadObjectList();
		return $results;
	}

	public function getMaxBidOfUser($product_id, $user_id) {
		if(empty($product_id) || empty($user_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('MAX(auction_amount)')
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_bidder_id = ' . (int)$user_id,
				'auction_status = 1'
			));

		$this->db->setQuery($query);
		$nb = $this->db->loadResult();
		return $nb;
	}

	public function getUsersWithMaxBid($product_id){
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select(array(
				'auction_bidder_id',
				'auction_amount'
			))
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_status = 1'
			))
			->order('auction_amount DESC, auction_created DESC');

		$this->db->setQuery($query,0,2);
		$usersMax = $this->db->loadObjectList();



		if(empty($usersMax)){
			return false;
		}

		if(count($usersMax) == 1 || $usersMax[0]->auction_amount > $usersMax[1]->auction_amount)
			$userMax = (int)$usersMax[0]->auction_bidder_id;
		else
			$userMax = (int)$usersMax[1]->auction_bidder_id;

		return $userMax;
	}

	public function getAllBiders($product_id){
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('DISTINCT auction_bidder_id')
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' .(int)$product_id,
				'auction_status = 1'
			));

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();
		return $results;
	}

	public function getMaxBid($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('MAX(auction_amount)')
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_status = 1'
			));

		$this->db->setQuery($query);
		$max_bid = $this->db->loadResult();

		if(isset($max_bid))
			return $max_bid;
		else
			return false;
	}

	public function getAuctionHistory($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select(array(
				'a.auction_amount',
				'a.auction_created',
				'us.name',
				'us.username'
			))
			->from(hikaauction::table('auction') .' AS a')
			->innerjoin(hikaauction::table('shop.user').' AS u ON a.auction_bidder_id = u.user_id')
			->innerjoin(hikashop_table('users', false).' AS us ON u.user_cms_id = us.id')
			->where('auction_product_id = ' .(int)$product_id)
			->order('a.auction_amount DESC, a.auction_created DESC');

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();
		return $results;
	}

	public function cancelUserAuction($product_id, $user_id) {
		if(empty($product_id) || empty($user_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->update(hikaauction::table('auction'))
			->set('auction_status = 0')
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_bidder_id = ' . (int)$user_id
			));

		$this->db->setQuery($query);
		$this->db->query();
		return true;
	}

	public function cancelAuction($product_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->update(hikaauction::table('auction'))
			->set('auction_status = 0')
			->where('auction_product_id = ' . (int)$product_id);

		$this->db->setQuery($query);
		$this->db->query();
		return true;
	}
	public function getBidIncrement() {
		$auctionConfig = hikaauction::config();
		$bid_increment = $auctionConfig->get('bid_increment', 1);

		return $bid_increment;
	}

	public function getAuctionCurrentPrice($product_id, $product_price, $current_winner_id) {
		if(empty($product_id) || (int)$product_id <= 0)
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('auction_amount, auction_bidder_id')
			->from(hikaauction::table('auction'))
			->where(array(
				'auction_product_id = ' . (int)$product_id,
				'auction_status = 1'
			))
			->order('auction_amount DESC');

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		$nbBidder = $this->getNumberofBidders($product_id, TRUE);

		if(!isset($results) || empty($results) || empty($current_winner_id) || (int)$current_winner_id <= 0 || $nbBidder <= 1)
			return $product_price;

		$issetCurrentPrice = FALSE;
		foreach($results as $bid){
			if($current_winner_id != $bid->auction_bidder_id && $issetCurrentPrice === FALSE){
				$current_price_amount = $bid->auction_amount;
				$issetCurrentPrice = TRUE;
			}
		}

		return $current_price_amount;
	}

	public function getAuctionBiddingPrice($current_price, $starting_price, $maxBidOfUser, $number_bids, $bidding_mode, $bid_increment) {

		$bid_amount = $current_price;

		switch ($bidding_mode) {
			case 'bid_increment_bidding':
				if (is_null($maxBidOfUser)  || (isset($maxBidOfUser) && ($current_price >= $maxBidOfUser))){
					if($current_price == $starting_price && $number_bids == 0)
						$bid_amount = $current_price;
					else
						$bid_amount = $current_price + $bid_increment;
				}
				elseif(isset($maxBidOfUser))
					$bid_amount = $maxBidOfUser + $bid_increment;
				break;

			case 'current_price_bidding':
				if (is_null($maxBidOfUser)  || (isset($maxBidOfUser) && ($current_price >= $maxBidOfUser)))
						$bid_amount = $current_price;
				elseif(isset($maxBidOfUser))
					$bid_amount = $maxBidOfUser;
				break;
		}

		return $bid_amount;
	}

	public function getAuctionStartingPrice($product_price){
		if(isset($product_price->price_value))
			$startingPriceAmount = $product_price->price_value;
		if(isset($product_price->price_value_with_tax))
			$startingPriceAmount = $product_price->price_value_with_tax;
		if(isset($product_price->price_value_without_discount_with_tax))
			$startingPriceAmount = $product_price->price_value_without_discount_with_tax;
		if(@$product_price->price_value_with_tax == @$product_price->price_value && isset($product_price->price_value_without_discount))
			$startingPriceAmount = $product_price->price_value_without_discount;

		return $startingPriceAmount;
	}

	public function getAuctionStatus($product){
		$now = time();
		if($product->product_sale_end > $now)
			$auction_finished = false;
		else
			$auction_finished = true;

		if($product->product_auction > 1)
			$auction_finished = true;

		return $auction_finished;
	}
}
