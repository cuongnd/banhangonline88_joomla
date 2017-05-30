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
class updateController extends hikaauctionBridgeController {

	public function __construct($config = array()){
		parent::__construct($config);
		$this->registerDefaultTask('update');
	}

	public function install() {
		hikaauction::setTitle(HIKAAUCTION_NAME, 'install', 'update');

		$newConfig = new stdClass();
		$newConfig->installcomplete = 1;
		$config = hikaauction::config();
		$config->save($newConfig);

		$updateHelper = hikaauction::get('helper.update');
		$updateHelper->addDefaultData();
		$updateHelper->createUploadFolders();
		$updateHelper->installMenu();
		$updateHelper->installExtensions();
		$updateHelper->addUpdateSite();

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', HIKAAUCTION_LNAME, JText::_('HKA_CPANEL'), hikaauction::completeLink('dashboard'));

		$this->showIframe(HIKAAUCTION_UPDATEURL.'install');
		return false;
	}

	public function update() {
		$config = hikaauction::config();
		if($config->get('website') != HIKAAUCTION_LIVE){
			$updateHelper = hikaauction::get('helper.update');
			$updateHelper->addUpdateSite();
		}
		hikaauction::setTitle(JText::_('UPDATE_ABOUT'), 'install', 'update');
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', HIKAAUCTION_LNAME, JText::_('HKA_CPANEL'), hikaauction::completeLink('dashboard'));
		$this->showIframe(HIKAAUCTION_UPDATEURL.'update');
		return false;
	}

	private function showIframe($url) {
		$config = hikaauction::config();
		echo '<div id="hikaauction_div"><iframe allowtransparency="true" scrolling="auto" height="450px" frameborder="0" width="100%" name="hikaauction_frame" id="hikaauction_frame" '.
			'src="'.$url.'&level='.$config->get('level').'&component='.HIKAAUCTION_LNAME.'&version='.$config->get('version').'&li='.urlencode(base64_encode(HIKAAUCTION_LIVE)).'"></iframe></div>';
	}

}
