<?php

/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

abstract class ArkTreelinkHelper
{
	
	public static function ListExtensions(& $extensions)
	{
	
		if(!is_dir(JPATH_ADMINISTRATOR.'/components/com_arkeditor'))
			return;
		
		$db = JFactory::getDBO();
		
		$root =  JURI::root() .'/plugins/editors/arkeditor/ckeditor/plugins';	
		$base =  JPATH_PLUGINS.'/editors/arkeditor/ckeditor/plugins';
		
		
		
		$query = $db->getQuery(true);
		
		$query->select('ext.name')
			->from('#__ark_editor_plugins ext')
			->innerjoin('#__ark_editor_plugins parent on parent.id = ext.parentid')
			->where('parent.name = "treelink"')
			->where('parent.published = 1')
			->where('ext.published = 1');
		
		$db->setQuery($query);
		
		$results = $db->loadColumn();
		
		if(empty($results))
			return;
	
		
		
		foreach($results as $extension)
		{
			$path = $base.'/'.$extension.'/images/icon.gif';
			$url = $root.$extension.'/images/icon.gif';	
			
			$icon = array('_open','_closed'); //We default to default icon if no custom icon has been supplied by plugin.
			
			if(JFile::exists($path))
			{
				$icon = array($url,$url);
			}
			else
			{	
				$path = $base.'/'.$extension.'/images/icon.png';
				$url = $root.$extension.'/images/icon.png';	
				
				if(JFile::exists($path))
					$icon = array($url,$url);
			}
			
			$extensions[$extension] =  $icon;

		}	

	}

}

?>