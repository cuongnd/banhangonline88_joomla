<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for icons.
 *
 * @since  3.2
 */
abstract class JHtmlModule
{
	/**
	 * Method to generate html code for groups of lists of links
	 *
	 * @param   array  $groupsOfLinks  Array of links
	 *
	 * @return  string
	 *
	 * @since   3.2
	 */
	public static function mod_toolbar()
	{
		jimport('joomla.application.module.helper');
		$module = JModuleHelper::getModule('mod_toolbar');
		echo JModuleHelper::renderModule($module);
	}

}
