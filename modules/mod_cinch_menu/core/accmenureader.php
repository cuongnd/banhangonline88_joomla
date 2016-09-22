<?php 
/*
* Pixel Point Creative - Cinch Menu Module
* License: GNU General Public License version
* See: http://www.gnu.org/copyleft/gpl.html
* Copyright (c) Pixel Point Creative LLC.
* More info at http://www.pixelpointcreative.com
* Last Updated: 3/14/13
*/

defined( '_JEXEC' ) or die( 'Restricted access' ); 

abstract class CinchMenuHelper
{
	public static function getMenus($menutype, $startLevel = 1, $endLevel = "all")
	{
		$arrMenus = array();
		
		CinchMenuHelper::getSortedMenus($arrMenus, $menutype, null, $startLevel, $endLevel);
		return $arrMenus;
	}
	public static function getSortedMenus(& $arrMenus, $menutype, $parent_id = null , $startLevel, $endLevel){
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->select('a.id, a.title, a.level, a.parent_id, a.link, a.params, a.type');
			$query->from('#__menu AS a');
			$query->where('a.published = 1');
			$where = "menutype=".$db->quote($menutype);

			if(isset($parent_id) && !isset($startLevel)){
				$where.=" AND a.parent_id = ".$parent_id;
			} 
			if(isset($startLevel) && !isset($parent_id)){
				$where .=" AND a.level = ".$startLevel;
			}
			if($endLevel != "all"){
				$where.= " AND a.level <= ".$endLevel; 
			}
			$query->where($where);
			$query->order('lft');
			$db->setQuery($query);
			$menus = $db->loadObjectList();
			
			if(count($menus)){
				foreach ($menus as $menu) {
					$arrMenus[] = $menu;
					CinchMenuHelper::getSortedMenus($arrMenus, $menutype, $menu->id, null, $endLevel);
				}
			}
			
		
	}
	static function getList($menuType,$startLevel, $endLevel, $showSub)
	{
		$list		= array();
		$db		= JFactory::getDbo();
		$user		= JFactory::getUser();
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
		$path		= $active->tree;
		
		$items 		= $menu->getItems('menutype',$menuType);
		$lastitem	= 0;
       	        //$instack = array();
		//$instack[] = $active->id;
		//$instack[] = $active->parent_id;
		
		if ($items) {
			foreach($items as $i => $item)
			{
				if (($startLevel && $item->level < $startLevel)
					|| ($endLevel && $endLevel != "all" && $item->level > $endLevel  )
					|| ($showSub == "false" && $item->level > $startLevel)
				) {
					unset($items[$i]);
					continue;
				}
				
				
				//print_r($active);
				//die;
				//echo $item->parent_id . '' . $active . '<br/>';. 
				
				if ($item->level > 1 && !in_array($item->parent_id, $path)) {
					unset($items[$i]);
					continue;
				}
				$path[] = $item->id;
				$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);
				$lastitem			= $i;
				$item->active		= false;
				$item->flink = $item->link;
				
				switch ($item->type)
				{
					case 'separator':
						continue;
					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							$item->flink = $item->link.'&Itemid='.$item->id;
						}
						break;
					case 'alias':
						$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
						break;
					default:
						$router = JSite::getRouter();
						if ($router->getMode() == JROUTER_MODE_SEF) {
							$item->flink = 'index.php?Itemid='.$item->id;							
						}
						else {
							$item->flink .= '&Itemid='.$item->id;
						}
						break;
				}
				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));					
				}
				else {
					$item->flink = JRoute::_($item->flink);
				}
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', '')) : '';
			
			}
		}
		
		return array_values($items);
	}	
}
