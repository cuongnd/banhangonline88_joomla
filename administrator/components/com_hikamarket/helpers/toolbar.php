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
$app = JFactory::getApplication();

if($app->isAdmin()) {
	include_once(HIKASHOP_HELPER.'toolbar.php');

	class hikamarketToolbarHelper extends hikashopToolbarHelper {

		public function customTool(&$bar, $toolname, $tool) {
			switch($toolname) {
				case 'shopdashboard':
					$bar->appendButton('Link', 'hikashop', JText::_('HIKASHOP_CPANEL'), hikamarket::completeLink('shop.dashboard'));
					return true;
				case 'dashboard':
					$bar->appendButton('Link', HIKAMARKET_LNAME, JText::_('HIKAMARKET_CPANEL'), hikamarket::completeLink('dashboard'));
					return true;
			}
			return false;
		}
	}
} else {
	class hikamarketToolbarHelper {
		public $aliases;

		public function __construct() {
			$this->aliases = array();
		}

		public function process($toolbar) {
			$ret = '';
			if(empty($toolbar))
				return $ret;

			$js = null;
			$params = new HikaParameter();
			$params->set('toolbar', $toolbar);
			echo hikamarket::getLayout('toolbar', 'default', $params, $js);
		}
	}
}
