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
class auctionsViewAuctions extends hikaauctionView {

	const ctrl = 'auctions';
	const name = 'AUCTIONS';
	const icon = 'auctions';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAAUCTION_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}


	public function listing($tpl = null) {
		$app = JFactory::getApplication();
		$dbHelper = hikaauction::get('helper.database');
		$db = $dbHelper->get();
		hikaauction::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaauction::config();
		$this->assignRef('config',$config);

		$toggleHelper = hikaauction::get('helper.toggle');
		$this->assignRef('toggleHelper', $toggleHelper);

		$auctionClass = hikaauction::get('class.auction');

		$filterType = $app->getUserStateFromRequest($this->paramBase.'.filter_type', 'filter_type', 0, 'int');

		$cfg = array(
			'table' => 'product',
			'alias' => 'product',
			'main_key' => 'product_id',
			'order_sql_value' => 'product.product_id'
		);

		$elemStruct = array(
			'id',
			'product_name',
			'product_code'
		);
		$this->assignRef('elemStruct', $elemStruct);

		if(empty($cfg['alias'])) $cfg['alias'] = 'a';

		$pageInfo = $this->getPageInfo($cfg['order_sql_value'], 'asc', array(
			'auction_type' => 0,
			'auction_valid' => 0
		));

		$searchMap = array(
			'product_name',
			'product_code',
		);
		$query = $dbHelper->getQuery(true);

		$this->processFilters($query, $searchMap, array('product.'));
		if($pageInfo->filter->auction_type > 0) {
			$now = time();
			switch($pageInfo->filter->auction_type) {
				case 1:
					$query->where(array(
						'product_sale_start <= '.$now,
						'product_sale_end >= '.$now,
					));
					break;
				case 2:
					$query->where(array(
						'product_sale_end < '.$now,
					));
					break;
				case 3:
					$query->where(array(
						'product_sale_start > '.$now,
					));
					break;
			}
		}

		$query->select('*')
			->from('`'.hikaauction::table('shop.product') . '` AS product')
			->where('product.product_auction > 0');

		$db->setQuery($query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);
		$rows = $db->loadObjectList('product_id');
		$this->assignRef('rows', $rows);

		$products = array_keys($rows);
		JArrayHelper::toInteger($products);

		if(!empty($products)) {
			$prices = $auctionClass->getProductsPrices($products, false);
			foreach($prices as $price) {
				$pid = (int)$price->price_product_id;
				if(!isset($rows[$pid]))
					continue;

				if(empty($rows[$pid]->prices))
					$rows[$pid]->prices = array();
				$rows[$pid]->prices[] = $price;
			}
			unset($prices);

			$statuses = $auctionClass->getAuctionsStatus($products);
			foreach($statuses as $status) {
				$pid = (int)$status->product_id;
				if(!isset($rows[$pid]))
					continue;
				$rows[$pid]->auction_quantity = (int)$status->quantity;
			}
			unset($statuses);
		}

		$query->clear('select');
		$query->select('count(*)');
		$db->setQuery($query);

		$this->pageInfo->elements = new stdClass();
		$this->pageInfo->elements->total = $db->loadResult();
		$this->pageInfo->elements->page = count($rows);

		$this->toolbar = array(
		array('name' => 'pophelp', 'target' => 'listing'),
		'dashboard'
		);

		$this->getPagination();

		$this->getOrdering($cfg['order_sql_value'], !$filterType);
	}
}
