<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  Installer Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Installer.ArkEditor
 */

 
class PlgInstallerArkeditor extends JPlugin
{
		
	public $app;	
		
	function onInstallerAfterInstaller ($model, $package, $installer, $result, $msg)
	{
	
		$group =  (string) $installer->manifest->attributes()->group;
		
	
		if($group != 'arkeditor')
			return;
	
	
		if(!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/plugin.php'))
		{
			return JError::raiseError(101,'The JCK component not detected. This means this plugin could not be properly installed and this may affect some operations of the editor');
		}
	
		require_once JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/plugin.php';
			
		$element = $installer->manifest->files;

		if ($element)
		{
			$name = '';

			if (count($element->children()))
			{
				foreach ($element->children() as $file)
				{
					if ((string) $file->attributes()->plugin)
					{
						$name = strtolower((string) $file->attributes()->plugin);
						break;
					}
				}
			}
				
			/*
			 * ---------------------------------------------------------------------------------------------
			 * Database Processing Section
			 * ---------------------------------------------------------------------------------------------
			 */

			$row = JTable::getInstance('extension');
			$row->load(array('folder'=>'arkeditor','element'=>$name));
			$row->enabled = 1;
			if(!$row->store())
				throw new Exception('Failed to publish plugin');
		
			/*
			*---------------------------------------------------------------------------------------------------
			* Integrate with JCK Component
			*---------------------------------------------------------------------------------------------------
			*/

			$jckRow = JTable::getInstance('plugin','ARKTable');
			$jckRow->load(array('name'=>$name));
			$icon 				= $installer->manifest->icon;
			$title				= ucFirst($name);
			$jckRow->title 		= (!empty($icon) ? (string) $title : '');
			$jckRow->name		= $name;
			$jckRow->type 		= 'plugin';
			$jckRow->row	 	= 4;
			$jckRow->published 	= 1;
			$jckRow->editable 	= 1;
			$jckRow->icon 		= (!empty($icon) ? (string) $icon : '');
			$jckRow->iscore 	= 0;
			if(!$jckRow->id)
				$jckRow->params 	= $installer->getParams();
          
            
			if(!$jckRow->store())
                throw new Exception('Failed to insert record into JCK Plugins table');	

		    //Update Extension table with reference to this new record
						
			$row->custom_data = $jckRow->id;
			if(!$row->store())
               throw Exception('Failed to add plugin reference to extension record');
        }	
	}//end function
}
