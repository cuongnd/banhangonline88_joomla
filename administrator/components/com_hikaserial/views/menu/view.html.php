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
class menuViewMenu extends hikaserialView {

	public function display($tpl = null, $title = '', $menu_style = '') {

		$this->assignRef('title',$title);
		$this->assignRef('menu_style', $menu_style);

		$fct = $this->getLayout();
		if(method_exists($this,$fct))
			$this->$fct();

		$menus = array(
			array(
				'name' => JText::_('SYSTEM'),
				'check' => 'ctrl=config',
				'acl' => 'config',
				'task' => 'manage',
				'icon' => 'icon-16-config',
				'url' => hikaserial::completeLink('config'),
				'children' => array(
					array(
						'name' => JText::_('HIKA_CONFIGURATION'),
						'check' => 'ctrl=config',
						'acl' => 'config',
						'task' => 'manage',
						'icon' => 'icon-16-settings',
						'url' => hikaserial::completeLink('config')
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
					)
				)
			),

			array(
				'name' => JText::_('HIKA_SERIALS'),
				'check' => 'ctrl=serial',
				'acl' => 'serial',
				'icon' => 'icon-16-serial',
				'url' => hikaserial::completeLink('serial'),
				'children' => array(
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
					)
				)
			),

			array(
				'name' => JText::_('HIKA_HELP'),
				'check' => 'ctrl=documentation',
				'icon' => 'icon-16-help',
				'url' => hikaserial::completeLink('documentation'),
				'children' => array(
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
			)
		);

		$this->checkActive($menus);
		$this->assignRef('menus',$menus);

		parent::display(null);
	}

	private function checkActive(&$menus, $level = 0) {
		if($level < 2) {
			foreach($menus as $k => $menu) {
				if(strpos($_SERVER['QUERY_STRING'], $menu['check']) !== false) {
					if(strpos($_SERVER['QUERY_STRING'], '&task=') === false || strpos($menu['check'], '&task=') !== false) {
						$menus[$k]['active'] = true;
					}
				}
				if(isset($menu['display']) && !$menu['display']) {
					unset($menus[$k]);
					continue;
				}
				if(!empty($menu['children'])) {
					$this->checkActive($menus[$k]['children'], $level+1);
				}
			}
		}
	}
}
