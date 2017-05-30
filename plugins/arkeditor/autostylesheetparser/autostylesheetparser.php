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
 *Ark  Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.Treelink
 */
class PlgArkEditorAutoStylesheetParser extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) 
	{
		$useTemplateCSS = $params->get('usetemplatecss',1);
		
		return "
					editor.on( 'configLoaded', function() {
						editor.config.useTemplateCSS = ". (int) $useTemplateCSS.";
						if(!editor.config.useTemplateCSS && CKEDITOR.stylesSet.registered.default)
							 CKEDITOR.stylesSet.registered.default = [];
					});
					
					CKEDITOR.once('defaultStylessetReady', function(evt)
					{
						if(!editor.config.useTemplateCSS && CKEDITOR.stylesSet.registered.default)
						{	
							CKEDITOR.stylesSet.registered.default = [];
						}
					});
				";
	}
		
	public function onInstanceLoaded(&$params) {}
}
