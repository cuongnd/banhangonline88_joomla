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
class productauctionViewProductauction extends hikaauctionView {

	const ctrl = 'productauction';
	const name = 'HIKAAUCTION_PRODUCTAUCTION';
	const icon = 'generic';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAAUCTION_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function market_block($params = null) {
		$app = JFactory::getApplication();
		$dbHelper = hikaauction::get('helper.database');
		$db = $dbHelper->get();

		$config = hikaauction::config();
		$this->assignRef('config', $config);

		$data = null;
		$product_id = 0;

		if(!empty($params)) {
			$product_id = (int)$params->get('product_id');
			$data = $params->get('product', null);
		}

		if($product_id > 0 && empty($data)) {
			$query = $dbHelper->getQuery(true);
			$query->select('*')
				->from(hikaauction::table('shop.product'))
				->where('product_id = ' . $product_id);
			$db->setQuery($query);
			$data = $db->loadObject();
		}

		$sales = 0;
		$product_auction = (int)@$data->product_auction;
		if($product_auction > 0) {
			$shopConfig = hikaauction::config(false);
			$order_statuses = explode(',', $shopConfig->get('invoice_order_statuses', 'confirmed,shipped'));

			$query = $dbHelper->getQuery(true);
			$query->select('count(op.order_product_quantity)')
				->from(hikaauction::table('shop.order').' AS ord')
				->innerjoin(hikaauction::table('shop.order_product').' AS op ON ord.order_id = op.order_id')
				->where(array(
					'product_id = ' . $product_id,
					'order_status IN (' . $dbHelper->implode($order_statuses) . ')',
				));
			$db->setQuery($query);
			$sales = (int)$db->loadResult();
		}

		$this->assignRef('data', $data);
		$this->assignRef('sales', $sales);
		$this->assignRef('product_id', $product_id);
	}
}
