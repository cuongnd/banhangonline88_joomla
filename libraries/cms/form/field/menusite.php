<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');
// Import the com_menus helper.
require_once realpath(JPATH_ROOT . '/components/com_menus/helpers/menus.php');
/**
 * Supports an HTML select list of menus
 *
 * @since  1.6
 */
class JFormFieldMenuSite extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'MenuSite';
	/**
	 * Method to get the list of menus for the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$menus = JHtml::_('menusite.menus');
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $menus);
		return $options;
	}
}
