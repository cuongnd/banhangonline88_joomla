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
 * @subpackage  ArkEditor.ImageManager
 */
class PlgArkEditorVersions extends JPlugin
{
		
    public function onInstanceLoaded(&$params){}
	
	public function onBeforeInstanceLoaded(&$params) {
	
		$app = JFactory::getApplication();
		$option = $app->input->get('arkoption');
		$cattype = '';
        $cattypeid = '';
        $type_map = array();

    	if($app->isSite())
		{
			$id =  $app->input->get('a_id',0);
		}
		else
		{
			$id = $app->input->get('id',0);
		}
		
		if($option == 'com_content')
		{
			$typeid = 1;
			$type = 'com_content.article';
		}		
		elseif($option == 'com_modules')
		{
		    $typeTable = JTable::getInstance('Contenttype', 'JTable'); 
			$typeid = $typeTable->getTypeId('com_modules.custom');
			$type ='com_module.custom';
		}
		elseif($option == 'com_categories')
		{
			$typeid = 6;
			$type = 'com_content.category';	
		}
		else
		{
            $db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('context')
				->from('#__ark_editor_inline_views')
				->where('element = '.$db->quote($option));
			if($query instanceof JDatabaseQueryLimitable)
				$query->setLimit(1);
			$db->setQuery($query);	
            $context = $db->loadResult();
            $type = $option.'.'.$context;
			$cattype = $option.'.category';               
			
			if($context)
            {
			    $typeTable 	= JTable::getInstance('Contenttype', 'JTable'); 
			    $typeid 	= $typeTable->getTypeId($type);
			    $cattypeid 	= $typeTable->getTypeId($cattype);
            }
            else
            {     
                $typeid = '';
				$cattypeid = '';
            }
			
			
			$query->clear()
				->select('type_id,type_alias')
				->from('#__content_types')
				->where('type_alias like '.$db->quote($option.'%'));
			$db->setQuery($query);	
	        $objects = $db->loadObjectList();	
			
			if(!empty($objects))
			{	
				foreach($objects as $obj)
				{
					$type_map[str_replace($option.'.','',$obj->type_alias)] = $obj->type_id;	
				}	
			}
		}
		 
        $temp = JComponentHelper::getParams('com_arkeditor');
		$params->merge($this->params); //merge with plugin parameters
	    $temp->merge($params); //merge with editor parameters

        return
	    " 	
		    editor.on( 'configLoaded', function()
		    {
			    editor.config.enableModuleHistory = ". (int) $temp->get('enable_modulehistory',1) .";
				editor.config.typeAlias = '".$type."'; 
				editor.config.categoryTypeAlias = '".$cattype."'; 
				editor.config.versionsTypeId = '".$typeid."'; 
				editor.config.versionsCategoryTypeId = '".$cattypeid."';
				
				editor.config.VersionsElement = '".$option."'; 
				editor.config.VersionsTypeMap = ".json_encode( $type_map , JSON_FORCE_OBJECT)."		
		    });	
			 
			editor.on('instanceReady', function()
			{
				var editable = this.editable();
				var versionsURL = 'index.php?option=com_contenthistory&view=history&layout=modal&tmpl=component&item_id=".$id."&type_id=".$typeid."&type_alias=".$type."&".JSession::getFormToken()."=1';
				editable.setCustomData('versionsURL',versionsURL);
			});
		";
	}
}