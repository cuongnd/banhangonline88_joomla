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
class SerialsMarketController extends hikamarketController {
	protected $rights = array(
		'display' => array(
			'packs', 'pack', 'stats', 'export', 'import',
		),
		'add' => array('packadd'),
		'edit' => array(),
		'modify' => array('packsave','packapply'),
		'delete' => array()
	);
	protected $default_task = 'packs';

	public function __construct($config = array(), $skip = false) {
		$config['base_path'] = rtrim(JPATH_SITE,DS).DS.'components'.DS.'com_hikaserial'.DS;
		parent::__construct($config, $skip);

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(HIKASERIAL_CSS.'frontend_default.css');
	}

	public function packs() {
		if(!hikaserial::initMarket() || !hikamarket::loginVendor())
			return false;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;

		if(!hikamarket::acl('plugins/hikaserial'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		JRequest::setVar('layout', 'packs');
		return parent::display();
	}

	public function pack() {
		if(!hikaserial::initMarket() || !hikamarket::loginVendor())
			return false;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;

		if(!hikamarket::acl('plugins/hikaserial/pack/edit'))
			return hikamarket::deny('serials', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		$vendor_id = hikamarket::loadVendor(false);
		$pack_id = hikamarket::getCID('cid');
		if(!empty($pack_id)) {
			$packClass = hikaserial::get('class.pack');
			$pack = $packClass->get($pack_id);
			if($vendor_id > 1 && (int)$pack->pack_vendor_id != $vendor_id)
				return hikamarket::deny('serials', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));
		} elseif(!hikamarket::acl('plugins/hikaserial/pack/add'))
			return hikamarket::deny('serials', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		JRequest::setVar('layout', 'pack');
		return parent::display();
	}

	public function packadd() {
		return $this->pack();
	}

	public function packsave() {
		$this->packstore();
		return $this->packs();
	}

	public function packapply() {
		$status = $this->packstore();
		return $this->pack();
	}

	protected function packstore() {
		JRequest::checkToken() || die('Invalid Token');

		$app = JFactory::getApplication();
		$class = hikaserial::get('class.pack');
		$status = $class->frontSaveForm();
		if($status) {
			$app->enqueueMessage(JText::_('HIKAM_SUCC_SAVED'), 'message');
			JRequest::setVar('cid', $status);
			JRequest::setVar('fail', null);
		} else {
			$app->enqueueMessage(JText::_('ERROR_SAVING'), 'error');
			if(!empty($class->errors)) {
				foreach($class->errors as $err) {
					$app->enqueueMessage($err, 'error');
				}
			}
		}
		return $status;
	}

	public function stats() {
		return $this->checkProductPack('listing', 'stats');
	}

	public function export() {
		return $this->checkProductPack('export', 'export');
	}

	protected function checkProductPack($acl, $view) {
		if(!hikaserial::initMarket() || !hikamarket::loginVendor())
			return false;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;

		if(!hikamarket::acl('plugins/hikaserial/serial/'.$acl))
			return hikamarket::deny('serials', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		$product_id = JRequest::getInt('product_id', 0);
		$pack_id = JRequest::getInt('pack_id', 0);

		if(empty($pack_id) || empty($product_id))
			return hikamarket::deny('serials', JText::_('INVALID_DATA'));

		$packClass = hikaserial::get('class.pack');
		if(!$packClass->isVendorPack($pack_id) || !hikamarket::isVendorProduct($product_id))
			return hikamarket::deny('serials', JText::_('INVALID_DATA'));

		JRequest::setVar('layout', $view);
		return parent::display();
	}

	public function import() {
		if(!hikaserial::initMarket() || !hikamarket::loginVendor())
			return false;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;

		if(!hikamarket::acl('plugins/hikaserial/serial/import'))
			return hikamarket::deny('serials', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		$pack_id = JRequest::getInt('pack_id', 0);
		if(empty($pack_id))
			return hikamarket::deny('serials', JText::_('INVALID_DATA'));

		$vendor_id = hikamarket::loadVendor(false);
		$packClass = hikaserial::get('class.pack');
		$pack = $packClass->get($pack_id);
		if($vendor_id > 1 && (int)$pack->pack_vendor_id != $vendor_id)
			return hikamarket::deny('serials', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		if(JRequest::checkToken()) {
			$app = JFactory::getApplication();

			$formData = JRequest::getVar('data', array(), '', 'array');
			if(!empty($formData['serials']) && is_string($formData['serials']) && strlen(trim($formData['serials'])) > 0) {
				$importHelper = hikaserial::get('helper.import');
				$importHelper->pack_id = (int)$pack_id;

				$options = array(
					'check_duplicates' => true
				);
				$ret = $importHelper->handleTextContent($formData['serials'], $options);

				if(!empty($ret)) {
					$productClass = hikaserial::get('class.product');
					$productClass->refreshQuantity(null, $pack_id);
				}

			} else {
				$app->enqueueMessage(JText::_('INVALID_DATA'), 'error');
			}
		}

		JRequest::setVar('layout', 'import');
		return parent::display();
	}
}
