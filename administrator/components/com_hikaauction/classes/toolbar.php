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
class hikaauctionToolbarClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	public function processView(&$view) {
		if(empty($view->toolbar))
			return;

		if(!empty($view->ctrl))
			$ctrl = $view->ctrl;
		else
			$ctrl = JRequest::getCmd('ctrl', '');
		$task = $view->getLayout();
	}
}
