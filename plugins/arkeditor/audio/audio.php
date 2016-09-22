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
class PlgArkEditorAudio extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) 
	{
		
		return "
					CKEDITOR.once('defaultStylessetReady', function(evt)
					{
						if(CKEDITOR.stylesSet.registered.default)
						{	
							CKEDITOR.stylesSet.registered.default =
							CKEDITOR.stylesSet.registered.default.concat([
								
								{ name: 'Left Audio', type: 'widget', widget: 'audio', attributes: { 'class': 'audio_align_left' } },
								{ name: 'Right Audio', type: 'widget', widget: 'audio', attributes: { 'class': 'audio_align_right' } },
								{ name: 'Centered Audio', type: 'widget', widget: 'audio', attributes: { 'class': 'audio_align_center' } }
							]);
						}
					});
				";
	}
		
	public function onInstanceLoaded(&$params) {}
}
