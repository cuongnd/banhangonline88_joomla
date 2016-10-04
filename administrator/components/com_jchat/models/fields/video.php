<?php
//namespace administrator\components\com_jchat\models\fields;
/**  
 * @package JCHAT::components::com_jchat::administrator
 * @subpackage framework
 * @subpackage html
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */ 
defined('JPATH_PLATFORM') or die;

/**  
 * @package JCHAT::components::com_jchat::administrator
 * @subpackage framework
 * @subpackage html
 * @since 1.6
 */ 
class JFormFieldVideo extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since 1.6
	 */
	protected $type = 'Video';

	/**
	 * Method to get the field markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$width = $this->element['width'] ? (int) $this->element['width']  : '420';
		$height = $this->element['height'] ? (int) $this->element['height']  : '315';
		$src = $this->element['src'];
		$class = $this->element['class'];
		
		return '<iframe class="'.$class.'" width="'.$width.'" height="'.$height.'" src="'.$src.'" frameborder="0" allowfullscreen></iframe>';
	}
}
