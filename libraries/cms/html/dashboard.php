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
abstract class JHtmlDashboard
{
	public function renderDashboard(){
		$input=JFactory::getApplication()->input;
		$input->set('tmpl','dashboard');
	}
}
