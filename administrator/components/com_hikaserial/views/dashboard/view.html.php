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
class dashboardViewDashboard extends hikaserialView {

	const ctrl = 'dashboard';
	const name = HIKASERIAL_NAME;
	const icon = HIKASERIAL_LNAME;

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function listing() {
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$buttons = array(
			array(
				'name' => JText::_('ADD_NEW_SERIAL'),
				'url' => hikaserial::completeLink('serial&task=edit'),
				'icon' => 'icon-48-add-serials'
			),
			array(
				'name' => JText::_('HIKA_SERIALS'),
				'url' => hikaserial::completeLink('serial'),
				'icon' => 'icon-48-serials'
			),
			array(
				'name' => JText::_('HIKA_PACKS'),
				'url' => hikaserial::completeLink('pack'),
				'icon' => 'icon-48-pack'
			),
			array(
				'name' => JText::_('PLUGINS'),
				'url' => hikaserial::completeLink('plugins'),
				'icon' => 'icon-48-plugin'
			),
			array(
				'name' => JText::_('HIKA_CONFIGURATION'),
				'url' => hikaserial::completeLink('config'),
				'icon' => 'icon-48-config'
			),
			array(
				'name' => JText::_('IMPORT'),
				'url' => hikaserial::completeLink('import'),
				'icon' => 'icon-48-import'
			),
			array(
				'name' => JText::_('UPDATE_ABOUT'),
				'url' => hikaserial::completeLink('update'),
				'icon' => 'icon-48-install'
			),
			array(
				'name' => JText::_('HIKA_HELP'),
				'url' => hikaserial::completeLink('documentation'),
				'icon' => 'icon-48-help_header'
			)
		);
		$this->assignRef('buttons', $buttons);

		if(HIKASHOP_J16 && JFactory::getUser()->authorise('core.admin', 'com_hikamarket')) {
			$this->toolbar[] = array('name' => 'preferences', 'component' => 'com_hikaserial');
		}
		$this->toolbar[] = array('name' => 'pophelp', 'target' => 'welcome');

		$toggleClass = hikaserial::get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
	}
}
