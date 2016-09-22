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
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
class PlgSystemModuleHistory extends JPlugin
{

	public $app;

	public function onAfterInitialise()
	{
		//Inline editing is only enabled for frontend editing	
	
         $user = JFactory::getUser();
                
        //if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}
		
		if(!JComponentHelper::isInstalled('com_arkeditor'))
		{
			return;
		}
	    
		
		$params = JComponentHelper::getParams('com_arkeditor');
		
		if(!$params->get('enable_modulehistory',true))
		{
			return;
		}	
		
		jimport('legacy.table.module');
  		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'JTableModule', array('typeAlias' => 'com_modules.custom'));
		$component = JComponentHelper::getComponent('com_modules');
		$component->params->set('save_history',$component->params->get('save_history', 1));
	}
}
