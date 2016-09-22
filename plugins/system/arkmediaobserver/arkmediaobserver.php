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
class PlgSystemArkmediaObserver extends JPlugin
{

	public $app;

	public function onAfterInitialise()
	{
		//Inline editing is only enabled for frontend editing	
    	if($this->app->isSite())
			return; 
	
		if(!JComponentHelper::isInstalled('com_arkeditor'))
			return;
		
	    if(!JComponentHelper::isInstalled('com_arkmedia'))
			return;
		
		//Map table observers
		require_once JPATH_PLUGINS.'/system/arkmediaobserver/observer/extension.php';
		JObserverMapper::addObserverClassToClass('JTableObserverARKExtension', 'JTableExtension');
	}
}
