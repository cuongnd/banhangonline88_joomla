<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Helper class for parsing content through the content plugin system
 */
class FSJ_JContentPlugin
{
	static function Process($intext, $context)
	{
		$i = FSJ_Settings::$base_item;
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$art = new stdClass;
		$art->text = $intext;
		
		$params = JFactory::getApplication()->getParams(JRequest::getVar('option'));

		$results = $dispatcher->trigger('onContentPrepare', array ($context, &$art, &$params, 0));
		$results = $dispatcher->trigger('onContentBeforeDisplay', array ($context, &$art, &$params, 0));

		FSJ_Settings::$base_item = $i;

		return $art->text;
	}	
}