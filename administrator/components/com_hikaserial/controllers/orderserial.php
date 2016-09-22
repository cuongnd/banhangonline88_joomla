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
class orderserialController extends hikaserialController {
	protected $type = 'orderserial';
	protected $rights = array(
			'display' => array('display','show','refresh'),
			'add' => array(),
			'edit' => array(),
			'modify' => array('addpack', 'modifypack', 'modifyserial'),
			'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
	}

	public function show() {
		JRequest::setVar('layout', 'show_order_back_show');

		$tmpl = JRequest::getString('tmpl', '');
		if($tmpl === 'component') {
			ob_end_clean();
			parent::display();
			exit;
		}
		return parent::display();
	}

	public function refresh() {
		$app = JFactory::getApplication();
		$orderClass = hikaserial::get('class.order');
		$orderId = hikaserial::getCID('order_id');
		if(empty($orderId)){
			$app->redirect(hikaserial::completeLink('dashboard'));
		}
		$orderClass->refresh($orderId);
		$app->redirect(hikaserial::completeLink('shop.order&task=edit&cid[]='.$orderId, false, true));
	}

	public function addpack() {
		$orderClass = hikaserial::get('class.order');
		$orderId = hikaserial::getCID('order_id');
		$packId = JRequest::getInt('pack', 0);
		$qty = JRequest::getInt('qty', 0);

		if(!empty($orderId) && !empty($packId) && !empty($qty)) {
			$ret = $orderClass->modifyPack($orderId, $packId, $qty, true);
			if($ret) {
				$orderClass->refresh($orderId);
				echo '1';
				exit;
			}
		}
		echo '0';
		exit;
	}

	public function modifypack() {
		$orderClass = hikaserial::get('class.order');
		$order_id = hikaserial::getCID('order_id');
		$pack_id = JRequest::getInt('pack', 0);
		$qty = JRequest::getInt('qty', 0);

		if(!empty($order_id) && !empty($pack_id) && !empty($qty)) {
			$ret = $orderClass->modifyPack($order_id, $pack_id, $qty, false);
			if($ret) {
				$orderClass->refresh($order_id);
				echo '1';
				exit;
			}
		}
		echo '0';
		exit;
	}

	public function modifyserial() {
		$orderClass = hikaserial::get('class.order');
		$order_id = hikaserial::getCID('order_id');
		$serial_id = JRequest::getInt('serial_id', 0);
		$add = JRequest::getInt('add', 0);

		if(!empty($order_id) && !empty($serial_id) && !empty($qty)) {
			$ret = $orderClass->modifySerial($order_id, $serial_id, $add);
			if($ret) {
				$orderClass->refresh($order_id);
				echo '1';
				exit;
			}
		}
		echo '0';
		exit;
	}
}
