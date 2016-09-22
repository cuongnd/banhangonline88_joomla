<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
 
require_once(JPATH_ADMINISTRATOR.'/components/com_arkeditor/helper.php');
 
class PlgArKEventsACL extends JPlugin
{

	public $db;


	public function onInstanceCreated(&$params)
	{
		
		$user = JFactory::getUser();
		
		if($user->authorise('core.admin'))
		return;
		
		$query = $this->db->getQuery(true);
		$query->select('parentid,id,name,acl')
			->from('#__ark_editor_plugins')
			->where('published = 1');
	
		$this->db->setQuery( $query );
		$plugins = $this->db->loadObjectList();
		
		if (!is_array($plugins)) {
			ARKHelper::error( $this->db->getErrorMsg() );
		}
		
		if(empty($plugins))
			return;
		
		$groups	= $user->getAuthorisedGroups();
		
		$groups = array_values($groups); //reindex array
		
		
		$js = '';
		
		$deniedPlugins = array();
		$removePlugins = array();
				
		foreach($plugins as $plugin)
		{
			
			if(is_null($plugin->acl))
				continue;
			
			
	
			$acl = json_decode($plugin->acl);
			
			$allow = true;
						
	
			if(empty($acl))
			{
				$allow = false;
				$deniedPlugins[] = $plugin->id;
				$removePlugins[] = $plugin->name;
			}	
			else
			{
				
				if( $groups )
				{
					$allow = false;
					for( $n=0, $i=count($groups); $n<$i; $n++ )
					{
						if( in_array( $groups[$n], $acl) )
						{
							$allow = true;
							break;
						}//end if
								
					}//end for loop
					if(!$allow)
					{
						$deniedPlugins[] = $plugin->id;
						$removePlugins[] = $plugin->name;
					}
				}//end if
				
				// check to see if parent plugin access view level is denied. If is then parent settings override
				if($allow && in_array( $plugin->parentid, $deniedPlugins))
				{
					$deniedPlugins[] = $plugin->id;
					$removePlugins[] = $plugin->name;
				}
			}

		}
	
		if(empty($removePlugins))
			return;
		
		$plugs = implode(',',$removePlugins);
		
		if($plugs)
			return	"
				
				editor.on( 'configLoaded', function()
				{
					if(editor.config.removePlugins || 1 == ".(count($removePlugins) > 1 ? 1 : 0 ).") 
						editor.config.removePlugins += ',".$plugs."';
					else 	
						editor.config.removePlugins += '".$plugs."';
				});";	
		
		return  null;	
	}
}
