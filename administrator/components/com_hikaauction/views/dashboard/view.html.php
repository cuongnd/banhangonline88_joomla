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
class dashboardViewDashboard extends hikaauctionView {

	const ctrl = 'dashboard';
	const name = HIKAAUCTION_NAME;
	const icon = HIKAAUCTION_LNAME;

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAAUCTION_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function listing() {
		hikaauction::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$buttons = array(
			array(
				'name' => JText::_('CONFIG'),
				'url' => hikaauction::completeLink('config'),
				'icon' => 'icon-48-config'
			),
			array(
				'name' => JText::_('HIKA_AUCTIONS'),
				'url' => hikaauction::completeLink('auctions'),
				'icon' => 'icon-48-product'
			),
			array(
				'name' => JText::_('HKA_HELP'),
				'url' => hikaauction::completeLink('documentation'),
				'icon' => 'icon-48-help'
			)
		);
		$this->assignRef('buttons', $buttons);
	}
}
