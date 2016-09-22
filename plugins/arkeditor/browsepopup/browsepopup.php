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
 * @subpackage  ArkEditor.BrowseBrowser
 */
class PlgArkEditorBrowsePopup extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) 
	{
		$enabled = (JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_arkmedia') &&  JPluginHelper::isEnabled('arkeditor','arkmedia'));

		if($enabled)
			return " 	
			editor.on( 'configLoaded', function()
			{
				if(	this.config.removePlugins)
					this.config.removePlugins += ',browsebrowser';
				else
					this.config.removePlugins = 'browsebrowser';
			});";	
	}
		
	public function onInstanceLoaded(&$params) {}
}
