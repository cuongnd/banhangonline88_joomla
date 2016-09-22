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
 * @subpackage  ArkEditor.Magicline
 */
class PlgArkEditorMagicline extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) {
	 
        return "editor.on( 'configLoaded', function() 
				{
					//Ensure we load magicline plugin
					if(editor.elementMode == CKEDITOR.ELEMENT_MODE_INLINE && !(". (int) JComponentHelper::getParams('com_arkeditor')->get('enable_magicline',0) .")) 
						return;
					var removeRegex = new RegExp( '(?:^|,)(?:magicline)(?:,|$)', 'g' );
					if(this.config.removePlugins.indexOf('magicline') === 0)  
						this.config.removePlugins = this.config.removePlugins.replace( removeRegex, '' );
					else
						this.config.removePlugins = this.config.removePlugins.replace( removeRegex, ',' );
				});";
		
		
	}
		
	public function onInstanceLoaded(&$params) {}
}


			