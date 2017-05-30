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
class hikaauctionCronClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	public function doChecksTask(&$messages) {
		$query = $this->dbHelper->getQuery(true);
		$query->select(array(
				'product_id',
				'product_name',
			))
			->from(hikaauction::table('shop.product'))
			->where(array(
				'product_auction = 1',
				'product_sale_end < '.time()
			));
		$this->db->setQuery($query);
		$products = $this->db->loadObjectList();

		if(empty($products))
			return;

		$auctionClass = hikaauction::get('class.auction');
		foreach($products as $product) {
			$ret = $auctionClass->closeAuction((int)$product->product_id);
			if($ret === true)
				$messages[] = JText::sprintf('HIKA_AUCTION_VALIDATED', $product->product_name);
			else if($ret === false)
				$messages[] = JText::sprintf('HIKA_AUCTION_REFUSED', $product->product_name);
			else
				$messages[] = JText::sprintf('HIKA_AUCTION_ERR_CLOSING_AUCTION', $product->product_name);
		}
	}

	public function doQueueTask(&$messages) {
		$config = hikaauction::config();

		$orderQueueItems = $config->get('queue_order_items', 10);
		$mailQueueItems = $config->get('queue_mail_items', 20);

		$queueClass = hikaauction::get('class.queue');
		$items = $queueClass->getItems($orderQueueItems, array('reject', 'valid'));

		if(empty($items)) {
			if($config->get('batch_queue_mails', false))
				return $this->doQueueMailTask($messages);
			$items = $queueClass->getItems($mailQueueItems, 'mail');
		}

		$orderClass = hikaauction::get('class.order');
		$mailClass = hikaauction::get('class.mail');

		$stats = array();
		foreach($items as $item) {
			if(!empty($item->queue_data))
				$item->queue_data = unserialize($item->queue_data);

			if($item->queue_type == 'reject' || $item->queue_type == 'valid') {
				$ret = $orderClass->queueProcess($item);
			}
			if($item->queue_type == 'mail') {
				$ret = $mailClass->queueProcess($item);
			}

			if($ret) {
				if(empty($stats[ $item->queue_type ]))
					$stats[ $item->queue_type ] = 0;
				$stats[ $item->queue_type ]++;

				$query = $this->dbHelper->getQuery(true);
				$query->delete(hikaauction::table('queue'))
					->where('queue_id = ' . (int)$item->queue_id);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		foreach($stats as $k => $v) {
			$messages[] = JText::sprintf('HKA_CRON_QUEUE_PROCESS_'.strtoupper($k), $v);
		}
	}

	public function doQueueMailTask(&$messages) {
		$config = hikaauction::config();
		$batch_queue_mail_items = $config->get('batch_queue_mail_items', 100);
		if($batch_queue_mail_items < 1)
			$batch_queue_mail_items = 100;

		$queueClass = hikaauction::get('class.queue');
		$items = $queueClass->getMailItems($batch_queue_mail_items);

		if(empty($items))
			return;

		$mailClass = hikaauction::get('class.mail');
		$mailClass->batchProcess($items);
	}
}
