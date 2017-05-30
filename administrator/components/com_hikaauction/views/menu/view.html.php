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
class MenubookingViewMenubooking extends hikaauctionView {
	var $triggerView = true;

	function display($tpl = null, $title = '',$menu_style = '') {
		$this->assignRef('title', $title);
		$this->assignRef('menu_style', $menu_style);

		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();

		$menus = array(
			array(
				'name' => JText::_('CONFIG'),
				'check' => 'ctrl=config',
				'icon' => 'icon-16-config',
				'url' => hikaauction::completeLink('config'),
				'children' => array(
					array(
						'name' => JText::_('CONFIG'),
						'check' => 'ctrl=config',
						'icon' => 'icon-16-config',
						'url' => hikaauction::completeLink('config')
					),
					array(
						'name' => JText::_('HKP_EMAIL'),
						'check' => 'ctrl=email',
						'icon' => 'icon-16-mail',
						'url' => hikaauction::completeLink('email')
					)
				)
			),
			array(
				'name' => JText::_('DOCUMENTATION'),
				'check' => 'ctrl=documentation',
				'acl' => 'help',
				'icon' => 'icon-16-help',
				'url' => hikaauction::completeLink('documentation'),
				'children' => array(
					array(
						'name' => JText::_('DOCUMENTATION'),
						'check' => 'ctrl=documentation',
						'icon' => 'icon-16-help',
						'url' => hikaauction::completeLink('documentation')
					),
					array(
						'name' => JText::_('UPDATE_ABOUT'),
						'check' => 'ctrl=update',
						'icon' => 'icon-16-install',
						'url' => hikaauction::completeLink('update')
					),
					array(
						'name' => 'Development Upgrade',
						'check' => 'ctrl=update&task=upgrade',
						'icon' => 'icon-16-install',
						'url' => hikaauction::completeLink('update&task=upgrade')
					),
					array(
						'name' => JText::_('FORUM'),
						'options' => 'target="_blank"',
						'icon' => 'icon-16-info',
						'url' => HIKAAUCTION_URL.'support/forum.html'
					)
				)
			)
		);

		$this->checkActive($menus);
		$this->assignRef('menus',$menus);

		parent::display(null);
	}

	private function checkActive(&$menus, $level = 0){
		if($level >= 2)
			return;

		if(empty($this->request)) {
			$this->request = array();
			$this->request['option'] = JRequest::getCmd('option', HIKAAUCTION_COMPONENT);
			$this->request['ctrl'] = JRequest::getCmd('ctrl', null);
			$this->request['task'] = JRequest::getCmd('task', null);
		}

		foreach($menus as $k => $menu) {
			if(!empty($menu['check'])) {
				if(is_array($menu['check'])) {
					$active = true;
					if(!isset($menu['check']['option'])) {
						$menu['check']['option'] = HIKAAUCTION_COMPONENT;
					}
					foreach($menu['check'] as $key => $value) {
						$invert = false;
						if(substr($key, 0, 1) == '!') {
							$key = substr($key,1);
							$invert = true;
						}

						if(!isset($this->request[$key])) {
							$this->request[$key] = JRequest::getCmd($key, null);
						}

						if($value === 0 && empty($this->request[$key])) {
							continue;
						}
						if($invert) {
							if(is_array($value)) {
								$active = !in_array($this->request[$key], $value);
							} else {
								$active = ($this->request[$key] != $value);
							}
						} else {
							$active = ($this->request[$key] == $value);
						}
						if(!$active)
							break;
					}
					if($active) {
						$menus[$k]['active'] = true;
					}
				} else {
					if(strpos($menu['check'], 'option=') === false) {
						if($this->request['option'] == HIKAAUCTION_COMPONENT && strpos($_SERVER['QUERY_STRING'], $menu['check']) !== false) {
							$menus[$k]['active'] = true;
						}
					} elseif(strpos($_SERVER['QUERY_STRING'], $menu['check']) !== false) {
						$menus[$k]['active'] = true;
					}
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
