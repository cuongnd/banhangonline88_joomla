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
 * @subpackage  ArkEditor.AutoSave
 */
class PlgArkEditorAutoSave extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params)
	{
		$temp = JComponentHelper::getParams('com_arkeditor');
		$params->merge($temp);
		
		return " 	
				editor.on( 'configLoaded', function()
				{
					editor.config.enableInlineAutoSave = ". (int) $params->get('enable_autosave',1) .";
				});";	
	}
		
	public function onInstanceLoaded(&$params) {}
}
