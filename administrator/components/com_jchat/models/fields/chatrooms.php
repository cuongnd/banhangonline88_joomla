<?php
// namespace administrator\components\com_jchat\models\fields;
/**  
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */ 
defined ( '_JEXEC' ) or die ( 'Restricted access' );
if(!class_exists('JFormFieldList')) {
	require_once JPATH_SITE . '/libraries/joomla/form/fields/list.php';
}

/**  
 * Templates selector
 * 
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 */ 
class JFormFieldChatrooms extends JFormFieldList {
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'chatrooms';
	
	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput() {
		$attr = '';
	
		// Initialize some field attributes.
		$attr .= ! empty ( $this->class ) ? ' class="' . $this->class . '"' : '';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= ! empty ( $this->size ) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
	
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
	
		// Get the field options.
		$options = $this->getOptions ();
	
		return JHtml::_ ( 'select.genericlist', $options, $this->name, trim ( $attr ), 'value', 'text', $this->value, $this->id );
	}
	
	/**
	 * Displays a list of the available access view levels
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					->select('a.id AS value, a.name AS text')
					->from('#__jchat_rooms AS a')
					->order($db->quoteName('name') . ' ASC');
			
		// Get the options.
		$db->setQuery ( $query );
		$options = $db->loadObjectList ();
	
		// Check for a database error.
		if ($db->getErrorNum ()) {
			return array();
		}
	
		array_unshift ( $options, JHtml::_ ( 'select.option', '', JText::_ ( 'COM_JCHAT_NO_CHATROOMS' ) ) );
	
		return $options;
	}
}
