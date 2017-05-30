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
class hikaauctionMenuClass extends hikaauctionClass {

	protected $tables = array();
	var $pkeys=array('id');
	var $toggle = array('published'=>'id');
	function getTable(){
		return hikashop_table('menu',false);
	}
	public function processView(&$view) {
		if(empty($view->menus))
			return;

		$currentuser = JFactory::getUser();
		if(!$currentuser->authorise('core.manage', 'com_hikaauction'))
			return;

		$auction = array(
			'name' => HIKAAUCTION_NAME,
			'check' => 'ctrl=config',
			'acl' => 'config',
			'task' => 'manage',
			'icon' => 'icon-16-hikaauction',
			'url' => hikaauction::completeLink('dashboard'),
			'children' => array(
				array(
					'name' => JText::_('HIKA_CONFIGURATION'),
					'check' => 'ctrl=config',
					'acl' => 'config',
					'task' => 'manage',
					'icon' => 'icon-16-settings',
					'url' => hikaauction::completeLink('config'),
					'display' => $currentuser->authorise('core.admin', 'com_hikaauction')
				),
				array(
					'name' => JText::_('HIKA_AUCTIONS'),
					'check' => 'ctrl=auctions',
					'icon' => 'icon-16-product',
					'url' => hikaauction::completeLink('auctions')
				),
				array(
					'name' => JText::_('DOCUMENTATION'),
					'check' => 'ctrl=documentation',
					'icon' => 'icon-16-help',
					'url' => hikaauction::completeLink('documentation')
				),
				array(
					'name' => JText::_('UPDATE_ABOUT'),
					'check' => 'ctrl=update',
					'icon' => 'icon-16-update',
					'url' => hikaauction::completeLink('update')
				)
			)
		);

		$newMenus = array(&$auction);
		$this->checkActive($newMenus, 0, HIKAAUCTION_COMPONENT);

		$last = array_pop($view->menus);
		array_push($view->menus, $auction, $last);
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

	function save(&$element){
		if(version_compare(JVERSION,'1.6','<')){
			$query="SELECT a.id FROM ".hikashop_table('components',false).' AS a WHERE a.option=\''.HIKAAUCTION_COMPONENT.'\'';
			$this->db->setQuery($query);
			$element->componentid = $this->db->loadResult();
		}else{
			$query="SELECT a.extension_id FROM ".hikashop_table('extensions',false).' AS a WHERE a.type=\'component\' AND a.element=\''.HIKAAUCTION_COMPONENT.'\'';
			$this->db->setQuery($query);
			$element->component_id = $this->db->loadResult();
		}
		if(empty($element->id)){
			$element->params['show_page_title']=1;
		}
		if(!empty($element->params)&&is_array($element->params)){
			$params = '';
			foreach($element->params as $k => $v){
				$params.=$k.'='.$v."\n";
			}
			$element->params = rtrim($params,"\n");
		}
		$element->id = parent::save($element);

		if($element->id && HIKASHOP_J30){

			$plugin = JPluginHelper::getPlugin('system', 'cache');
			$params = new JRegistry(@$plugin->params);

			$options = array(
				'defaultgroup'	=> 'page',
				'browsercache'	=> $params->get('browsercache', false),
				'caching'		=> false,
			);

			$cache		= JCache::getInstance('page', $options);
			$cache->clean();
		}
		return $element->id;
	}
}
