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
class dashboardmarketViewdashboardmarket extends hikamarketView {

	const ctrl = 'dashboard';
	const name = HIKAMARKET_NAME;
	const icon = HIKAMARKET_LNAME;

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function listing() {
		hikamarket::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$buttons = array(
			array(
				'name' => JText::_('HIKA_VENDORS'),
				'url' => hikamarket::completeLink('vendor'),
				'icon' => 'icon-48-vendors'
			),
			array(
				'name' => JText::_('PLUGINS'),
				'url' => hikamarket::completeLink('plugins'),
				'icon' => 'icon-48-plugin'
			),
			array(
				'name' => JText::_('HIKA_CONFIGURATION'),
				'url' => hikamarket::completeLink('config'),
				'icon' => 'icon-48-config'
			),
			array(
				'name' => JText::_('HIKAM_ACL'),
				'url' => hikamarket::completeLink('config&task=acl'),
				'icon' => 'icon-48-acl'
			),
			array(
				'name' => JText::_('UPDATE_ABOUT'),
				'url' => hikamarket::completeLink('update'),
				'icon' => 'icon-48-install'
			),
			array(
				'name' => JText::_('HIKA_HELP'),
				'url' => hikamarket::completeLink('documentation'),
				'icon' => 'icon-48-help_header'
			)
		);
		$this->assignRef('buttons', $buttons);

		if(HIKASHOP_J16 && JFactory::getUser()->authorise('core.admin', 'com_hikamarket')) {
			$this->toolbar[] = array('name' => 'preferences', 'component' => 'com_hikamarket');
		}
		$this->toolbar[] = array('name' => 'pophelp', 'target' => 'welcome');
	}
}
