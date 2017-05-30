<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Field to load a list of available users statuses
 *
 * @since  3.2
 */
class JFormFieldComponentList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.2
	 */
	protected $type = 'ComponentList';

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $elements = array();

	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.2
	 */
	
	public function setup( SimpleXMLElement $element, $value, $group = null )
	{
		if(!isset($element['multiple']))
			$element->addAttribute('multiple', 'true');

		if(empty($value))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('element')
				->from('#__extensions e')
				->where($db->quoteName('type').' = '. $db->quote('component'))
				->where('EXISTS(SELECT 1 FROM #__menu WHERE link LIKE CONCAT(CONCAT( \'index.php?option=\', e.element),\'%\') AND client_id = 0)'  ) 
				->order('element ASC');
			$db->setQuery($query);	
			$elements = $db->loadColumn();	
			
			if(!empty($elements))
			{	
				$value = array();
				
				foreach ($elements as $elem)
				{
					$value[] = $elem;
				}	
			}
		}

		return parent::setup( $element, $value, $group );
	}//end function
	
		
	
	protected function getOptions()
	{
		$options = array();
	
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('element AS value, element AS text')
			->from('#__extensions e')
			->where($db->quoteName('type').' = '. $db->quote('component'))
			->where('EXISTS(SELECT 1 FROM #__menu WHERE link LIKE  CONCAT(CONCAT( \'index.php?option=\', e.element),\'%\') AND client_id = 0)'  )  
			->order('element ASC');
		$db->setQuery($query);	
		$items = $db->loadObjectList();	
		
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$item->text =  str_replace('com_','',$item->text);  
				$options[]	= JHtml::_('select.option', $item->value, JText::_($item->text));
			}
		}
		$options = array_merge(parent::getOptions(), $options);		
		
		return $options;
	}
}
