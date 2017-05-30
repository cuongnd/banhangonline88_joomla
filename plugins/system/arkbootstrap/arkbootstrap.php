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
 * ArkBootstrap Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.arkbootstrap
 */
 
class plgSystemArKBootstrap extends JPlugin
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

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__extensions')
			->where('type =' . $db->Quote('component'))
			->where('element ='	.$db->Quote('com_arkeditor'));
	
		$result = $db->setQuery($query)->loadResult();	
		
		if(is_string($result)) //always must do this check
		$params = @ new JRegistry($result);
		else
			return;
				
		if(!$params->get('loadbootstrap',true))
		{
			return;
		}
		
		$doc = JFactory::getDocument();
		
		if($doc->getType() != 'html') {  //If not correct document type  exit
			return;
		}

		$data = $doc->getHeadData();
		$stylesheet = array();
		$url = JURI::base(true).'/index.php?option=com_ajax&plugin=arkbootstrap&format=json';
		$stylesheet[$url]['mime'] = 'text/css';
		$stylesheet[$url]['media'] = null;
		$stylesheet[$url]['attribs'] = array();
		$data['styleSheets'] = $stylesheet + $data['styleSheets'];
		$doc->setHeadData($data);
	}
}