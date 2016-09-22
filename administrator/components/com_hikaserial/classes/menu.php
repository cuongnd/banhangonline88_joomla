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
class hikaserialMenuClass extends hikaserialClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	public function processView(&$view) {
		if(empty($view->menus))
			return;

		$currentuser = JFactory::getUser();
		if(HIKASHOP_J16 && !$currentuser->authorise('core.manage', 'com_hikaserial'))
			return;

		$market = array(
			'name' => HIKASERIAL_NAME,
			'check' => 'ctrl=config',
			'acl' => 'config',
			'task' => 'manage',
			'icon' => 'icon-16-serial',
			'url' => hikaserial::completeLink('dashboard'),
			'children' => array(
				array(
					'name' => JText::_('HIKA_CONFIGURATION'),
					'check' => 'ctrl=config',
					'acl' => 'config',
					'task' => 'manage',
					'icon' => 'icon-16-settings',
					'url' => hikaserial::completeLink('config'),
					'display' => !HIKASHOP_J16 || $currentuser->authorise('core.admin', 'com_hikaserial')
				),
				array(
					'name' => JText::_('PLUGINS'),
					'check' => 'ctrl=plugins',
					'icon' => 'icon-16-plugin',
					'url' => hikaserial::completeLink('plugins')
				),
				array(
					'name' => JText::_('VIEWS'),
					'check' => 'ctrl=views',
					'icon' => 'icon-16-views',
					'url' => hikaserial::completeLink('shop.view&component='.HIKASERIAL_COMPONENT)
				),
				array('name' => ''),
				array(
					'name' => JText::_('HIKA_PACKS'),
					'check' =>'ctrl=pack',
					'icon' => 'icon-16-pack',
					'url' => hikaserial::completeLink('pack')
				),
				array(
					'name' => JText::_('HIKA_SERIALS'),
					'check' =>'ctrl=serial',
					'icon' => 'icon-16-serial',
					'url' => hikaserial::completeLink('serial')
				),
				array(
					'name' => JText::_('IMPORT'),
					'check' => 'ctrl=import',
					'icon' => 'icon-16-import',
					'url' => hikaserial::completeLink('import')
				),
				array('name' => ''),
				array(
					'name' => JText::_('DOCUMENTATION'),
					'check' => 'ctrl=documentation',
					'icon' => 'icon-16-help',
					'url' => hikaserial::completeLink('documentation')
				),
				array(
					'name' => JText::_('UPDATE_ABOUT'),
					'check' => 'ctrl=update',
					'icon' => 'icon-16-update',
					'url' => hikaserial::completeLink('update')
				),
				array(
					'name' => JText::_('FORUM'),
					'check' => 'support/forum.html',
					'icon' => 'icon-16-info',
					'url' => HIKASERIAL_URL.'support/forum.html'
				)
			)
		);

		$newMenus = array(&$market);
		$this->checkActive($newMenus, 0, HIKASERIAL_COMPONENT);

		$last = array_pop($view->menus);
		array_push($view->menus, $market, $last);
	}

	private function checkActive(&$menus, $level = 0, $default_component = HIKASHOP_COMPONENT) {
		if($level < 2) {
			$currentComponent = JRequest::getCmd('option', HIKASHOP_COMPONENT);
			foreach($menus as $k => $menu) {
				if(isset($menu['display']) && !$menu['display']) {
					unset($menus[$k]);
					continue;
				}
				if(empty($menu['check']))
					continue;
				if(is_array($menu['check'])) {
					$component = $menu['check'][0];
					$check = $menu['check'][1];
				} else {
					$check = $menu['check'];
					$component = $default_component;
				}
				if($component == $currentComponent && strpos($_SERVER['QUERY_STRING'], $check) !== false) {
					$menus[$k]['active'] = true;
				}
				if(!empty($menu['children'])) {
					$this->checkActive($menus[$k]['children'], $level+1, $default_component);
				}
			}
		}
	}
}
