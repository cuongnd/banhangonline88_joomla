<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('fsj_core.lib.j25.layouthelper');

/**
 * Searchtools elements.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.2
 */
abstract class JHtmlSearchtools
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.2
	 */
	protected static $loaded = array();

	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $tip = '', $icon = null, $formName = 'adminForm')
	{
		$direction = strtolower($direction);
		$orderIcons = array('icon-arrow-up-3', 'icon-arrow-down-3');
		$index = (int) ($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		// Create an object to pass it to the layouts
		$data            = new stdClass;
		$data->order     = $order;
		$data->direction = $direction;
		$data->selected  = $selected;
		$data->task      = $task;
		$data->tip       = $tip;
		$data->title     = $title;
		$data->orderIcon = $orderIcons[$index];
		$data->icon      = $icon;
		$data->formName  = $formName;

		return JLayoutHelper::render('searchtools.grid.sort', $data, JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'html'.DS.'joomla25');
	}
	
	public static function tooltipText($title = '', $content = '', $translate = 1, $escape = 1)
	{
		// Return empty in no title or content is given.
		if ($title == '' && $content == '')
		{
			return '';
		}

		// Split title into title and content if the title contains '::' (old Mootools format).
		if ($content == '' && !(strpos($title, '::') === false))
		{
			list($title, $content) = explode('::', $title, 2);
		}

		// Pass texts through the JText.
		if ($translate)
		{
			$title = JText::_($title);
			$content = JText::_($content);
		}

		// Escape the texts.
		if ($escape)
		{
			$title = str_replace('"', '&quot;', $title);
			$content = str_replace('"', '&quot;', $content);
		}

		// Return only the content if no title is given.
		if ($title == '')
		{
			return $content;
		}

		// Return only the title if title and text are the same.
		if ($title == $content)
		{
			return '<strong>' . $title . '</strong>';
		}

		// Return the formated sting combining the title and  content.
		if ($content != '')
		{
			return '<strong>' . $title . '</strong><br />' . $content;
		}

		// Return only the title.
		return $title;
	}
}
