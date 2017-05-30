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
class hikaauctionQueueClass extends hikaauctionClass {

	protected $tables = array('queue');
	protected $pkeys = array('queue_id');
	protected $toggle = array();

	public function createQueue($product_id, $user_id, $type, $data) {
		$config = hikaauction::config();

		$data = array(
			'queue_user_id' => (int)$user_id,
			'queue_product_id' => (int)$product_id,
			'queue_created' => time(),
			'queue_type' => $type,
			'queue_data' => serialize($data)
		);

		$query = $this->dbHelper->getQuery(true);
		$query->insert(hikaauction::table('queue'))
			->columns($query->quoteName(array_keys($data)))
			->values($this->dbHelper->implode($data));
		$this->db->setQuery($query);
		$ret = $this->db->query();
	}

	public function getItems($limit = 10, $filter = null) {
		$query = $this->dbHelper->getQuery(true);
		$query->select('*')
			->from(hikaauction::table('queue'))
			->order('queue_created ASC');

		if($filter !== null) {
			if(is_string($filter))
				$query->where('queue_type = ' . $query->Quote($filter));
			if(is_array($filter))
				$query->where('queue_type IN (' . $this->dbHelper->implode($filter) . ')');
		}

		$this->db->setQuery($query, 0, $limit);
		$items = $this->db->loadObjectList();

		return $items;
	}

	public function getMailItems($limit = 20) {
		$query = $this->dbHelper->getQuery(true);
		$query->select('*')
			->from(hikaauction::table('queue'))
			->order('queue_created ASC')
			->where('queue_type = ' . $query->Quote('mail'));

		$this->db->setQuery($query, 1);
		$item = $this->db->loadObject();

		if(empty($item))
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('*')
			->from(hikaauction::table('queue'))
			->where(array(
				'queue_type = ' . $query->Quote('mail'),
				'queue_product_id = ' . (int)$item->queue_product_id,
				'queue_data = ' . $query->Quote($item->queue_data)
			));

		$this->db->setQuery($query, $limit);
		$items = $this->db->loadObjectList();

		return $items;
	}
}
