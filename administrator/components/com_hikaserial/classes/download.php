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
class hikaserialDownloadClass extends hikaserialClass {

	protected function getOrderFileSerials(&$file) {
		$config = hikaserial::config();
		$display_serial_statuses = $config->get('display_serial_statuses','');
		if(empty($display_serial_statuses)) {
			$display_serial_statuses = explode(',', $config->get('used_serial_status','assigned,used'));
		} else {
			$display_serial_statuses = explode(',', $display_serial_statuses);
		}
		$statuses = array();
		foreach($display_serial_statuses as $s) {
			$statuses[] = $this->db->Quote($s);
		}

		$app = JFactory::getApplication();
		if(empty($file->order_id) && $app->isAdmin())
			$file->order_id = JRequest::getInt('order_id', 0);

		$serials = array();
		if(!empty($file->order_id)) {
			$query = 'SELECT s.*, p.*, op.product_id '.
				' FROM ' . hikaserial::table('serial') . ' AS s '.
				' INNER JOIN ' . hikaserial::table('pack') . ' AS p ON s.serial_pack_id = p.pack_id '.
				' LEFT JOIN ' . hikaserial::table('shop.order_product') . ' AS op ON op.order_product_id = s.serial_order_product_id AND op.order_id = s.serial_order_id '.
				' LEFT JOIN ' . hikaserial::table('shop.product') . ' AS product ON product.product_id = op.product_id '.
				' WHERE s.serial_status IN ('.implode(',',$statuses).') AND s.serial_order_id = '. $file->order_id . ' AND (product.product_id = ' . $file->file_ref_id . ' OR product.product_parent_id = ' . $file->file_ref_id . ' OR op.product_id IS NULL) ' .
				' ORDER BY s.serial_id';
			$this->db->setQuery($query);
			$serials = $this->db->loadObjectList();
		}
		return $serials;
	}

	public function beforeDownloadFile(&$filename, &$do, &$file) {
		$serials = $this->getOrderFileSerials($file);

		$f12 = '';
		if(strlen($filename) >= 12)
			$f12 = substr($filename, 0, 12);
		if( ($f12 == '@hikaserial:' || $f12 == '#hikaserial:') || !empty($serials)) {
			JPluginHelper::importPlugin('hikaserial');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onDisplaySerials', array(&$serials, 'beforeMailSend'));
			$dispatcher->trigger('onBeforeSerialDownloadFile', array(&$filename, &$do, &$file, &$serials));

			if(strlen($filename) >= 11 && ($f12 == '@hikaserial:' || $f12 == '#hikaserial:')) {
				$do = false;
			}
			return;
		}
	}
}
