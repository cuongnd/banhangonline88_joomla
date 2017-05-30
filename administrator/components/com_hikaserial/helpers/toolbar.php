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
include_once(HIKASHOP_HELPER . 'toolbar.php');

class hikaserialToolbarHelper extends hikashopToolbarHelper {

	public function customTool(&$bar, $toolname, $tool) {
		switch(strtolower($toolname)) {
			case 'shopdashboard':
				$bar->appendButton('Link', 'hikashop', JText::_('HIKASHOP_CPANEL'), hikaserial::completeLink('shop.dashboard'));
				return true;
			case 'dashboard':
				$bar->appendButton('Link', HIKASERIAL_LNAME, JText::_('HIKASERIAL_CPANEL'), hikaserial::completeLink('dashboard') );
				return true;
		}
		return false;
	}
}
