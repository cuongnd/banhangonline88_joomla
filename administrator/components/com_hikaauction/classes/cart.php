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
class hikaauctionCartClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	public function onAfterProductQuantityCheck(&$product, &$wantedQuantity, &$quantity, &$cartContent, &$cart_product_id_for_product, &$displayErrors) {
		if(empty($product->product_auction))
			return;

		$config = hikaauction::config();
		if(!$config->get('limit_auction_global_quantity', 0))
			return;

		$min_quantity = (int)$product->product_min_per_order;
		if($min_quantity == 0)
			return;


		$query = $this->dbHelper->getQuery(true);
		$query->select('SUM(op.order_product_quantity)')
			->from(hikaauction::table('shop.order_product').' AS op')
			->innerjoin(hikaauction::table('shop.order').' AS o ON op.order_id = o.order_id')
			->where('o.order_user_id = ' . (int)hikaauction::loadUser());

		if(empty($product->product_parent_id)) {
			$query->where('op.product_id = '.(int)$product->product_id);
		} else {
			$query->innerjoin(hikaauction::table('shop.product').' AS p ON op.product_id = p.product_id')
				->where('p.product_parent_id = '.(int)$product->product_parent_id);
		}

		$this->db->setQuery($query);
		$total = (int)$this->db->loadResult();

		if(($quantity + $total) > $min_quantity)
			$quantity = ($min_quantity - $total);

		if($quantity < 0)
			$quantity = 0;
	}
}
