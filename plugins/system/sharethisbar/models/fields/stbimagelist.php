<?php
/** 
 * @package ShareThisBar Plugin for Joomla! 3.x
 * @subpackage Form Field Stbimagelist
 * @version $Id: sharethisbar.php 3.7 2016-02-27 17:00:33Z Dusanka $
 * @author Dusanka Ilic
 * @copyright (C) 2016 - Dusanka Ilic, All rights reserved.
 * @authorEmail: gog27.mail@gmail.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html, see LICENSE.txt
**/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('filelist');

/**
 * Supports an HTML select list of images, which start with a word "spread",
 * from directory /sharethisbar/images
 *
 * @package     ShareThisBar
 * @since       3.5
 */
class JFormFieldStbImageList extends JFormFieldFileList
{

	/**
	 * The form field type.
	 */
	public $type = 'StbImageList';

	/**
	 * Method to get the list of images field options.
	 * Use the filter attribute to specify allowable file extensions.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Define the image file type filter.
		//$filter = '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$|\.jpeg$|\.psd$|\.eps$';
            
        // Izmena 27.2.16   
        // $filter = '^spread(.)*[\.png|\.gif|\.jpg|\.jpeg]$';
        $this->filter = '^spread(.)*[\.png|\.gif|\.jpg|\.jpeg]$';

        // Izmena 27.2.16 - stavljeno pod komentar. 
        // Set the form field element attribute for file type filter.
	    // $this->element->addAttribute('filter', $filter);
            
		// Get the field options.
		return parent::getOptions();
	}
}
