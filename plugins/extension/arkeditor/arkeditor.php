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
 *Ark inline content  Extension Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Extension.ArkEditor
 */
 

 
class PlgExtensionArkEditor extends JPlugin
{
	public function onExtensionAfterUninstall($installer, $eid, $result)
	{
		
		if(!$result)
			return;
		
		
		if( empty($installer) || empty($installer->manifest))
			return;
		
		if(is_null($installer->manifest->attributes())) 
			return;
		
		$type =  (string) $installer->manifest->attributes()->type;

		if($type != 'plugin')
			return;

		$group =  (string) $installer->manifest->attributes()->group;
        		
		if($group != 'arkeditor')
			return;
		
		if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/plugin.php'))
		{
		
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
				//Delete record from JCKplugins table	
					
				$row = JTable::getInstance('plugin','ARKTable');
				$row->load(array('name'=>$name));
					
				if($row->id && $row->delete() === false)
					throw Exception('Failed to delete record from the JCKPlugin table');	
			
			}
		}
	}

}