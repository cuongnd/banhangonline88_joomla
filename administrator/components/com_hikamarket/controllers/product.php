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
class productMarketController extends hikamarketController {
	protected $type = 'product';

	protected $rights = array(
		'display' => array('selection','useselection','gettree','waitingapproval'),
		'add' => array('new_template'),
		'edit' => array('approve'),
		'modify' => array(),
		'delete' => array()
	);

	public function new_template(){
		$productClass = hikamarket::get('shop.class.product');
		$product = new stdClass();
		$product->product_type = 'template';
		$product->product_code = '@template-' . uniqid();
		$status = $productClass->save($product);
		if($status) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('PRODUCT_TEMPLATE_CREATED'));
			$app->redirect( hikamarket::completeLink('shop.product&task=edit&cid=' . $status, false, true) );
		}
		return false;
	}

	public function selection(){
		JRequest::setVar('layout', 'selection');
		return parent::display();
	}

	public function useselection(){
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function waitingapproval() {
		$config = hikamarket::config();
		if(!$config->get('product_approval', 0))
			return false;
		JRequest::setVar('layout', 'waitingapproval');
		return parent::display();
	}

	public function approve() {
		JRequest::checkToken('request') || die('invalid token');
		$product_id = hikamarket::getCID('product_id');
		if(empty($product_id))
			return false;

		$productClass = hikamarket::get('class.product');
		$status = $productClass->approve($product_id);

		$cancel_redirect = JRequest::getString('cancel_redirect', '');
		if(!empty($cancel_redirect))
			$cancel_redirect = '&cancel_redirect=' .urlencode($cancel_redirect);

		$app = JFactory::getApplication();
		if($status) {
			$app->enqueueMessage(JText::_('HIKAMARKET_PRODUCT_APPROVED'));
		}
		$app->redirect( hikamarket::completeLink('shop.product&task=edit&cid='.$product_id.$cancel_redirect, false, true) );
	}

	public function getTree() {
		$category_id = JRequest::getInt('category_id', 0);
		$displayFormat = JRequest::getVar('displayFormat', '');
		$variants = JRequest::getInt('variants', 0);
		$search = JRequest::getVar('search', null);

		$namebox_mode = JRequest::getCmd('namebox_mode', 'product');
		if(!in_array($namebox_mode, array('product', 'product_template')))
			$namebox_mode = 'product';

		$nameboxType = hikamarket::get('type.namebox');
		$options = array(
			'start' => $category_id,
			'displayFormat' => $displayFormat,
			'variants' => $variants
		);
		$ret = $nameboxType->getValues($search, $namebox_mode, $options);
		if(!empty($ret)) {
			echo json_encode($ret);
			exit;
		}
		echo '[]';
		exit;
	}
}
