<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class productserialViewProductserial extends hikaserialView {

	const ctrl = 'productserial';
	const name = 'HIKASERIAL_PRODUCTSERIAL';
	const icon = 'generic';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function market_block($params = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$this->loadRef(array(
			'nameboxType' => 'shop.type.namebox'
		));

		$data = null;
		$product_id = 0;

		if(!empty($params)) {
			$product_id = (int)$params->get('product_id');
		}

		if($product_id > 0) {
			$query = 'SELECT a.*, b.* FROM ' . hikaserial::table('product_pack') . ' as a INNER JOIN ' . hikaserial::table('pack') . ' as b ON a.pack_id = b.pack_id WHERE a.product_id = ' . $product_id;
			$db->setQuery($query);
			$data = $db->loadObjectList();
		}

		$this->assignRef('data', $data);
		$this->assignRef('product_id', $product_id);
	}
}
