<?php
/**
 * @version		$Id: cache.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Joomla! Page Cache Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.arktypography
 */
 
class plgSystemARKtypography extends JPlugin
{

	var $_cache = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */

     function onAfterRoute()
     {

		$app = JFactory::getApplication();
		
		if ($app->isAdmin()) {
			return;
		}
		
		if(!file_exists(JPATH_PLUGINS.'/editors/arkeditor')) {
			return;
		}

		$doc = JFactory::getDocument();
		
		if($doc->getType() != 'html') {  //If not correct document type  exit
			return;
		}
						
		$data = $doc->getHeadData();
		$stylesheet = array();
		$url = JURI::base(true).'/index.php?option=com_ajax&plugin=arktypography&format=json';
		$stylesheet[$url]['mime'] = 'text/css';
		$stylesheet[$url]['media'] = null;
		$stylesheet[$url]['attribs'] = array();
		$data['styleSheets'] = $stylesheet + $data['styleSheets'];
		$doc->setHeadData($data);
	}
}