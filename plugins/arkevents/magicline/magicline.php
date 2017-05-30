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
 * @subpackage  ArkEvents.Magicline
 */

class PlgArKEventsMagicline extends JPlugin
{
	public function onInstanceCreated(&$params)
	{
		return "editor.on( 'configLoaded', function() 
				{
					//load in remove plugins
					if(	this.config.removePlugins)
						this.config.removePlugins += ',magicline';
					else
						this.config.removePlugins = 'magicline';
			
				});";
	}
}