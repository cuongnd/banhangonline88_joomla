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
class updateController extends hikaserialBridgeController {

	public function __construct($config = array()){
		parent::__construct($config);
		$this->registerDefaultTask('update');
	}

	public function install() {
		hikaserial::setTitle(HIKASERIAL_NAME, 'install', 'update');

		$newConfig = new stdClass();
		$newConfig->installcomplete = 1;
		$config = hikaserial::config();
		$config->save($newConfig);

		$updateHelper = hikaserial::get('helper.update');
		$updateHelper->addJoomfishElements();
		$updateHelper->addDefaultData();
		$updateHelper->installMenu();
		$updateHelper->installExtensions();
		$updateHelper->addUpdateSite();

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', HIKASERIAL_LNAME, JText::_('HIKASHOP_CPANEL'), hikaserial::completeLink('dashboard'));

		$this->showIframe(HIKASERIAL_UPDATEURL.'install');
		return false;
	}

	public function update() {
		$config = hikaserial::config();
		if($config->get('website') != HIKASHOP_LIVE){
			$updateHelper = hikaserial::get('helper.update');
			$updateHelper->addUpdateSite();
		}
		hikaserial::setTitle(JText::_('UPDATE_ABOUT'), 'install', 'update');
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', HIKASERIAL_LNAME, JText::_('HIKASHOP_CPANEL'), hikaserial::completeLink('dashboard'));
		$this->showIframe(HIKASERIAL_UPDATEURL.'update');
		return false;
	}

	private function showIframe($url) {
		$config = hikaserial::config();
		$shopConfig = hikaserial::config(false);
		$menu_style = $shopConfig->get('menu_style','title_bottom');
		if(HIKASHOP_J30) $menu_style = 'content_top';
		if($menu_style == 'content_top') {
			echo hikaserial::getMenu();
		}
		echo '<div id="hikaserial_div"><iframe allowtransparency="true" scrolling="auto" height="450px" frameborder="0" width="100%" name="hikaserial_frame" id="hikaserial_frame" '.
			'src="'.$url.'&level='.$config->get('level').'&component='.HIKASERIAL_LNAME.'&version='.$config->get('version').'"></iframe></div>';
	}

}
