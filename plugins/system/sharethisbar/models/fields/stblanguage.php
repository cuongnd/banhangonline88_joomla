<?php
/** 
 * @package ShareThisBar Plugin for Joomla! 2.5
 * @subpackage Form Field Stblanguage 
 * @version $Id: sharethisbar.php 3.5 2012-12-29 17:00:33Z Dusanka $
 * @author Dusanka Ilic
 * @copyright (C) 2012 - Dusanka Ilic, All rights reserved.
 * @authorEmail: gog27.mail@gmail.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html, see LICENSE.txt
**/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Supports a list of installed languages for the ShareThisBar plugin.
 *
 * @package     ShareThisBar
 * @since       3.5
 */
class JFormFieldStblanguage extends JFormFieldList
{
	/**
	 * The form field type.
	 */
	protected $type = 'Stblanguage';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{

                $langBasepath = JPATH_PLUGINS . '/' . 'system' . '/' . 'sharethisbar';
            
		// Merge any additional options in the XML definition.
                // poslednji parametar je true ako želiš samo instalirane jezike na nivou sistema.
		$options = array_merge(
			parent::getOptions(),
			JLanguageHelper::createLanguageList($this->value, $langBasepath, true, false)
		);

		return $options;
	}
}
