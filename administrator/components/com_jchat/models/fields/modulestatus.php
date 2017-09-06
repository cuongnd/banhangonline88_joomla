<?php
//namespace components\com_jchat\models\fields;
/**  
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */ 
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Form Field for module status
 * @package JCHAT::components::com_jchat
 * @subpackage models 
 * @subpackage fields
 * @since 1.0
 */
class JFormFieldModuleStatus extends JFormField {
	/**
	 * The form field type.
	 *
	 * @var string
	 * @since 11.1
	 */
	protected $type = 'ModuleStatus';
	
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
		
		// Retrieve status informations about the chat module
		$db = JFactory::getDbo();
		$queryModuleStatus = "SELECT id, published, position" .
							 "\n FROM #__modules" .
							 "\n WHERE " . $db->quoteName('module') . "=" . $db->quote('mod_jchat') .
							 "\n AND " . $db->quoteName('published') . ">= 0" ;
		$db->setQuery($queryModuleStatus);
		$publishedModule = $db->loadObject();
		if (is_object($publishedModule)) {
			$isModulePublished = $publishedModule->published && ($publishedModule->position != '');
		}
		
		// Initialize some field attributes.
		
		if ($isModulePublished) {
			$html [] = 	'<a target="_blank" href="index.php?option=com_modules&amp;task=module.edit&amp;id=' . $publishedModule->id . '">' .
						'<span data-content="' . JText::sprintf ( 'COM_JCHAT_MODULE_ENABLED_DESC', $publishedModule->position) . 
						'" class="label label-success label-large hasPopover">' . '<span class="icon-checkmark"></span>' . 
						JText::sprintf ( 'COM_JCHAT_MODULE_ENABLED' ) . '</span></a>';
		} else {
			$html [] = 	'<a target="_blank" href="index.php?option=com_modules&amp;task=module.edit&amp;id=' . $publishedModule->id . '">' .
						'<span data-content="' . JText::_ ( 'COM_JCHAT_MODULE_DISABLED_DESC' ) . 
						'" class="label label-important label-large hasPopover">' . '<span class="icon-remove"></span>' . 
						JText::sprintf ( 'COM_JCHAT_MODULE_DISABLED' ) . '</span></a>';
		}
		
		return implode ( $html );
	}
}
