<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class fsj_ToolbarsHelper
{
	public static function addSubmenu($vName, $class='JSubMenuHelper')
	{
		//echo "ADD SUB MENU!";
		$com = "";
		
		// need to load xml file and parse for admin/section parts
		$mainframe = JFactory::getApplication();
		$default = str_replace("com_fsj_", "", JRequest::getVar('option'));
		if ($default == "main")
		{
			$admin_com = $mainframe->getUserStateFromRequest( "com_fsj_main.admin_com", "admin_com", $default);
		} else {
			$admin_com = $default;		
		}
		
		$language = JFactory::getLanguage();
		$language->load('com_fsj_' . $admin_com);
		$xmlfile = JPATH_ADMINISTRATOR.DS."components/com_fsj_$admin_com/$admin_com.xml";
		
		$xml = @simplexml_load_file($xmlfile);
		if (!$xml)
		{
			echo "Error loading component xml file ($xmlfile)\n";
		}
		
		if ($admin_com == "fssadd")
		{
			$class::addEntry(
				"<span class='fsj_admin_menu_overview'>".JText::_("Support Portal Overview")."</span>",
				'index.php?option=com_fss' ,
				false
				);
			self::displayThisCom($admin_com, $xml, $class, $com, $vName);

		} elseif ($admin_com != "main")
		{
			$class::addEntry(
				"<span class='fsj_admin_menu_overview'>".JText::_("FSJ_M_OVERVIEW")."</span>",
				'index.php?option=com_fsj_main',
				$vName == "fsj_main"
				);

			$class::addEntry(
				"<span class='fsj_admin_menu_overview'>".JText::_("FSJ_M_C_OVERVIEW")."</span>",
				'index.php?option=com_fsj_' . $admin_com ,
				$vName == "fsj_" . $admin_com
				);
			self::displayThisCom($admin_com, $xml, $class, $com, $vName);
			self::displayOptions($admin_com, $xml, $class, $com, $vName);
		} else {
			$class::addEntry(
				"<span class='fsj_admin_menu_overview'>".JText::_("FSJ_M_OVERVIEW")."</span>",
				'index.php?option=com_fsj_main',
				$vName == "fsj_main"
				);

			self::displayComponents($admin_com, $xml, $class, $com, $vName);
			self::displayOptions($admin_com, $xml, $class, $com, $vName);
			self::displayThisCom($admin_com, $xml, $class, $com, $vName);
		}
	}
	
	static function displayOptions($admin_com, $xml, $class, $com, $vName)
	{
		$class::addEntry(
			"<span class='fsj_admin_menu_set'>" . JText::_("FSJ_ADMIN_SETTINGS_HEADER") . "</span>",
			'',
			false
			);	
		
		if ($com != "main")
		{
			$class::addEntry(
				JText::_("FSJ_ADMIN_SETTINGS"),
				'index.php?option=com_fsj_main&view=settings&admin_com=' . $admin_com ,
				JRequest::getVar('view') == "settings" && JRequest::getVar('settings') != "global"
				);	
		}	
		
		$class::addEntry(
			JText::_('FSJ_ADMIN_GLOBAL_SETTINGS'),
			'index.php?option=com_fsj_main&view=settings&settings=global&admin_com=' . $admin_com ,
			JRequest::getVar('view') == "settings" && JRequest::getVar('settings') == "global"
		);
	}

	static function displayComponents($admin_com, $xml, $class, $com, $vName)
	{
			// need to add all components and includes to list in menu
		$components = fsj_ToolbarsHelper::getComponents();
		
		if ($admin_com == "main")
		{
			$class::addEntry(
				"<span class='fsj_admin_menu_set'>Components</span>",
				'',
				false
				);	
		}
		
		$has_includes = false;
		
		foreach ($components as $component)
		{
			$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.$component->element.DS.str_replace("com_fsj_", "", $component->element) . ".xml";
			
			if ($component->element == "com_fsj_includes") $has_includes = true;
			if (file_exists($xmlfile))
			{
				$xmlobj = simplexml_load_file($xmlfile);
				if (isset($xmlobj->overview))
				{
					if ($xmlobj->overview->attributes()->not_in_main) continue;	
				}
			}
			
			if ($admin_com == "main")
			{
				$class::addEntry(
					JText::_($component->displayname),
					'index.php?option='.$component->element ,
					false
					);
			}
			//echo "Component : {$component->name}<br>";	
		}	
		
		if ($has_includes)
		{	
			$class::addEntry(
				"<span class='fsj_admin_menu_set'>Includes Plugins</span>",
				'',
				false
				);	

			foreach ($components as $component)
			{
				$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.$component->element.DS.str_replace("com_fsj_", "", $component->element) . ".xml";
				if (file_exists($xmlfile))
				{
					$xmlobj = simplexml_load_file($xmlfile);
					if (!isset($xmlobj->overview)) continue;
					if (!$xmlobj->overview->attributes()->not_in_main) continue;	
				} else {
					continue;
				}
				
				$class::addEntry(
					JText::_($component->name . "_menu"),
					'index.php?option='.$component->element ,
					false
					);
			}
		}
	}

	static function displayThisCom($admin_com, $xml, $class, $com, $vName)
	{
		if ($xml->admin && $xml->admin->section)
		{
			$section_added = false;
			
			$curset = "";
			foreach ($xml->admin->section as $section)
			{
				if ((string)$section->attributes()->nonmenu) continue;
				if (isset($section->auth))
				{
					$com = $section->auth->attributes()->com;
					$perm = $section->auth->attributes()->perm;
					if (!JFactory::getUser()->authorise($perm, $com)) {
						continue;
					}		
				}
				
				if (!$section_added)
				{
					$class::addEntry(
						"<span class='fsj_admin_menu_set'>".JText::_("COM_FSJ_" . strtoupper($admin_com) . "_MENU")."</span>",
						'',
						false
						);	
					$section_added = true;
				}
				
				$sectionname = (string)$section->attributes()->name;
				/*if ($class == 'JSubMenuHelper')
				{
					if ($sectionname && !$section->attributes()->nonmenusection)
					{
						$menu = JToolBar::getInstance('submenu');
						$menu->appendButton("<span class='fsj_admin_menu_set'>".JText::_($sectionname).":</span>", '', false);
					}
				} else {*/
				if ($sectionname && !$section->attributes()->nonmenusection)
				{
					$class::addEntry(
						"<span class='fsj_admin_menu_header'>".JText::_($sectionname)."</span>",
						'',
						false
						);	
				}
				//}
				
				foreach ($section->item as $item)
				{
					$id = (string)$item->attributes()->id;
					
					if ($id == "spacer")
					{
						$class::addEntry(
							"<hr />",
							'' ,
							false
							);
						continue;
					}
					
					if (isset($item->auth))
					{
						$com = $item->auth->attributes()->com;
						$perm = $item->auth->attributes()->perm;
						if (!JFactory::getUser()->authorise($perm, $com)) {
							continue;
						}		
					}
					$com = $admin_com;
					if ($item->attributes()->component)
					$com = (string)$item->attributes()->component . "&admin_com=" . $com;
					$set = (string)$item->attributes()->set;
					if ($curset != $set)
					{
						$curset = $set;
					} 
					$view = (string)$item->attributes()->id . "s";

					$link = 'index.php?option=com_fsj_'.$com.'&view='.$view;
					
					if ($item->attributes()->url)
					$link = (string)$item->attributes()->url;
					
					$class::addEntry(
						JText::_((string)$item->title),
						$link ,
						$vName == $view
						);
				}	
			}
		}
	}
	
	static function getComponents()
	{
		// get a list of installed freestyle components
		$db	= JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE (element LIKE '%com_fsj_%' OR element LIKE '%com_fss%' OR element LIKE '%com_fst%' OR element LIKE '%com_fsf%') AND type = 'component' AND element != 'com_fsj_main' ORDER BY name";
		$db->setQuery($qry);
		$items = $db->loadObjectList();
		
		foreach ($items as $item)
		{
			FSJ_Lang_Helper::Load_Component($item->element);

			$item->displayname = $item->name . "_menu";
			if (strtolower($item->name) == "com_fss") $item->displayname = "Support Portal";
			if (strtolower($item->name) == "com_fsf") $item->displayname = "FAQs 1.x Lite";
			if (strtolower($item->name) == "com_fst") $item->displayname = "Testimonials 1.x Lite";
		}
		
		usort($items, array("fsj_ToolbarsHelper", "compSort"));

		return $items;
	}

	static function compSort($a, $b)
	{
		return strcmp(JText::_($a->displayname), JText::_($b->displayname));
	}
}
