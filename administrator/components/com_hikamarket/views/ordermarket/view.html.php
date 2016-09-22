<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class ordermarketViewOrdermarket extends hikamarketView {

	const ctrl = 'order';
	const name = 'HIKAMARKET_ORDERMARKET';
	const icon = 'generic';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function show_order_back_show($params = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikamarket::config();
		$this->assignRef('config', $config);

		$currencyHelper = hikamarket::get('shop.class.currency');
		$this->assignRef('currencyHelper', $currencyHelper);

		$data = null;
		$order_id = 0;

		if(!empty($params)) {
			$order_id = (int)$params->get('order_id');
		} else {
			$order_id = hikamarket::getCID('order_id');
		}

		$ajax = (JRequest::getVar('tmpl', '') == 'component');

		if($order_id > 0) {
			$query = 'SELECT b.*, a.* '.
				' FROM ' . hikamarket::table('shop.order') . ' AS a '.
				' LEFT JOIN ' . hikamarket::table('vendor') . ' AS b ON a.order_vendor_id = b.vendor_id '.
				' WHERE a.order_parent_id = ' . $order_id . ' '.
				' ORDER BY b.vendor_id ASC, a.order_id ASC';
			$db->setQuery($query);
			$data = $db->loadObjectList();

			$refunds = false;
			foreach($data as $d) {
				if($d->order_type !== 'vendorrefund') {
					$refunds = true;
					break;
				}
			}
			if($refunds) {
				foreach($data as &$d) {
					if($d->order_type !== 'subsale')
						continue;
					$m = false;
					$total = (float)hikamarket::toFloat($d->order_vendor_price);
					foreach($data as $o) {
						if($o->order_type == 'vendorrefund' && $o->order_vendor_id == $d->order_vendor_id) {
							$total += (float)hikamarket::toFloat($o->order_vendor_price);
							$m = true;
						}
					}
					if($m)
						$d->order_vendor_price_with_refunds = $total;
				}
				unset($d);
			}
		}
		$this->assignRef('data', $data);
		$this->assignRef('order_id', $order_id);
		$this->assignRef('ajax', $ajax);
	}
}
