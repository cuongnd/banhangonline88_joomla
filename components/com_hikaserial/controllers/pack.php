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
class packController extends hikaserialController {
	protected $rights = array(
		'display' => array('select','useselection'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function select() {
		if(!hikaserial::initMarket())
			return false;

		if(!hikamarket::loginVendor())
			return false;
		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition', 0))
			return false;
		if(!hikamarket::acl('product/edit/plugin/hikaserial'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		JRequest::setVar('layout', 'select');
		return parent::display();
	}

	public function useselection() {
		if(!hikaserial::initMarket())
			return false;

		if(!hikamarket::loginVendor())
			return false;
		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;
		if(!hikamarket::acl('product/edit/plugin/hikaserial'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}
}
