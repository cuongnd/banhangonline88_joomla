<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * HTML helper class for rendering telephone numbers.
 *
 * @since  1.6
 */
abstract class JHtmlMedia
{
	/**
	 * Converts strings of integers into more readable telephone format
	 *
	 * By default, the ITU-T format will automatically be used.
	 * However, one of the allowed unit types may also be used instead.
	 *
	 * @param   integer  $number       The integers in a phone number with dot separated country code
	 *                                 ccc.nnnnnnn where ccc represents country code and nnn represents the local number.
	 * @param   string   $displayplan  The numbering plan used to display the numbers.
	 *
	 * @return  string  The formatted telephone number.
	 *
	 * @see     JFormRuleTel
	 * @since   1.6
	 */
	public static function image($name, $value)
	{
		require_once JPATH_ROOT . '/libraries/cms/form/field/media.php';
		$media_field = new JFormFieldMedia();
		$media_string = <<<XML

<field name="$name"  type="media" default="1" label="">
</field>

XML;

		$element_media = simplexml_load_string($media_string);
		$media_field->setup($element_media, $value, '');
		return $media_field->renderField();
	}

}
