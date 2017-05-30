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
 *Ark Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.SaveContent
 */
class PlgArkEditorSaveContent extends JPlugin
{
	
	private $allowed_contexts = array('com_content','com_categories','com_modules');
	
	public function onBeforeInstanceLoaded(&$params){
	
		$app = JFactory::getApplication();
		$option = $app->input->get('option');
		$isAdmin = $app->isAdmin();
		$saveContext = '';	
		
		if(!in_array($option,$this->allowed_contexts))
			$option = '';
		
		
		switch($option)
		{
			case "com_content":
				$saveContext = ($isAdmin ? 'article.apply' : 'article.save');
				break;
			case "com_categories":
				$saveContext = ($isAdmin ? 'category.apply' : 'category.save');
				break;
			case "com_modules":
				$saveContext = ($isAdmin ? 'module.apply' : 'module.save');
				break;	
		}	
					
	
		return "
			 
				editor.on( 'configLoaded', function()
				{
					editor.config.saveContext = '".$option."'; 
				});	
				 
				 
				editor.on('instanceReady', function()
				{
					var editable = this.editable();
					var saveCmd = '".$saveContext."' ;
					editable.setCustomData('saveCmd',saveCmd); 
				 });
			";
	}
	
	public function onInstanceLoaded(&$params) {}
}
