<?php
//namespace components\com_jchat\models\fields;
/**  
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */ 
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Form Field for module status
 * @package JCHAT::components::com_jchat
 * @subpackage models 
 * @subpackage fields
 * @since 2.4
 */
class JFormFieldLanguageTranslationCheck extends JFormField {
	/**
	 * The form field type.
	 *
	 * @var string
	 * @since 11.1
	 */
	protected $type = 'LanguageTranslationCheck';
	
	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return string The field input markup.
	 *        
	 * @since 11.1
	 */
	protected function getInput() {
		// Initialize variables.
		$html = array ();
		
		
		// Initialize some field attributes.
		if(version_compare(PHP_VERSION, '5.4', '>=')) {
			$html [] = 	'<span data-content="' . JText::_ ( 'COM_JCHAT_LANGUAGE_TRANSLATION_SUPPORTED_DESC') . 
						'" class="label label-success label-large hasPopover">' . '<span class="icon-checkmark"></span>' . 
						JText::sprintf ( 'COM_JCHAT_LANGUAGE_TRANSLATION_SUPPORTED' ) . '</span>';
		} else {
			$html [] = 	'<span data-content="' . JText::_ ( 'COM_JCHAT_LANGUAGE_TRANSLATION_NOTSUPPORTED_DESC' ) . 
						'" class="label label-important label-large hasPopover">' . '<span class="icon-remove"></span>' . 
						JText::sprintf ( 'COM_JCHAT_LANGUAGE_TRANSLATION_NOTSUPPORTED' ) . '</span>';
		}
		
		return implode ( $html );
	}
}
