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
 *Ark inline ArkEditor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEvents.mobile
 */

class PlgArKEventsMobile extends JPlugin
{
	public function onBeforeLoadToolbar(&$params)
	{
			$agent = $_SERVER['HTTP_USER_AGENT'];
		
			if(preg_match('/ipad/i',$agent)) //if iPad keep full toolbar
				return;
			
			if(preg_match('/(mobi|iphone|ipod|android)/i',$agent) )
			{
					$params->set('toolbar','mobile');
					$params->set('toolbar_ft','mobile');
			}	
	
		return  null;	
	}
}
