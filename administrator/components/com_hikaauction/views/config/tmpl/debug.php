<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset style="width:48%;float:left;">
	<legend>Check</legend>
<ul>
	<li><a href="<?php echo hikaauction::completeLink('config&task=debug&action=checkproducts');?>">Check products</a></li>
	<li><a href="<?php echo hikaauction::completeLink('config&task=debug&action=checkpaiements');?>">Check paiement queue</a></li>
	<li><a href="<?php echo hikaauction::completeLink('config&task=debug&action=checkmails');?>">Check mail queue</a></li>
</ul>
</fieldset>
<fieldset style="width:48%;float:left;">
	<legend>Process</legend>
<ul>
	<li><a href="<?php echo hikaauction::completeLink('config&task=debug&action=processproducts');?>">Process products</a></li>
	<li><a href="<?php echo hikaauction::completeLink('config&task=debug&action=processpaiements');?>">Process paiement queue</a></li>
	<li><a href="<?php echo hikaauction::completeLink('config&task=debug&action=processmails');?>">Process mail queue</a></li>
</ul>
</fieldset>
<div style="clear:both;"></div>
<div><?php

$action = JRequest::getCmd('action', '');
if(!empty($action)) {
	$messages = array();

	switch($action) {
		case 'checkproducts':
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
			$messages[] = 'There are '.count($products).' finished auctions ready to be processed';
			break;

		case 'processproducts':
			$cronClass = hikaauction::get('class.cron');
			$cronClass->doChecksTask($messages);
			break;

		case 'checkpaiements':
			$query = $this->dbHelper->getQuery(true);
			$query->select('count(*)')
				->from(hikaauction::table('queue'))
				->order('queue_created ASC')
				->where('queue_type IN (' . $this->dbHelper->implode(array('reject', 'valid')) . ')');
			$this->db->setQuery($query);
			$items = $this->db->loadResult();
			$messages[] = 'There are '.(int)$items.' items in the paiement queue';
			break;

		case 'checkmails':
			$query = $this->dbHelper->getQuery(true);
			$query->select('count(*)')
				->from(hikaauction::table('queue'))
				->order('queue_created ASC')
				->where('queue_type = ' . $this->db->Quote('mail'));
			$this->db->setQuery($query);
			$items = $this->db->loadResult();
			$messages[] = 'There are '.(int)$items.' items in the mail queue';
			break;

		case 'processpaiements':
			$orderQueueItems = $this->config->get('queue_order_items', 10);
			$queueClass = hikaauction::get('class.queue');
			$items = $queueClass->getItems($orderQueueItems, array('reject', 'valid'));
			if(empty($items)) {
				$messages[] = 'There are no items in the paiement queue';
			} else {
				$stats = array();
				$orderClass = hikaauction::get('class.order');
				foreach($items as $item) {
					if(!empty($item->queue_data))
						$item->queue_date = unserialize($item->queue_data);
					$ret = $orderClass->queueProcess($item);
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
			break;

		case 'processmails':
			$mailQueueItems = (int)$this->config->get('queue_mail_items', 20);
			$queueClass = hikaauction::get('class.queue');
			$items = $queueClass->getItems($mailQueueItems, 'mail');
			if(empty($items)) {
				$messages[] = 'There are no items in the mail queue';
			} else {
				$stats = array();
				$mailClass = hikaauction::get('class.mail');
				foreach($items as $item) {
					if(!empty($item->queue_data))
						$item->queue_date = unserialize($item->queue_data);
					$ret = $mailClass->queueProcess($item);
					if($ret) {
						if(empty($stats[ $item->queue_type ]))
							$stats[ $item->queue_type ] = 0;
						$stats[ $item->queue_type ]++;

						$query = $this->dbHelper->getQuery(true);
						$query->delete(hikaauction::table('queue'))
							->where('queue_id = ' . (int)$item->queue_id);
						$this->db->setQuery($query);
						$this->db->query();
					} else {
						$messages[] = 'Error processing mail item '. (int)$item->queue_id;
					}
				}
				foreach($stats as $k => $v) {
					$messages[] = JText::sprintf('HKA_CRON_QUEUE_PROCESS_'.strtoupper($k), $v);
				}
			}
			break;
	}
}

if(!empty($messages)) {
	echo '<p>';
	echo implode('</p><p>', $messages);
	echo '</p>';
}

?></div>
