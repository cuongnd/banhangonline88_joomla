<?php
/*------------------------------------------------------------------------
# Copyright (C) 2015-2016 WebxSolution Ltd. All Rights Reserved.
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
 * @subpackage  Ajax.arkbootstrap
 */

  error_reporting(E_ERROR & ~E_NOTICE);
 
class PlgAjaxArkBootstrap extends JPlugin
{

	public $db;
	
    public function onAjaxArkBootstrap()
	{

   
		$bootstrap = @file_get_contents(JPATH_PLUGINS.'/ajax/arkbootstrap/bootstrap.css');	
		$contentCSS = $bootstrap; 
	
		// Remove comments
		$contentCSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contentCSS);

		// Remove space after colons
		$contentCSS = str_replace(': ', ':', $contentCSS);

		// Remove whitespace
		$contentCSS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contentCSS);

		// Enable GZip encoding.

		if(JFactory::getConfig()->get('gzip',false))
		{
			if(!ini_get('zlib.output_compression') && ini_get('output_handler')!='ob_gzhandler') //if server is configured to do this then leave it the server to do it's stuff
			ob_start("ob_gzhandler");
		}

		// Enable caching
		header('Cache-Control: public'); 

		// Expire in one day
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT'); 

		// Set the correct MIME type, because Apache won't set it for us
		header("Content-type: text/css");

		// Write everything out
		echo $contentCSS;
		exit;
	}	
}