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
class hikaauctionOrderClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	protected function containAuction(&$order) {
		$products = isset($order->products) ? $order->products : $order->cart->products;
		if(empty($products))
			return false;

		$p = reset($products);
		if(!isset($p->product_auction)) {
			$pid = array();
			foreach($products as $product) {
				$pid[] = (int)$product->product_id;
			}
			$query = $this->dbHelper->getQuery(true);
			$query->select('COUNT(*)')
				->from(hikaauction::table('shop.product'))
				->where(array(
					'product_auction > 0',
					'product_id IN ('.implode(',', $pid).')'
				));
			$this->db->setQuery($query);
			$res = (int)$this->db->loadResult();
			return ($res > 0);
		}

		foreach($products as $product) {
			if(!empty($product->product_auction))
				return true;
		}
		return false;
	}

	public function beforeOrderCreate(&$order, &$do) {
		$app = JFactory::getApplication();
		if($app->isAdmin())
			return;

		if($order->order_type != 'sale')
			return;

		if(!$this->containAuction($order))
			return;

		$products = isset($order->products) ? $order->products : $order->cart->products;

		foreach($products as $product) {
			if(!empty($product->product_auction) && (int)$product->product_auction > 1) {
				$do = false;

				$app->enqueueMessage(JText::_('HIKA_ERR_ORDER_CREATION_AUCTION_FINISH'), 'error');
				return;
			}
		}
	}

	public function afterOrderUpdate(&$order) {
		if(empty($order->order_status))
			return;
		if($order->order_status == $order->old->order_status)
			return;

		$config = hikaauction::config();
		$confirm_statuses = explode(',', $config->get('confirm_status', 'confirm,shipped'));
		if(!in_array($order->order_status, $confirm_statuses) || in_array($order->old->order_status, $confirm_statuses))
			return;

		$query = $this->dbHelper->getQuery(true);
		$query->select(array(
				'p.*',
				'op.order_product_quantity'
			))
			->from(hikaauction::table('shop.product').' AS p')
			->innerjoin(hikaauction::table('shop.order_product').' AS op ON p.product_id = op.product_id')
			->where(array(
				'op.order_id = '.(int)$order->order_id,
				'p.product_auction = 1'
			));
		$this->db->setQuery($query);
		$products = $this->db->loadObjectList('product_id');
		if(empty($products))
			return;

		$product_ids = array_keys($products);
		$queueClass = null;
		$mailClass = null;
		$auctionClass = hikaauction::get('class.auction');

		$auctionStatuses = $auctionClass->getAuctionsStatus($product_ids);

		$prices = $auctionClass->getProductsPrices($product_ids);

		foreach($products as $product) {
			$auctionStatus = null;
			if(isset($auctionStatuses[(int)$product->product_id])) {
				$auctionStatus = $auctionStatuses[$product->product_id];
			} else {
				foreach($auctionStatuses as $ds) {
					if((int)$ds->product_id == (int)$product->product_id) {
						$auctionStatuses = $ds;
						break;
					}
				}
			}
			if(empty($auctionStatus))
				continue;

			$cpt = 0;
			foreach($prices as $price) {
				if((int)$price->price_product_id != (int)$product->product_id)
					continue;

				$cpt++;

				if( ((int)$price->price_min_quantity <= (int)$auctionStatus->quantity) && ((int)$price->price_min_quantity > ((int)$auctionStatus->quantity - (int)$product->order_product_quantity))) {
					if(empty($mailClass))
						$mailClass = hikaauction::get('class.mail');

					$mail = $mailClass->loadInfos('auction_updated');
					if(!empty($mail) && !empty($mail->published)) {
						if(empty($queueClass))
							$queueClass = hikaauction::get('class.queue');
						$queueClass->createQueue((int)$product->product_id, 'mail', array(
							'qty' => (int)$auctionStatus->quantity,
							'price' => (float)hikaauction::toFloat($price->price_value)
						));
					}
					break;
				}
			}
		}
	}

	public function queueProcess($item) {
		if(!isset($item->queue_type) || !in_array($item->queue_type, array('reject', 'valid')))
			return false;

		$order_id = (int)$item->queue_order_id;
		$product_id = (int)$item->queue_product_id;

		$config = hikaauction::config();
		$orderClass = hikaauction::get('shop.class.order_product');
		$orderProductClass = hikaauction::get('shop.class.order_product');

		$order = $orderClass->loadFullOrder($order_id, false, false);

		if($item->queue_type == 'reject') {
			foreach($order->products as $product) {
				if($product->product_id == $product_id) {
					if(empty($order->product))
						$order->product = array();
					$p = clone($product);

					$p->order_product_quantity = 0;

					$order->product[] = $p;
				}
			}
		}

		if($item->queue_type == 'valid') {
			foreach($order->products as $product) {
				if($product->product_id == $product_id) {
					if(empty($order->product))
						$order->product = array();
					$p = clone($product);

					$price = (float)$item->queue_data['price'];
					$rate = (float)($price / $p->order_product_price);

					$p->order_product_price = $price;
					$p->order_product_tax *= $rate;

					$order->product[] = $p;
				}
			}
		}

		if(empty($order->hikamarket))
			$order->hikamarket = new stdClass();
		$order->hikamarket->reprocess = true;

		$query = $this->dbHelper->getQuery(true);
		$query->select('COUNT(*)')
			->from(hikaauction::table('shop.order_product').' AS op')
			->innerjoin(hikaauction::table('shop.product').' AS p ON p.product_id = op.product_id')
			->where(array(
				'p.product_auction = 1',
				'op.order_id = '.$order_id
			));
		$this->db->setQuery($query);
		$res = (int)$this->db->loadResult();

		if($res > 0) {
			$orderClass->save($order);
			return true;
		}

		if($order->order_full_price > 0)
			$order->order_status = $config->get('confirm_order_status', 'confirmed');
		else
			$order->order_status = $config->get('cancel_order_status', 'cancelled');

		$orderClass->save($order);

		return true;
	}
}
