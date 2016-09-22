<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark Typography Ajax Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Ajax.arktemplates
 */

  error_reporting(E_ERROR & ~E_NOTICE);
 
class PlgAjaxArkTemplates extends JPlugin
{
	
    public function onAjaxArkTemplates()
	{
        $templatesXML = @file_get_contents(JPATH_PLUGINS.'/arkeditor/xmltemplates/xmltemplates/templates/default.xml');	
		
		// Enable caching
		header('Cache-Control: public'); 

		// Expire in one day
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT'); 

		// Set the correct MIME type, because Apache won't set it for us
		header("Content-type: text/xml");

		// Write everything out
		echo $templatesXML;
		exit;
	}	
}