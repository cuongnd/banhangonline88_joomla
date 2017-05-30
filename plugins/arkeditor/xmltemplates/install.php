<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @package		ARK
 * @subpackage	arkeditor
 * @since		1.0.1
 */
jimport('joomla.filesystem.file');
 
class plgArkeditorXmlTemplatesInstallerScript
{
	/**
	 * Pre-flight extension installer method.
	 *
	 * This method runs before all other installation code.
	 *
	 * @param	$type
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.0.1
	 */
	 
	function preflight($type, $parent)
	{ 
		$path = JPATH_PLUGINS.'/arkeditor/xmltemplates/xmltemplates/templates/default.xml';
		
		if(JFile::exists($path))
		{	
			JFIle::copy($path,$path.'.old',null);
		}
	} 
	
	
	/**
	 * Post-flight extension installer method.
	 *
	 * This method runs after all other installation code.
	 *
	 * @param	$type
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.0.1
	*/
	
	function postflight($type, $parent)
	{
		$src = JPATH_PLUGINS.'/arkeditor/xmltemplates/xmltemplates/templates/default.xml.old';
		$dest = JPATH_PLUGINS.'/arkeditor/xmltemplates/xmltemplates/templates/default.xml';
		
		if(JFile::exists($src))
		{	
			JFIle::copy($src,$dest,null);
		}
	}
}